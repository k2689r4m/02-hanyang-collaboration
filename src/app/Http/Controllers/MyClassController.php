<?php

namespace App\Http\Controllers;

use App\Models\ClassList;
use App\Models\ClassObject;
use App\Models\MyClass;
use App\Models\MyPage;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Card;
use App\Models\Calendar;
use App\Models\ActLog;
use App\Models\Expert;
use App\Models\TeamMaster;
use App\Models\User;
use App\Models\ClassManager;
use http\Env\Response;
use http\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\assertDirectoryDoesNotExist;
use App\Events\CardEvent;
use App\Events\CalendarEvent;
use App\Events\ActLogEvent;

class MyClassController extends Controller
{
    //
    public function myClassView (Request $request) {
        $myPage = MyPage::where('userId', Auth::id())->first();

        if (!$myPage) {
            $myPage = MyPage::create([
                'userId' => Auth::id(),
            ]);
//            return redirect()->back();
        }

        $COMMON_USER = 1;
        $PROFESSOR = 2;

        $user = Auth::user();
        $myClasses = null;
        if (!$user) {
            //middleware가 처리함
            return redirect()->back();
        }
        elseif ($user->authority == $COMMON_USER) {
            $myClasses = ClassList::where('userId', $user->id)->with('class_object')->with('class_apply')->get()->filter(function ($item) {
                return $item->class_apply;
            });

            if(!$myClasses->first()){
                return redirect()->back()->withErrors(['notMyClasses' => '참여중인 학습플로우가 없습니다.']);
            }
        }
        elseif ($user->authority == $PROFESSOR) {
            $myClasses = MyClass::where('userId', $user->id)->with('class_object')->get();
            if(!$myClasses->first()){
                return redirect()->back()->withErrors(['notMyClasses' => '참여중인 학습플로우가 없습니다.']);
            }
        }
        elseif ($user->authority == 5) {
            $classObjectIds = Expert::where('userId', Auth::id())->get()->map(function ($item) {
                return $item->classObjectId;
            });
            $myClasses = MyClass::whereIn('classObjectId', $classObjectIds)->with('class_object')->get();
            if(!$myClasses->first()){
                return redirect()->back()->withErrors(['notMyClasses' => '참여중인 학습플로우가 없습니다.']);
            }
        }
        elseif ($user->authority == 6) {
            $classObjectIds = ClassManager::where('userId', Auth::id())->get()->map(function ($item) {
                return $item->classObjectId;
            });
            $myClasses = MyClass::whereIn('classObjectId', $classObjectIds)->with('class_object')->get();
            if(!$myClasses->first()){
                return redirect()->back()->withErrors(['notMyClasses' => '참여중인 학습플로우가 없습니다.']);
            }
        }
        else {
            return redirect()->back()->withErrors(['notAuthority' => '찾을 수 없는 등급']);
//            return route('/myPageView');
        }

        return view('myClass.my_class_view',
            [
                'myPage' => $myPage,
                'myClasses' => $myClasses,
                'reClassId' => $request->myClassId ?? 0,
            ]);

//        return view('myClass.my_class_view',
//            [
//                'myPage' => $myPage,
//                'myClassObjects' => $myClassObjects,
//            ]);
    }

    public function classList (Request $request, $year, $semester) {
        $validator = Validator::make([
            'year' => $year,
            'semester' => $semester
        ], [
            'year' => ['required', 'integer', 'min:2000', 'max:9999'],
            'semester' => ['required', 'integer', 'min:1', 'max:4']
        ]);

        if ($validator->fails()) {
            return [];
        }

        $classObjects = [];

        switch (Auth::user()->authority) {
            case 1:
                $classObjects = ClassList::where('userId', Auth::id())->with(['class_object' => function ($query) use($year, $semester) {
                    $query->where('suupYear', $year)->where('suupTerm', $semester * 5 + 5);
                }])->get()->filter(function ($class) {
                    return $class->class_object && $class->class_object->class_apply;
                })->sortBy(function ($class) {
                    return $class->class_object->class_apply->id;
                });
                break;
            case 2:
                $classObjects = MyCLass::where('userId', Auth::id())->with(['class_object' => function ($query) use($year, $semester) {
                    $query->where('suupYear', $year)->where('suupTerm', $semester * 5 + 5);
                }])->get()->filter(function ($class) {
                    return $class->class_object && $class->class_object->class_apply;
                })->sortBy(function ($class) {
                    return $class->class_object->class_apply->id;
                });
                break;
            case 5:
                $classObjects = Expert::where('userId', Auth::id())->with(['my_class' => function ($query) use($year, $semester) {
                    $query->with(['user2' => function ($query) {
                        $query->select('id', 'name');
                    }])->with(['class_object' => function ($query) use($year, $semester) {
                        $query->where('suupYear', $year)->where('suupTerm', $semester * 5 + 5);
                    }]);
                }])->get()->filter(function ($item) {
                    return $item->my_class && $item->my_class->class_object;
                })->map(function ($item) {
                    return $item->my_class;
                });
                break;
            case 6:
                $classObjects = ClassManager::where('userId', Auth::id())->with(['my_class' => function ($query) use($year, $semester) {
                    $query->with(['class_object' => function ($query) use($year, $semester) {
                        $query->where('suupYear', $year)->where('suupTerm', $semester * 5 + 5);
                    }]);
                }])->get()->filter(function ($item) {
                    return $item->my_class && $item->my_class->class_object;
                })->map(function ($item) {
                    return $item->my_class;
                });
                break;
            case 3:
            case 4:
            default:
        }

        return $classObjects;
    }

    public function fetch (Request $request) {
        $myClass = MyClass::find($request['id']);
        if (!$myClass) {
            return ['fail' => 'invalid parameter'];
        }

        if (!$myClass->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        return [$myClass];
    }

    public function classSetting (Request $request) {
//        return dd($request->all());

        /**
         *
         * if type: setting
         * id: classObjectId
         * type: setting
         *
         */

        $validator = Validator::make($request->all(), [
            'classObjectId' => ['required', 'integer', 'min:1'],
//            'experts' => ['required', 'json'],
//            'teamMasters' => ['required', 'json'],
//            'classManagers' => ['required', 'json'],
            'id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return false;
        }
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'userId' => ['required', 'integer', 'min:1'],
            'classObjectId' => ['required', 'integer', 'min:1'],
            'onClassTalk' => ['required', 'boolean'],
            'onTeamTalk' => ['required', 'boolean'],
            'onOrientation' => ['required', 'boolean'],
            'onReflectionLog' => ['required', 'boolean'],
            'onEvaluation' => ['required', 'boolean'],
            'onTeamActivity' => ['required', 'boolean'],
            'onProblemAnalysis' => ['required', 'boolean'],
            'onTeamAccess' => ['required', 'boolean'],
            'onTeamOrientation' => ['required', 'boolean'],
            'onSetting' => ['required', 'boolean'],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('id')) {
                return ['fail' => 'validate error: id'];
            }
            else if ($errors->has('userId')) {
                return ['fail' => 'validate error: userId'];
            }
            else if ($errors->has('classObjectId')) {
                return ['fail' => 'validate error: classObjectId'];
            }
            else if ($errors->has('version')) {
                return ['fail' => 'validate error: version'];
            }
            else if ($errors->has('cardsNum')) {
                return ['fail' => 'validate error: cardsNum'];
            }
            else if ($errors->has('onClassTalk')) {
                return ['fail' => 'validate error: onClassTalk'];
            }
            else if ($errors->has('onTeamTalk')) {
                return ['fail' => 'onTeamTalk'];
            }
            else if ($errors->has('onOrientation')) {
                return ['fail' => 'validate error: onOrientation'];
            }
            else if ($errors->has('onReflectionLog')) {
                return ['fail' => 'validate error: onReflectionLog'];
            }
            else if ($errors->has('onEvaluation')) {
                return ['fail' => 'validate error: onEvaluation'];
            }
            else if ($errors->has('onTeamActivity')) {
                return ['fail' => 'validate error: onTeamActivity'];
            }
            else if ($errors->has('onProblemAnalysis')) {
                return ['fail' => 'validate error: onProblemAnalysis'];
            }
            else if ($errors->has('onProblemAnalysis')) {
                return ['fail' => 'validate error: onProblemAnalysis'];
            }
            else if ($errors->has('onTeamAccess')) {
                return ['fail' => 'validate error: onTeamAccess'];
            }
            else if ($errors->has('onTeamOrientation')) {
                return ['fail' => 'validate error: onTeamOrientation'];
            }
            else if ($errors->has('onSetting')) {
                return ['fail' => 'validate error: onSetting'];
            }
        }


        $classObject = ClassObject::find($request['classObjectId']);
        if (!$classObject) {
            return ['fail' => 'classObjectId'];
        }
        $classObjectId = $classObject->id;
        $myClass = MyClass::find($request['id']);
        if (!$myClass) {
            return ['fail' => 'id'];
        }

//        $experts = (array)json_decode($request['experts']);
//        $experts = collect($experts)->filter(function ($item) use($classObjectId) {
//            $validator = Validator::make(['userId' => $item->id, 'write' => $item->write], [
//                'userId' => ['required', 'integer', 'min:1'],
//                'write' => ['required', 'boolean'],
//            ]);
//
//            if ($validator->fails()) {
//                return false;
//            }
//
//            $user = User::find($item->id);
//            if (!$user) {
//                return false;
//            }
//            if ($user->authority != 5) {
//                return false;
//            }
//
//            $expert = Expert::where('classObjectId', $classObjectId)->where('userId', $item->id)->first();
//            if ($expert) {
//                return false;
//            }
//
//            return true;
//        });
//
//        $experts->each(function ($item) use($classObjectId) {
//            Expert::create([
//               'userId' => $item->id,
//               'classObjectId' => $classObjectId,
//                'write' => $item->write,
//            ]);
//        });
//
//        $managers = (array)json_decode($request['classManagers']);
//        $managers = collect($managers)->filter(function ($item) use($classObjectId) {
//            $validator = Validator::make(['userId' => $item->id, 'write' => $item->write], [
//                'userId' => ['required', 'integer', 'min:1'],
//                'write' => ['required', 'boolean'],
//            ]);
//
//            if ($validator->fails()) {
//                return false;
//            }
//
//            $user = User::find($item->id);
//            if (!$user) {
//                return false;
//            }
//            if ($user->authority != 6) {
//                return false;
//            }
//
//            $manager = ClassManager::where('classObjectId', $classObjectId)->where('userId', $item->id)->first();
//            if ($manager) {
//                return false;
//            }
//
//            return true;
//        });
//
//        $managers->each(function ($item) use($classObjectId) {
//            ClassManager::create([
//                'userId' => $item->id,
//                'classObjectId' => $classObjectId,
//                'write' => $item->write,
//            ]);
//        });
//
//        $teamMasters = (array)json_decode($request['teamMasters']);
//        $teamMasters = collect($teamMasters)->filter(function ($item) use($classObjectId) {
//            $validator = Validator::make(['userId' => $item->id, 'write' => $item->write], [
//                'userId' => ['required', 'integer', 'min:1'],
//                'write' => ['required', 'boolean'],
//            ]);
//
//            if ($validator->fails()) {
//                return false;
//            }
//
//            $user = User::find($item->id);
//            if (!$user) {
//                return false;
//            }
//            if ($user->authority != 1) {
//                return false;
//            }
//
//            $teamMaster = TeamMaster::where('classObjectId', $classObjectId)->where('userId', $item->id)->first();
//            if ($teamMaster) {
//                return false;
//            }
//
//            return true;
//        });
//
//        $teamMasters->each(function ($item) use($classObjectId) {
//            TeamMaster::create([
//                'userId' => $item->id,
//                'classObjectId' => $classObjectId,
//                'write' => $item->write,
//            ]);
//        });

//        Expert::upsert([
//            $experts
//        ], ['userId', 'classObjectId'], ['write']);



        $data = ['fail' => 'unknown error'];

        if ($myClass->userId != Auth::id()) {
            return ['fail' => 'permission denied'];
        }

        if ($myClass->classObjectId != $request['classObjectId']) {
            return ['fail' => 'classObjectId'];
        }

        if ($myClass->onSetting) {
            return ['fail' => 'already changed'];
        }

        $teams = $teams = $myClass->teams();

        $classCards = [];
        $teamCards = [];
        foreach (range(1, 20) as $key) {
            $teamCards[] = [];
        }
        $existClassData = false;
        $existTeamData = false;

        /////////////////////////////////////////////////////////////
        if ($request['onClassTalk'] == '1' && !$myClass->onClassTalk) {
            if ($myClass->onClassTalk == 0) {
                $classCards[] = new Card([
                    'title' => '수업공지',
                    'userId' => Auth::id(),
                    'type' => 5
                ]);
            }
        }
        $myClass->onClassTalk = $request['onClassTalk'];
        if ($request['onTeamTalk'] == '1' && !$myClass->onTeamTalk) {
            $existTeamData = true;
            foreach ($teams as $key=>$team) {
                if ($team->onTeamTalk == 0) {
                    $teamCards[$key][] = new Card([
                        'title' => '팀톡',
                        'userId' => Auth::id(),
                        'type' => 12,
                        'classObjectId' => $myClass->classObjectId
                    ]);
                    $team->onTeamTalk = $request['onTeamTalk'];
                }
            }
        }
        $myClass->onTeamTalk = $request['onTeamTalk'];
        if ($request['onTeamOrientation'] == '1' && !$myClass->onTeamOrientation) {
            $existTeamData = true;
            foreach ($teams as $key=>$team) {
                $teamCards[$key][] = new Card([
                    'title' => '팀 오리엔테이션',
                    'userId' => Auth::id(),
                    'type' => 11,
                    'classObjectId' => $myClass->classObjectId
                ]);
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 11)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '팀 오리엔테이션',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'teamId' => $team->id,
//                        'type' => 11
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
            }
        }
        $myClass->onTeamOrientation = $request['onTeamOrientation'];
        if ($request['onOrientation'] == '1' && !$myClass->onOrientation) {
            $classCards[] = new Card([
                'title' => '오리엔테이션',
                'userId' => Auth::id(),
                'type' => 6
            ]);
        }
        $myClass->onOrientation = $request['onOrientation'];
        if ($request['onProblemAnalysis'] == '1' && !$myClass->onProblemAnalysis) {
            $classCards[] = new Card([
                'title' => '문제분석',
                'userId' => Auth::id(),
                'type' => 10
            ]);

            $existTeamData = true;
            foreach ($teams as $key=>$team) {
                $teamCards[$key][] = new Card([
                    'title' => '문제분석',
                    'userId' => Auth::id(),
                    'type' => 10,
                    'classObjectId' => $myClass->classObjectId
                ]);
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 10)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '문제분석',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 10,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
            }
        }
        $myClass->onProblemAnalysis = $request['onProblemAnalysis'];
        if ($request['onTeamActivity'] == '1' && !$myClass->onTeamActivity) {
            $classCards[] = new Card([
                'title' => '팀활동 보고서',
                'userId' => Auth::id(),
                'type' => 9,
            ]);

            $existTeamData = true;
            foreach ($teams as $key=>$team) {
                $teamCards[$key][] = new Card([
                    'title' => '팀활동 보고서',
                    'userId' => Auth::id(),
                    'type' => 9,
                    'classObjectId' => $myClass->classObjectId
                ]);
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 9)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '팀활동',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 9,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
            }
        }
        $myClass->onTeamActivity = $request['onTeamActivity'];
        if ($request['onEvaluation'] == '1' && !$myClass->onEvaluation) {
            $classCards[] = new Card([
                'title' => '평가',
                'userId' => Auth::id(),
                'type' => 8
            ]);

            $existTeamData = true;
            foreach ($teams as $key=>$team) {
                $teamCards[$key][] = new Card([
                    'title' => '평가',
                    'userId' => Auth::id(),
                    'type' => 8,
                    'classObjectId' => $myClass->classObjectId
                ]);
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 8)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '평가',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 8,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
            }
        }
        $myClass->onEvaluation = $request['onEvaluation'];
        if ($request['onReflectionLog'] == '1' && !$myClass->onReflectionLog) {
            $classCards[] = new Card([
                'title' => '성찰',
                'userId' => Auth::id(),
                'type' => 7
            ]);

//            $myClass->withCards()->save($card);
//            $myClass->cardsNum = array_merge([$card->id], $myClass->cardsNum ?? []);

            $existTeamData = true;
            foreach ($teams as $key=>$team) {
                $teamCards[$key][] = new Card([
                    'title' => '성찰',
                    'userId' => Auth::id(),
                    'type' => 7,
                    'classObjectId' => $myClass->classObjectId
                ]);
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 7)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '성찰',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 7,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
            }
        }
        $myClass->onReflectionLog = $request['onReflectionLog'];
        /////////////////////////////////////////////////////////////
        $myClass->onTeamAccess = $request['onTeamAccess'];
        $myClass->onSetting = true;
        $myClass->save();

        if (count($classCards) > 0) {
            $myClass->withCards()->saveMany($classCards);
            $ids = [];
            foreach ($classCards as $card) {
                $ids[] = $card->id;
            }

            $myClass->cardsNum = array_merge($ids, $myClass->cardsNum ?? []);
            $myClass->save();
        }
        if ($existTeamData) {
            foreach ($teams as $key=>$team) {
                if (count($teamCards[$key]) > 0) {
                    $team->withCards()->saveMany($teamCards[$key]);

                    $ids = [];
                    foreach ($teamCards[$key] as $card) {
                        $ids[] = $card->id;
                    }

                    $team->cardsNum = array_merge($ids, $team->cardsNum ?? []);
                    $team->save();
                }
            }
        }

        if (!($request['onClassTalk'] == '1' && !$myClass->onClassTalk) && count($classCards) > 0) {
            $cardsNum = is_null($myClass->cardsNum) ? [] : $myClass->cardsNum;
            $chat = Card::where('classObjectId', $myClass->classObjectId)->where('type', 5)->first();
            if ($chat) {
                $key = array_search($chat->id, $cardsNum);
                if (!is_bool($key)) {
                    array_splice($cardsNum, $key, 1);
                    array_unshift($cardsNum, $chat->id);
                    $myClass->cardsNum = $cardsNum;

                    $myClass->save();
                }
            }
        }

        if (!($request['onTeamTalk'] == '1' && !$myClass->onTeamTalk) && $existTeamData) {
            $teamChats = Card::where('classObjectId', $myClass->classObjectId)->where('type', 12)->get();
            foreach ($teamChats as $teamChat) {
                if (count($teamCards[$key]) > 0) {
                    $team = Team::find($teamChat->teamId);

                    $cardsNum = is_null($team->cardsNum) ? [] : $team->cardsNum;
                    $key = array_search($teamChat->id, $cardsNum);
                    if (!is_bool($key)) {
                        array_splice($cardsNum, $key, 1);
                        array_unshift($cardsNum, $teamChat->id);

                        $team->cardsNum = $cardsNum;

                        $team->save();
                    }
                }
            }
        }

        broadcast(
            new CardEvent($myClass, $myClass->classObjectId, 'MYCLASS', 'setting')
        )->toOthers();
        foreach ($teams as $team) {
            broadcast(
                new CardEvent($myClass, $team->id, 'MYTEAM', 'setting')
            )->toOthers();
        }

        return ['success' => true];
    }

    public function fetchClassMembers (Request $request, $classObjectId) {
        $myClass = MyClass::where('classObjectId', $classObjectId)->first();
        if (!$myClass) {
            return ['fail' => 'invalid class'];
        }

        if (!$myClass->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $teamIds = Team::where('classObjectId', $myClass->classObjectId)->get()->map(function ($item, $key) {
            return $item->id;
        });

        $members = $myClass->members();

        $members = $members->map(function ($member, $key) {
            $user = $member->user();
            if ($user) {
                return ['id' => $member->userId, 'name' => $user->name, 'email' => $user->email, 'team' => $member->team()->name ?? ''];
            }
        });

//        $_members = [];
//        $members = $members->filter(function ($value, $key) {
//            if (!is_null($value)) {
//                return true;
//            }
//        });

        $_members = [];
        foreach ($members as $member) {
            if (!is_null($member)) {
                $member['email'] = $this->asterisk($member['email']);
                $_members[] = $member;
            }
        }

        return $_members;
    }

    public function fetchTeamMembers (Request $request, $teamId) {
        $team = Team::find($teamId);
        if (!$team) {
            return ['fail' => 'invalid team'];
        }

        if (!$team->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $members = $team->teamMembers();

        $members = $members->map(function ($member, $key) use($team) {
            $user = $member->user();
            return ['id' => $member->userId, 'name' => $user->name, 'email' => $this->asterisk($user->email), 'team' => $team->name];
        });

        return $members;
    }

    public function fetchCalendar (Request $request, $classObjectId, $teamId, $year, $month) {
        $validator = Validator::make([
            'classObjectId' => $classObjectId,
            'teamId' => $teamId,
            'year' => $year,
            'month' => $month
        ], [
            'classObjectId' => ['required', 'integer', 'min:1'],
            'teamId' => ['required', 'integer', 'min:0'],
            'year' => ['required', 'integer', 'min:2000', 'max:9999'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('year')) {
                return ['fail' => 'invalid year'];
            }
            else if ($errors->has('month')) {
                return ['fail' => 'invalid month'];
            }
        }

        if ($validator->fails()) {
            return ['fail' => 'invalid date'];
        }

        $startDate = date('Y-m-d', strtotime("$year-$month-1"));
        $endDate = date('Y-m-d', strtotime("$year-$month-1 + 1 MONTH"));

        $myClass = MyClass::where('classObjectId', $classObjectId)->first();
        if (!$myClass) {
            return ['fail' => 'class not found'];
        }

        if (!$myClass->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $user = Auth::user();
        if ($user->id == $myClass->userId || $user->authority == 5 || $user->authority == 6) {
            if ((int)$teamId == 0) {
                //전체 조회
                return Calendar::where('classObjectId', $myClass->classObjectId)->whereBetween('dateTime', [$startDate, $endDate])->orderBy('dateTime', 'asc')->get();
            }
            else {
                //특정 팀 조회
                $team = Team::find($teamId);
                if (!$team) {
                    return ['fail' => 'team not found'];
                }

                if ($team->classObjectId != $myClass->classObjectId) {
                    return ['fail' => 'permission denied'];
                }

                return Calendar::where('teamId', $team->id)->where('classObjectId', $myClass->classObjectId)->whereBetween('dateTime', [$startDate, $endDate])->orderBy('dateTime', 'asc')->get();
            }
        }
        else {
            $teamMember = TeamMember::where('userId', $user->id)->where('teamId', $teamId)->first();
            if (!$teamMember) {
                return ['fail' => 'permission denied'];
            }

            $team = $teamMember->team();
            if (!$team) {
                return ['fail' => 'team not found'];
            }

            return Calendar::where('teamId', $team->id)->where('classObjectId', $myClass->classObjectId)->whereBetween('dateTime', [$startDate, $endDate])->where('userId', '!=', $myClass->userId)->orderBy('dateTime', 'asc')->get();
        }
    }
    public function deleteCalendar(Request $request, $calendarId){
//        $user = Auth::user();
        $calendar = Calendar::findOrFail($calendarId);

        if(Auth::id() == $calendar->userId) {
            return response()->json($calendar->delete(), 200);
        }else{
            return ['fail' => 'permission denied'];
        }
    }
    public function updateCalendar(Request $request){ //, , $classObjectId,$teamId, $dateTime,$content
        $validator = Validator::make([
            'calendarId' => $request['calendarId'],
            'dateTime' => $request['dateTime'],
            'content' => $request['content'],
        ], [
            'calendarId' => ['required', 'integer', 'min:1'],
            'dateTime' => ['required', 'date'],
            'content' => ['required', 'string'],

        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('calendarId')) {
                return ['fail' => 'invalid calendarId'];
            }
            if ($errors->has('dateTime')) {
                return ['fail' => 'invalid dateTime'];
            }
            if ($errors->has('content')) {
                return ['fail' => 'invalid content'];
            }
        }
        $user = Auth::user();
        $calendar = Calendar::find($request['calendarId']);
        if(!$calendar){
            return ['fail' => 'invalid calendar'];
        }
        if($user->id == $calendar->userId){
            return response()->json($calendar->update([
                'content' => $request['content'],
                'dateTime' => $request['dateTime']
            ]),200);
        }else{
            return ['fail' => 'permission denied'];
        }
    }

    public function postCalendar (Request $request) {
        $validator = Validator::make($request->all(), [
            'classObjectId' => ['required', 'integer', 'min:1'],
            'teamId' => ['required', 'integer', 'min:0'],
            'dateTime' => ['required', 'date'],
            'content' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('classObjectId')) {
                return ['fail' => 'invalid classObjectId'];
            }
            if ($errors->has('teamId')) {
                return ['fail' => 'invalid teamId'];
            }
            if ($errors->has('year')) {
                return ['fail' => 'invalid year'];
            }
            if ($errors->has('month')) {
                return ['fail' => 'invalid month'];
            }
        }

        $myClass = MyClass::where('classObjectId', $request['classObjectId'])->first();
        if (!$myClass) {
            return ['fail' => 'class not found'];
        }

        if (!$myClass->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $user = Auth::user();
        if (!Expert::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first()) {
            if ($user->authority == 5) {
                return ['fail' => 'permission denied'];
            }
        }
        if (!ClassManager::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first()) {
            if ($user->authority == 6) {
                return ['fail' => 'permission denied'];
            }
        }
        if ($user->authority == 2 || $user->authority == 5 || $user->authority == 6) {
            $calendar = null;
            if ((int)$request['teamId'] == 0) {
                //전체 조회
                $calendar = Calendar::create([
                    'userId' => Auth::id(),
                    'classObjectId' => $myClass->classObjectId,
                    'dateTime' => $request['dateTime'],
                    'content' => $request['content'],
                ]);
            }
            else {
                //특정 팀 조회
                $team = Team::find($request['teamId']);
                if (!$team) {
                    return ['fail' => 'team not found'];
                }

                if ($team->classObjectId != $myClass->classObjectId) {
                    return ['fail' => 'permission denied'];
                }

                $calendar = Calendar::create([
                    'userId' => Auth::id(),
                    'classObjectId' => $myClass->classObjectId,
                    'teamId' => $team->id,
                    'dateTime' => $request['dateTime'],
                    'content' => $request['content'],
                ]);
            }

//            $team = Team::find($request['teamId']);
            $calendar->load('classApply');

            if (strtotime($calendar->dateTime) >= strtotime(date('Y-m-d')) && strtotime($calendar->dateTime) < strtotime(date('Y-m-d', strtotime('now + 1 DAY')))) {
                $calendar->today = true;

                broadcast(
                    new ActLogEvent($calendar->toArray(), $myClass->userId, 'sch', 'SCH')
                );
            }
            else if (strtotime($calendar->dateTime) >= strtotime(date('Y-m-d', strtotime('now + 1 DAY'))) && strtotime($calendar->dateTime) < strtotime(date('Y-m-d', strtotime('now + 2 DAY')))) {
                $calendar->today = false;

                broadcast(
                    new ActLogEvent($calendar->toArray(), $myClass->userId, 'sch', 'SCH')
                );
            }

            return $calendar;
        }
        else if ($user->authority == 1) {
            $teamMember = TeamMember::where('userId', $user->id)->where('teamId', $request['teamId'])->first();
            if (!$teamMember) {
                return ['fail' => 'permission denied'];
            }

            $team = $teamMember->team();
            if (!$team) {
                return ['fail' => 'team not found'];
            }

            $calendar = Calendar::create([
                'userId' => Auth::id(),
                'classObjectId' => $myClass->classObjectId,
                'teamId' => $team->id,
                'dateTime' => $request['dateTime'],
                'content' => $request['content'],
            ]);

            broadcast(
                new CalendarEvent(['calendar' => $calendar], $team->id,
                    'MYTEAM',
                    'add')
            )->toOthers();

            broadcast(
                new CalendarEvent(['calendar' => $calendar], $team->classObjectId,
                    'MYCLASS',
                    'add')
            )->toOthers();

            $calendar->load('classObject');

            if (strtotime($calendar->dateTime) >= strtotime(date('Y-m-d')) && strtotime($calendar->dateTime) < strtotime(date('Y-m-d', strtotime('now + 1 DAY')))) {
                $calendar->today = true;

                broadcast(
                    new ActLogEvent($calendar->toArray(), $myClass->userId, 'sch', 'SCH')
                );

//                broadcast(
//                    new ActLogEvent($calendar->toArray(), $myClass->userId, 'sch', 'STU_SCH')
//                );
            }
            else if (strtotime($calendar->dateTime) >= strtotime(date('Y-m-d', strtotime('now + 1 DAY'))) && strtotime($calendar->dateTime) < strtotime(date('Y-m-d', strtotime('now + 2 DAY')))) {
                $calendar->today = false;

                broadcast(
                    new ActLogEvent($calendar->toArray(), $myClass->userId, 'sch', 'SCH')
                );

//                broadcast(
//                    new ActLogEvent($calendar->toArray(), $myClass->userId, 'sch', 'STU_SCH')
//                );
            }

            return $calendar;
        }
    }


    public function experts (Request $request) {
        if (Auth::user()->authority != 2) {
            return [];
        }

        $users = User::select('id', 'name', 'email')->where('authority', 5)->get();

        return $users;
    }
    public function managers (Request $request) {
        if (Auth::user()->authority != 2) {
            return [];
        }

        $users = User::select('id', 'name', 'email')->where('authority', 6)->get();

        return $users;
    }


    private function asterisk($string) {
        $string = trim($string);
        $length = mb_strlen($string, 'utf-8');
        $string_changed = $string;
        if ($length <= 2) {
            // 한두 글자면 그냥 뒤에 별표 붙여서 내보낸다.
            $string_changed = mb_substr($string, 0, 1, 'utf-8') . '*';
        }
        if ($length >= 3) {
            // 3으로 나눠서 앞뒤.
            $leave_length = floor($length/4); // 남겨 둘 길이. 반올림하니 너무 많이 남기게 돼, 내림으로 해서 남기는 걸 줄였다.
            $asterisk_length = $length - ($leave_length * 2);
            $offset = $leave_length + $asterisk_length;
            $head = mb_substr($string, 0, $leave_length, 'utf-8');
            $tail = mb_substr($string, $offset, $leave_length, 'utf-8');
            $string_changed = $head . implode('', array_fill(0, $asterisk_length, '*')) . $tail;
        }
        return $string_changed;
    }
}
