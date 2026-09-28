<?php

namespace App\Http\Controllers;

use http\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//이렇게 선언하고 사용
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Broadcast;

//페이지 네이션 선언부
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
/////////
use App\Models\User;
use App\Models\Member;
use App\Models\MyClass;
use App\Models\ClassObject;
use App\Models\ClassList;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Card;
use App\Models\Item;
use App\Models\ReflectionLog;
use App\Models\ProblemAnalysis;
use App\Models\TeamActivity;
use App\Models\ClassApply;
use Illuminate\Support\Arr;
use App\Models\ActLog;
use App\Events\ItemEvent;
use App\Events\SuperEvent;



class LecCon2 extends Controller
{
    public function lectureList (Request $request) {
        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->back();
        }

        $perPage = 3;
        $myClasses = MyClass::where('userId', $user->id)->paginate($perPage);
        if (!$myClasses) {
            return redirect()->back();
        }

        if($request->has('content')){
            $validator = Validator::make($request->all(),[
                'content' => ['nullable', 'string','min:2']
            ]);

            if(!$validator->fails()){
                $content = $request['content'];
                $myClasses = MyClass::where('userId', Auth::id())
                    ->with(['withClassObject' => function ($query) use($content) {
                        $query->where('name', 'like', '%'.$content.'%');
                    }])->get()->whereNotNull('wi
                    thClassObject');

                $paginate = new LengthAwarePaginator(
                    $myClasses->forPage(Paginator::resolveCurrentPage(), $perPage),
                    $myClasses->count(),
                    $perPage,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );

                $myClasses = $paginate;
            }
            else {
                $errors = $validator->errors();

                if ($errors->has('content')) {
                    return redirect('/lectureList')->withErrors(['content_length' => '검색어를 두자 이상 입력하세요.']);
                }
            }
        }

        return view('./lecture/lecture_list',['myClasses' => $myClasses]);
    }

    //정보
    public function lectureDetailInfo (Request $request,$classObjectId){
        $classObject = ClassObject::where('id', $classObjectId)->first();

        $classApply = ClassApply::where('classObjectId', $classObject->id)->first();

        return view('./lecture/lecture_detail_info',['classObjectId' => $classObjectId, 'classObjectName' => $classObject, 'classApply' => $classApply]);
    }

    public function lectureDetailBeforeApproval (Request $request){
        $classApply = ClassApply::find(109);
        return view('./lecture/lecture_detail_before_approval',['classApply' => $classApply]);
    }


    public function lectureDetailMember (Request $request, $classObjectId) {
        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        //한 페이지 에 몇개 표시 할건지 perPage변수에 넣어줌
        $perPage = 3;

        if ($request->has('type') && $request->has('content')) {
            $validator = Validator::make($request->all(), [
                'type' => ['required', 'string', 'in:name,email'],
                'content' => ['nullable', 'string', 'min:2']
            ]);


            if (!$validator->fails()) {
                $type = $request['type'];
                $content = $request['content'];
                $classLists = ClassList::where('classObjectId', $classObjectId)->with(['withUser' => function ($query) use($type, $content) {
                    $query->where($type, 'like', '%'.$content.'%');
                }])->get()->whereNotNull('withUser');

                $paginate = new LengthAwarePaginator(
                    $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
                    $classLists->count(),
                    $perPage,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );

                return view('./lecture/lecture_detail_member',['classObjectId' => $classObjectId, 'classLists' => $paginate, 'classObjectName' => $classObjectName] );
            }
            else {
                $errors = $validator->errors();

                if ($errors->has('content')) {
                    return redirect()->route('lectureDetailMember', ['classObjectId' => $classObjectId])->withErrors(['content_length' => '검색어를 두자 이상 입력하세요.']);
                }
                return redirect()->back()->withErrors($validator->errors());
            }
        }

        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->back();
        }

        $classLists = $classObject->withClassLists()->paginate($perPage);

        /*
                //자 이제 이거 이게 검색한거 타입이랑 컨텐츠로 리스트 뿌려주는거야?
                //if (request에 type이랑, content가 있으면)
                if ($request->has('type') && $request->has('content')) {
                    //if(request에 컨텐츠가 안 비어있으면) 비어있으면 그냥 출력함
                    if ($request['content'] != '') {
                        //검색 결과 받을 변수 선언
                        $searchResults = [];
                        //클래스 리스트 foreach로 뽑음
                        foreach($classLists as $classList) {
                            //request type이 name이면
                            if($request['type'] == 'name') {
                                //result에  strpos(탐색할 문자열, 찾을 문자열 )
                                $result = strpos($classList->user()->name, $request['content']);
                                //제가 해볼게여 쌤 ㅇ strpos가 트루펄스 반환하는거같은데 일치하는거 있으면 아 맞아 이게 php에서 값이 그냥 0이면 if를 안돌아 그래서
                                //저 타입이 불리언이 아니면 if돌리는거 ㅇㅇ  ㅇㅇ 글서 like로 머 검색할값 일치하는거 있으면$searchResults엿다가 뽑을 리스트 추가해주고 뽑는거?
                                //ㅇㅇ 넹 이제 우리 뭐해야해여?
                                if (gettype($result) != 'boolean') {
                                    $searchResults[] = $classList;
                                }
                            }
                            //이거요 >>ㅏ악 ???ㅋㅋㅋㅋ 이거먼데 이메일타입은 안했어? 이메일도 아까 검색대는거같던디
                            //request type이 email이면
                            elseif ($request['type'] == 'email') {
                                //이하 동문
                                $result = strpos($classList->user()->email, $request['content']);
                                if (gettype($result) != 'boolean') {
                                    $searchResults[] = $classList;
                                }
                            }
                        }
                        return view('./lecture/lecture_detail_member',['classObjectId' => $classObjectId, 'classLists' => $searchResults] );
                    }
                }
        */
        return view('./lecture/lecture_detail_member',['classObjectId' => $classObjectId, 'classLists' => $classLists, 'classObjectName' => $classObjectName] );
    }


    //팀배정
    public function lectureDetailTeamSelect (Request $request, $classObjectId) {
        $classObjectName = ClassObject::where('id', $classObjectId)->first();

        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();

        return view('./lecture/lecture_detail_teamselect',['classObjectId' => $classObjectId, 'classObjectName' => $classObjectName, 'classApply' => $classApply]);
    }


    //문제분석
    public function lectureDetailProblem (Request $request, $classObjectId) {
//        $classObject = ClassObject::find($classObjectId);
        $classObject = ClassObject::find($classObjectId);
//        return dd($classObject);
        if (!$classObject) {
            return redirect()->route('lectureList');
        }

        $cards = Card::where('classObjectId', $classObjectId)->with('withItemsWithProblems', function ($query) {
            $query->where('type', 1);
        })->get()->where('withItemsWithProblems', '!=', '[]');

        $data = [];
        if ($cards) {
            foreach ($cards as $card) {
                foreach ($card->withItemsWithProblems as $item) {
                    $data[] = $item;
                }
            }
        }

        $data = collect($data);

        $perPage = 10;
        $paginate = new LengthAwarePaginator(
            $data->forPage(Paginator::resolveCurrentPage(), $perPage),
            $data->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();
        return view('./lecture/lecture_detail_problem',
            [ 'classObject' => $classObject, 'problems' => $paginate ,'classApply'=> $classApply ]);
    }
    /////////////////////////////////////////////////////////////////

    //문제분석 디테일
    public function lectureDetailProblemDetail (Request $request, $classObjectId, $problemAnalysisId) {
        $validator = Validator::make(
            [
                'classObjectId' => $classObjectId,
                'problemAnalysisId' => $problemAnalysisId
            ], [
            'classObjectId' => ['required', 'integer', 'min:1'],
            'problemAnalysisId' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->back();
        }

        $problemAnalysis = ProblemAnalysis::find($problemAnalysisId);
        if (!$problemAnalysis) {
            return redirect()->back();
        }

        $card = $problemAnalysis->item()->card();
        if (!$card) {
            return redirect()->back();
        }

        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();
        return view('./lecture/lecture_detail_problemdetail',
            [ 'classObject' => $classObject, 'team' => $card->team(), 'problemAnalysis' => $problemAnalysis , 'classApply' => $classApply]);
    }

    //팀 활동 보고서
    public function lectureDetailTeam (Request $request, $classObjectId) {
        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->route('lectureList');
        }

        $cards = Card::where('classObjectId', $classObjectId)->with('withItemsWithTeamActivity', function ($query) {
            $query->where('type', 5);
        })->get()->where('withItemsWithTeamActivity', '!=', '[]');

        $data = [];
        if ($cards) {
            foreach ($cards as $card) {
                foreach ($card->withItemsWithTeamActivity as $item) {
                    $data[] = $item;
                }
            }
        }

        $data = collect($data);

        $perPage = 10;
        $paginate = new LengthAwarePaginator(
            $data->forPage(Paginator::resolveCurrentPage(), $perPage),
            $data->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();
        return view('./lecture/lecture_detail_team',[ 'classObject' => $classObject, 'teamActivities' => $paginate , 'classApply' => $classApply]);
    }
    //팀 활동 보고서 디테일
    public function lectureDetailTeamReport (Request $request, $classObjectId, $teamActivityId) {
        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->back();
        }

        $teamActivity = TeamActivity::find($teamActivityId);
        if (!$teamActivity) {
            return redirect()->back();
        }

        if ($teamActivity->item()->card()->classObjectId != $classObject->id) {
            return redirect()->back();
        }

        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();
        return view('lecture.lecture_detail_teamdetail', [ 'classObject' => $classObject, 'teamActivity' => $teamActivity,'classApply' => $classApply]);
    }

    public function lectureDetailTeamReportFeedback (Request $request, $classObjectId, $teamActivityId) {
        $retVal = redirect()->back();
        if ($request->has('check')) {
            $retVal = false;
        }

        $request->validate([
            'feedback' => ['required', 'string'],
        ]);

        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return $retVal;
        }

        $teamActivity = TeamActivity::find($teamActivityId);
        if (!$teamActivity) {
            return $retVal;
        }

        if ($teamActivity->item()->card()->classObjectId != $classObject->id) {
            return $retVal;
        }

        if (!$classObject->isOwner()) {
            return $retVal;
        }

        $teamActivity->feedback = $request['feedback'];
        $teamActivity->save();

        $item = $teamActivity->item();
        if (!$item) {
            return $retVal;
        }
        $card = $item->card();
        if (!$card) {
            return $retVal;
        }

        if ($request->has('check')) {
            $item->load('withTeamActivity');
        }

        $item->load('withCard');
        if (is_null($card->myPageId)) {
            if ($card->type != 9) {
                broadcast(
                    new ItemEvent(
                        $item->toArray(),
                        $card->teamId ?? $card->classObjectId,
                        $card->teamId ? 'MYTEAM' : 'MYCLASS',
                        'update')
                )->toOthers();
            }
            else {
                if ($item->type == 5) {
                    if (!is_null($item->card()->teamId)) {
                        if ($item->card()->myClass()->onTeamAccess) {
                            broadcast(
                                new ItemEvent(
                                    $item->toArray(),
                                    $card->classObjectId,
                                    'MYCLASS',
                                    'update')
                            )->toOthers();
                        }
                        else {
                            $_card = Card::where('classObjectId', $card->classObjectId)->where('teamId', null)->where('type', $card->type)->first();
                            if ($_card) {
                                broadcast(
                                    new SuperEvent(
                                        $item->toArray(),
                                        $card->classObjectId,
                                        $_card->id,
                                        'update')
                                )->toOthers();
                            }
                        }

                        broadcast(
                            new ItemEvent(
                                $item->toArray(),
                                $card->teamId,
                                'MYTEAM',
                                'update')
                        )->toOthers();
                    }
                    else {
                        broadcast(
                            new ItemEvent(
                                $item->toArray(),
                                $card->classObjectId,
                                'MYCLASS',
                                'update')
                        )->toOthers();
                    }
                }
                else {
                    broadcast(
                        new ItemEvent(
                            $item->toArray(),
                            $card->teamId ?? $card->classObjectId,
                            $card->teamId ? 'MYTEAM' : 'MYCLASS',
                            'update')
                    )->toOthers();
                }
            }
//            if (!is_null($card->classObjectId) && in_array($item->type, [1, 2, 5])) {
//                $user = Auth::user();
//                $classObject = ClassObject::find($card->classObjectId);
//                $team = Team::find($card->teamId);
//                $myClass = MyClass::where('classObjectId', $classObject->id)->first();
//
//                $actLog = ActLog::create([
//                    'userId' => $user->id,
//                    'userName' => $user->name,
//                    'classObjectId' => $classObject->id,
//                    'classObjectName' => $classObject->gwamokNm,
//                    'teamId' => $team ? $team->id : null,
//                    'teamName' => $team ? $team->name : null,
//                    'authority' => $user->authority,
//                    'eventType' => $item->type * 10,
//                    'eventState' => 20,
//                    'eventTitle' => $item->title,
//                ]);
//
//                broadcast(
//                    new ActLogEvent($actLog->toArray(), $myClass->userId)
//                );
//
//                if (!is_null($card->teamId) && in_array($item->type, [1, 2, 5])) {
//                    $teamMembers = $team->teamMembers();
//
//                    foreach ($teamMembers as $member) {
//                        if (is_null($member->userId)) {
//                            continue;
//                        }
//
//                        broadcast(
//                            new ActLogEvent($actLog->toArray(), $member->userId)
//                        );
//                    }
//                }
//                else {
//                    $classMembers = $myClass->members();
//
//                    foreach ($classMembers as $member) {
//                        if (is_null($member->userId)) {
//                            continue;
//                        }
//
//                        broadcast(
//                            new ActLogEvent($actLog->toArray(), $member->userId)
//                        );
//                    }
//                }
//            }
        }

        if ($request->has('check')) {
            return [$item];
        }

        return redirect()->route('lectureDetailTeamReport', ['classObjectId' => $classObjectId, 'teamActivityId' => $teamActivityId]);
    }

    //평가지
    public function lectureDetailEvolutionPaper (Request $request, $classObjectId) {
        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        $teams = Team::where('classObjectId', $classObjectId)->get();

        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();
        return view('./lecture/lecture_detail_evolutionpaper',
            [ 'classObjectId' => $classObjectId, 'teams' => $teams, 'classObjectName' => $classObjectName, 'classApply' => $classApply ]);
    }
    //평가지 디테일
    public function lectureDetailEvolutionPaperDetail (Request $request,$classObjectId, $teamId) {
        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        $teamId = Team::where('id', $teamId)->first();

        return view('./lecture/lecture_detail_evolutionpaperdetail',[ 'classObjectId' => $classObjectId, 'teamId' => $teamId, 'classObjectName' => $classObjectName ]);
    }

    //성찰일지
//    public function lectureDetailMind (Request $request, $classObjectId) {
//
//        $teams = Team::where('classObjectId', $classObjectId)->get();
//        return view('./lecture/lecture_detail_mind',[ 'classObjectId' => $classObjectId, 'teams' => $teams ]);
//    }

    //성찰일지
    public function lectureDetailMind (Request $request, $classObjectId) {
        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->route('lectureList');
        }

        $cards = Card::where('classObjectId', $classObjectId)->with('withItemsWithReflections', function ($query) {
            $query->where('type', 2);
        })->get()->where('withItemsWithReflections', '!=', '[]');

        $data = [];
        if ($cards) {
            foreach ($cards as $card) {
                foreach ($card->withItemsWithReflections as $item) {
                    $data[] = $item;
                }
            }
        }

        $data = collect($data);

        $perPage = 10;
        $paginate = new LengthAwarePaginator(
            $data->forPage(Paginator::resolveCurrentPage(), $perPage),
            $data->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();

        return view('./lecture/lecture_detail_mind',[ 'classObject' => $classObject, 'reflections' => $paginate ,'classApply' => $classApply] );
    }

    //성찰일지 디테일
    public function lectureDetailMindDetail (Request $request, $classObjectId, $reflectionLogId ) {
        $validator = Validator::make(
            [
                'classObjectId' => $classObjectId,
                'reflectionLogId' => $reflectionLogId
            ], [
            'classObjectId' => ['required', 'integer', 'min:1'],
            'reflectionLogId' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->back();
        }

        $reflectionLog = ReflectionLog::find($reflectionLogId);
        if (!$reflectionLog) {
            return redirect()->back();
        }

        $card = $reflectionLog->item()->card();
        if (!$card) {
            return redirect()->back();
        }

        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();
        return view('./lecture/lecture_detail_minddetail',
            [ 'classObject' => $classObject, 'team' => $card->team(), 'reflectionLog' => $reflectionLog , 'classApply' => $classApply]);
    }

}
