<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyPage;
use App\Models\MyClass;
use App\Models\Card;
use App\Models\Item;
use App\Models\ClassObject;
use App\Models\ClassList;
use App\Models\User;
use App\Models\Competition;
use App\Models\CompetitionApply;
use App\Models\Basic;
use App\Models\BasicApply;
use App\Models\Consulting;
use App\Models\ClassApply;
use App\Models\ConsultingApply;
use App\Models\AdminLog;
use App\Models\ProLog;
use App\Models\ActLog;
use App\Models\TeamMember;
use App\Models\Daehak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Events\AdminLogEvent;

class MyPageController extends Controller
{
    //
    public function dash (Request $request) {
        $user = Auth::user();

        switch ($user->authority) {
            case 1:
                //return redirect()->route('dashboardStuView');
                return redirect()->route('myClassView');
            case 2:
                //return redirect()->route('dashboardProView');
                return redirect()->route('lectureList');
            case 4:
                return redirect()->route('dashboardConView');
            default:
                return redirect()->route('dashboardOutView');
        }
    }

    public function myPageView (Request $request) {
        

        //현재 학기 데이터
        /*
         *
         * */


        //리퀘스트 학기 데이터
        /*
         *
         * */


        $myPage = MyPage::where('userId', Auth::id())->first();
        if (!$myPage) {
            $myPage = MyPage::create([
               'userId' => Auth::id(),
            ]);
        }

        $card = Card::where('myPageId', $myPage->id)->where('type', 0)->first();
        if (!$card) {
            if (Auth::user()->authority == 2) {
                $card = Card::create([
                    'userId' => Auth::id(),
                    'myPageId' => $myPage->id,
                    'type' => 0,
                    'title' => '수업개설',
                ]);

                $cardsNum = $myPage->cardsNum;
                $cardsNum[] = $card->id;
                $myPage->cardsNum = $cardsNum;

                $myPage->save();
                $myPage->refresh();
            }
            else {
//                $classObjects = ClassObject::get();
//                foreach ($classObjects as $classObject) {
//                    $classList = ClassList::where('classObjectId', $classObject->id)->where('userId', Auth::id())->first();
//                    if (!$classList) {
//                        ClassList::create([
//                            'userId' => Auth::id(),
//                            'classObjectId' => $classObject->id,
//                            'classObjectName' => $classObject->name,
//                        ]);
//                    }
//                }
            }
        }

        $card = Card::where('myPageId', $myPage->id)->where('type', 1)->first();
        if (!$card) {
            if (Auth::user()->authority == 2) {
                $card = Card::create([
                    'userId' => Auth::id(),
                    'myPageId' => $myPage->id,
                    'type' => 1,
                    'title' => '기초교육',
                ]);

                $cardsNum = $myPage->cardsNum;
                $cardsNum[] = $card->id;
                $myPage->cardsNum = $cardsNum;

                $myPage->save();
                $myPage->refresh();
            }
            else {
            }
        }

        $card = Card::where('myPageId', $myPage->id)->where('type', 2)->first();
        if (!$card) {
            if (Auth::user()->authority == 2) {
                $card = Card::create([
                    'userId' => Auth::id(),
                    'myPageId' => $myPage->id,
                    'type' => 2,
                    'title' => '컨설팅',
                ]);

                $cardsNum = $myPage->cardsNum;
                $cardsNum[] = $card->id;
                $myPage->cardsNum = $cardsNum;

                $myPage->save();
                $myPage->refresh();
            }
            else {
            }
        }

//        $card = Card::where('myPageId', $myPage->id)->where('type', 3)->first();
//        if (!$card) {
//            if (Auth::user()->authority == 2) {
//                $card = Card::create([
//                    'userId' => Auth::id(),
//                    'myPageId' => $myPage->id,
//                    'type' => 3,
//                    'title' => '수업관리',
//                ]);
//
//                $cardsNum = $myPage->cardsNum;
//                $cardsNum[] = $card->id;
//                $myPage->cardsNum = $cardsNum;
//
//                $myPage->save();
//                $myPage->refresh();
//            }
//            else {
//            }
//        }

        $card = Card::where('myPageId', $myPage->id)->where('type', 13)->first();
        if (!$card) {
            if (Auth::user()->authority == 2) {
                $card = Card::create([
                    'userId' => Auth::id(),
                    'myPageId' => $myPage->id,
                    'type' => 13,
                    'title' => '요약보고서',
                ]);

                $cardsNum = $myPage->cardsNum;
                $cardsNum[] = $card->id;
                $myPage->cardsNum = $cardsNum;

                $myPage->save();
                $myPage->refresh();
            }
            else {
            }
        }

        $myClasses = MyClass::where('userId', Auth::id())->with('withClassApply')->get()
            ->map(function ($class) {
                if ($class->withClassApply) {
                    if ($class->withClassApply->classObjectId) {
                        return $class->withClassApply;
                    }
                }
                return null;
            })->filter(function ($classApply) {
                return $classApply;
            });

        $daehaks = Daehak::all()->unique('department')->groupBy('name');

//        return dd($daehaks);

        return view('myPage.my_page_view', ['myPage' => $myPage, 'myClasses' => $myClasses, 'daehaks' => $daehaks]);
    }

    public function proList (Request $request, $year, $semester) {
        //permission check

//        return dd([$year, $semester]);

        $validator = Validator::make(['year' => $year, 'semester' => $semester], [
            'year' => ['required', 'integer', 'min:2000', 'max:9999'],
            'semester' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('year')) {
                $year = date('Y', strtotime('now'));
            }
            if ($errors->has('semester')) {
                $semester = '1';
            }
        }

//        return dd($year, $semester);

        $myPage = MyPage::where('userId', Auth::id())->where('year', $year)->where('semester', $semester)->first();
        if (!$myPage) {
            $myPage = MyPage::create([
                'userId' => Auth::id(),
                'year' => $year,
                'semester' => $semester
            ]);

            $card = Card::where('myPageId', $myPage->id)->where('type', 0)->first();
            if (!$card) {
                if (Auth::user()->authority == 2) {
                    $card = Card::create([
                        'userId' => Auth::id(),
                        'myPageId' => $myPage->id,
                        'type' => 0,
                        'title' => '수업개설',
                    ]);

                    $cardsNum = $myPage->cardsNum;
                    $cardsNum[] = $card->id;
                    $myPage->cardsNum = $cardsNum;

                    $myPage->save();
                    $myPage->refresh();
                }
                else {
//                $classObjects = ClassObject::get();
//                foreach ($classObjects as $classObject) {
//                    $classList = ClassList::where('classObjectId', $classObject->id)->where('userId', Auth::id())->first();
//                    if (!$classList) {
//                        ClassList::create([
//                            'userId' => Auth::id(),
//                            'classObjectId' => $classObject->id,
//                            'classObjectName' => $classObject->name,
//                        ]);
//                    }
//                }
                }
            }

            $card = Card::where('myPageId', $myPage->id)->where('type', 1)->first();
            if (!$card) {
                if (Auth::user()->authority == 2) {
                    $card = Card::create([
                        'userId' => Auth::id(),
                        'myPageId' => $myPage->id,
                        'type' => 1,
                        'title' => '기초교육',
                    ]);

                    $cardsNum = $myPage->cardsNum;
                    $cardsNum[] = $card->id;
                    $myPage->cardsNum = $cardsNum;

                    $myPage->save();
                    $myPage->refresh();
                }
                else {
                }
            }

            $card = Card::where('myPageId', $myPage->id)->where('type', 2)->first();
            if (!$card) {
                if (Auth::user()->authority == 2) {
                    $card = Card::create([
                        'userId' => Auth::id(),
                        'myPageId' => $myPage->id,
                        'type' => 2,
                        'title' => '컨설팅',
                    ]);

                    $cardsNum = $myPage->cardsNum;
                    $cardsNum[] = $card->id;
                    $myPage->cardsNum = $cardsNum;

                    $myPage->save();
                    $myPage->refresh();
                }
                else {
                }
            }

//        $card = Card::where('myPageId', $myPage->id)->where('type', 3)->first();
//        if (!$card) {
//            if (Auth::user()->authority == 2) {
//                $card = Card::create([
//                    'userId' => Auth::id(),
//                    'myPageId' => $myPage->id,
//                    'type' => 3,
//                    'title' => '수업관리',
//                ]);
//
//                $cardsNum = $myPage->cardsNum;
//                $cardsNum[] = $card->id;
//                $myPage->cardsNum = $cardsNum;
//
//                $myPage->save();
//                $myPage->refresh();
//            }
//            else {
//            }
//        }

            $card = Card::where('myPageId', $myPage->id)->where('type', 13)->first();
            if (!$card) {
                if (Auth::user()->authority == 2) {
                    $card = Card::create([
                        'userId' => Auth::id(),
                        'myPageId' => $myPage->id,
                        'type' => 13,
                        'title' => '요약보고서',
                    ]);

                    $cardsNum = $myPage->cardsNum;
                    $cardsNum[] = $card->id;
                    $myPage->cardsNum = $cardsNum;

                    $myPage->save();
                    $myPage->refresh();
                }
                else {
                }
            }
        }

        $myClasses = MyClass::where('userId', Auth::id())->with('withClassApply')->with(['withClassObject' => function ($query) use($year, $semester) {
            $query->where('suupYear', $year)->where('suupTerm', $semester * 5 + 5);
        }])->get()->filter(function ($page) {
            return $page->withClassObject;
        })
            ->map(function ($class) {
                if ($class->withClassApply) {
                    if ($class->withClassApply->classObjectId) {
                        return $class->withClassApply;
                    }
                }
                return null;
            })->filter(function ($classApply) {
                return $classApply;
            });

        return ['myPage' => $myPage, 'myClasses' => $myClasses,'year' => $year, 'semester' => $semester];
    }

    //마이페이지 디비 생성을 위한 임시 페이지
    public function createMyPageView (Request $request) {
        return view('myPage.create_my_page_view');
    }

    //마이페이지 생성 post
    public function createMyPage (Request $request) {
        $myPage = MyPage::where('userId', Auth::id())->first();
        if (!$myPage) {
            MyPage::create([
                'userId' => Auth::id()
            ]);
        }

        return redirect('/myPage');
    }

    public function myStuList(Request $request)
    {
//        if($request['infoBtn']){
//            return dd("btn Click");
//        }

        if (Auth::user()->authority != 1) {
            return redirect()->back();
        }

        $myPage = MyPage::where('userId', Auth::id())->first();
        if (!$myPage) {
            MyPage::create([
                'userId' => Auth::id(),
            ]);
        }

        $selectType = $request->only('type');
        $perPage = 10;

        $userId = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'type' => ['required', 'string', 'in:classObjectName,professor'],
            'content' => ['required', 'string', 'min:2'],
        ]);

        $classLists = [];
        $paginate = [];
        if (!$validator->fails()) {
            if ($request['type'] == 'classObjectName') {
                $name = $request['content'];
                $classLists = ClassList::where('userId', $userId)->with(['applyInfo' => function ($query) use($name) {
                    $query->where('korName', 'like', '%'.$name.'%');
                }])->get()->filter(function ($item) {
                    return $item->applyInfo;
                });

                $paginate = new LengthAwarePaginator(
                    $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
                    $classLists->count(),
                    $perPage,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );
            }
            else {
                $name = $request['content'];
                $classLists = ClassList::where('userId', $userId)->with('applyInfo')
                    ->with(['myClassUser' => function ($query) use($name) {
                        $query->with(['user2' => function ($_query) use($name) {
                            $_query->where('name', 'like', '%'.$name.'%');
                        }]);
                    }])
                    ->get()->filter(function ($item) {
                        if ($item->myClassUser) {
                            return $item->myClassUser->user2;
                        }

                        return false;
                });

                $paginate = new LengthAwarePaginator(
                    $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
                    $classLists->count(),
                    $perPage,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );
            }
        }
        else {
            $classLists = ClassList::where('userId', $userId)->with('applyInfo')->get()->filter(function ($item) {
                return $item->applyInfo;
            });


            $paginate = new LengthAwarePaginator(
                $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
                $classLists->count(),
                $perPage,
                Paginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
            );

//            ->paginate($perPage);
        }



//        if (!$classLists) {
//            return redirect()->back();
//        }
//
//        if ($request->has('content')) {
//            $validator = Validator::make($request->all(), [
//                'content' => ['nullable', 'string', 'min:2']
//            ]);
//
//            if (!$validator->fails()) {
//                if ($selectType == ['type' => 'classObjectName']) {
//                    if ($request->has('content')) {
//                        if (!$validator->fails()) {
//                            $searchData = $request['content'];
//                            $classLists = ClassList::where('userId', $userId)
//                                ->where('gwamokNm', 'LIKE', '%' . $searchData . '%')
//                                ->with('applyInfo')->get();
//
////                                $searchData = $request['content'];
////                                $classLists = [];
////                                $classObjects = ClassObject::where('gwamokNm','LIKE', '%'.$searchData.'%')->get();
////                                foreach ($classObjects as $classObject){
////                                    $classList = ClassList::where('classObjectId', $classObject->id)
////                                        ->where('userId', Auth::id())
////                                        ->with('applyInfo')
////                                        ->first();
////                                    if($classList){
////                                        $classLists = $classList;
////                                    }
////                                }
//
////                                return dd($classLists);
//                            $paginate = new LengthAwarePaginator(
//                                $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
//                                $classLists->count(),
//                                $perPage,
//                                Paginator::resolveCurrentPage(),
//                                ['path' => Paginator::resolveCurrentPath()]
//                            );
//
////                                return dd($classLists);
////                            $classLists = $paginate;
//                            return view('myPage.student.my_stu_list', ['classLists' => $paginate]);
//                        }
//                    }
//                } elseif ($selectType == ['type' => 'professor']) {
//                    if (!$validator->fails()) {
//                        $professors = User::where('name', 'like', '%' . $request['content'] . '%')->get();
//                        $classLists = [];
//                        foreach ($professors as $professor) {
//                            $myClasses = MyClass::where('userId', $professor->id)->get();
////                            return dd($myClasses);
//                            foreach ($myClasses as $myClass) {
////                                $index[] = $myClass->classObjectId;
//                                $classList = ClassList::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first();
//                                if($classList){
//                                    $classLists[] = $classList;
//                                }
//                            }
//                        }
//                        $classLists = collect($classLists);
//
//                        $paginate = new LengthAwarePaginator(
//                            $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
//                            $classLists->count(),
//                            $perPage,
//                            Paginator::resolveCurrentPage(),
//                            ['path' => Paginator::resolveCurrentPath()]
//                        );
//
//                        $classLists = $paginate;
//
//                        return view('myPage.student.my_stu_list', ['classLists' => $classLists]);
//                    }
//                }
//            }else{
//                $errors = $validator->errors();
//
//                if ($errors->has('content')) {
//                    return view('myPage.student.my_stu_list', ['classLists' => $classLists])->withErrors(['content_length' => '검색어를 두자 이상 입력하세요.']);
//                }
//            }
//        }
        return view('myPage.student.my_stu_list', ['classLists' => $paginate]);
    }

    public function myStuListDetail(Request $request, $classListId){
        $classList = ClassList::find($classListId);
        $classObject = ClassObject::find($classList->classObjectId);
        $classApply = ClassApply::where('classObjectId', $classObject->id)->with('classObject')->first();
//        return dd($classApply);


        return view('myPage.student.my_stu_list_detail',['classObject' => $classObject, 'classApply' => $classApply]);
    }




    public function proLogFetch (Request $request) {
        $before = date('Y-m-d H:i:s', strtotime('now - 1 DAY'));
        $after = date('Y-m-d H:i:s', strtotime('now'));

        $proLog = ProLog::whereBetween('created_at', [$before, $after])->where('targetId', Auth::id())->orderBy('created_at', 'desc')->get();

        return $proLog;
    }
    public function actLogFetch (Request $request) {
        $before = date('Y-m-d H:i:s', strtotime('now - 1 DAY'));
        $after = date('Y-m-d H:i:s', strtotime('now'));

        $user = Auth::user();
        $classObjectIds = [];
        $actLog = [];
        if ($user->authority == 2) {
            $classObjectIds = MyClass::select('classObjectId')->where('userId', $user->id)->get()->map(function ($value) {
                return $value->classObjectId;
            })->toArray();

            $actLog = ActLog::whereBetween('created_at', [$before, $after])->whereIn('classObjectId', $classObjectIds)->orderBy('created_at', 'desc')->get();
        }
        else if ($user->authority == 1) {
            $classObjectIds = ClassList::select('classObjectId')->where('userId', $user->id)->get()->map(function ($value) {
                return $value->classObjectId;
            })->toArray();

            $teamIds = TeamMember::select('teamId')->where('userId', $user->id)->get()->map(function ($value) {
                return $value->teamId;
            })->toArray();

            $teamIds = array_merge([null], $teamIds);

            $actLog = ActLog::whereBetween('created_at', [$before, $after])->whereIn('classObjectId', $classObjectIds)->whereIn('teamId', $teamIds)->orderBy('created_at', 'desc')->get();
        }

        return $actLog;
    }

    public function myStuCom(Request $request){
        return view('myPage.student.my_stu_com');
    }

    public function myStuComApply(Request $request){
        $competition = Competition::find($request['competitionId']);
        if (!$competition) {
            return false;
        }

        if (strtotime($competition->year.'-'.$competition->month.'-'.$competition->day) < strtotime(date('Y-m-d'))) {
            return false;
        }

        if ($competition->is_applied()) {
            return false;
        }

        $competitionApply = CompetitionApply::create([
            'userId' => Auth::id(),
            'competitionId' => $request['competitionId'],
            'state' => 1,
        ]);

        $competitionApply->user = $competitionApply->user();

        return ['competitionApply' => $competitionApply, 'day' => $competition->day];
    }

    public function myStuComReload(Request $request) {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $week = date("w");
        $weeks = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];

        $validator = Validator::make($request->only('year', 'month'), [
            'year' => ['required', 'integer', 'min:1900', 'max:2500'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        if (!$validator->fails()) {
            $year = $request['year'];
            $month = $request['month'];
            $day = 1;

            $week = date('w', strtotime($year.'-'.$month.'-'.$day));
        }

        $competitions = Competition::where('year', $year)->where('month', $month)->with(['competitionApplies' => function ($query) {
            $query->where('userId', Auth::id());
        }])->get();

        $days = $competitions->groupBy('day');

        return ['days' => $days, 'year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week]];
    }

    public function basicLists (Request $request, $year, $month) {
        if (Auth::user()->authority != 2) {
           return false;
        }

        $validator = Validator::make(['year' => $year, 'month' => $month], [
            'year' => ['required', 'integer', 'min:1900', 'max:2500'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        if ($validator->fails()) {
            return false;
            $year = date("Y");
            $month = date("m");
        }

        $basics = Basic::whereBetween('startDateTime',
            [date('Y-m-d H:i:s', strtotime("$year-$month-01")), date('Y-m-d H:i:s', strtotime("$year-$month-01 + 1 MONTH - 1 SECONDS"))]
        )
//            ->withCount('applier')
            ->withCount('isAccepted')
            ->with('isAccepted')
//            ->with('consultant')
            ->orderBy('startDateTime', 'asc')->get()
            ->map(function ($basic) {
                if (strtotime($basic->endDateTime) < strtotime('now')) {
                    $basic->state = false;
                }
                else {
                    $basic->state = true;
                }

                return $basic;
            })
            ->groupBy(function ($item) {
            return (int)date('d', strtotime($item->startDateTime));
        });

        return [
            'isBasicTarget' => Auth::user()->basicTarget ? true : false,
            'year' => $year,
            'month' => $month,
            'basics' => $basics,
        ];
    }

    public function basicApply (Request $request, $basicId) {
        if (Auth::user()->authority != 2) {
            return false;
        }

        if (!($request->has('year') && $request->has('semester'))) {
            return false;
        }

        $myPage = MyPage::where('userId', Auth::id())->where('year', $request->only('year'))->where('semester', $request->only('semester'))->first();
        if (!$myPage) {
            return false;
        }

        $card = Card::where('userId', Auth::id())->where('type', 1)->where('myPageId', $myPage->id)->first();
        if (!$card) {
            return false;
        }

        $basic = Basic::where('id', $basicId)->where('startDateTime', '>=' , date('Y-m-d', strtotime('now')))->first();
        if (!$basic) {
            return false;
        }

//        if ($basic->applier()->count() >= $basic->maxMemberCount) {
//            return false;
//        }

        $apply = BasicApply::where('userId', Auth::id())->where('basicId', $basic->id)->first();
        if ($apply) {
            return false;
        }

        $basicApply = BasicApply::create([
            'userId' => Auth::id(),
            'basicId' => $basic->id,
            'state' => Auth::user()->basicTarget,
        ]);

        $item = new Item([
            'title' => $basic->title." (".date('m.d H:i', strtotime($basic->startDateTime))." ~ ".date('m.d H:i', strtotime($basic->endDateTime)).")",
            'userId' => Auth::id(),
            'activationImage' => false,
            'type' => 1001,
            'basicApplyId' => $basicApply->id,
        ]);

        $card->createItem()->save($item);
        $card->itemsNum = array_merge([$item->id], $card->itemsNum ? $card->itemsNum : []);
        $card->save();

        $adminLog = AdminLog::create([
            'userId' => Auth::id(),
            'userName' => Auth::user()->name,
            'authority' => Auth::user()->authority,
            'eventTitle' => $basic->title,
            'eventType' => '21'
        ]);

        broadcast(
            new AdminLogEvent($adminLog->toArray())
        );

        return $item;
        return true;
    }

    public function consultingLists (Request $request, $year, $month) {
        if (Auth::user()->authority != 2) {
           return false;
        }

        $validator = Validator::make(['year' => $year, 'month' => $month], [
            'year' => ['required', 'integer', 'min:1900', 'max:2500'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        if ($validator->fails()) {
            return false;
            $year = date("Y");
            $month = date("m");
        }

        $consultings = Consulting::whereBetween('startDateTime',
            [date('Y-m-d H:i:s', strtotime("$year-$month-01")), date('Y-m-d H:i:s', strtotime("$year-$month-01 + 1 MONTH - 1 SECONDS"))]
        )
            ->withCount('applier')
            ->withCount('isAccepted')
            ->with('consultant')
            ->orderBy('startDateTime', 'asc')
            ->get()
            ->map(function ($consulting) {
                if (strtotime($consulting->endDateTime) < strtotime('now')) {
                    $consulting->state = false;
                }
                else {
                    $consulting->state = true;
                }

                return $consulting;
            })
            ->groupBy(function ($item) {
            return (int)date('d', strtotime($item->startDateTime));
        });

//        $myClasses = MyClass::where('userId', Auth::id())->with('withClassApply')->get()
//            ->map(function ($class) {
//            if ($class->withClassApply) {
//                if ($class->withClassApply->classObjectId) {
//                    return $class->withClassApply;
//                }
//            }
//            return null;
//        })->filter(function ($classApply) {
//            return $classApply;
//        });

        return [
            'isConsultingTarget' => Auth::user()->consultingTarget ? true : false,
            'year' => $year,
            'month' => $month,
            'consultings' => $consultings,
//            'myClasses' => $myClasses,
        ];
    }

    public function consultingApply (Request $request, $consultingId, $classObjectId) {
        if (Auth::user()->authority != 2) {
            return false;
        }

        if (!($request->has('year') && $request->has('semester'))) {
            return false;
        }

        $myPage = MyPage::where('userId', Auth::id())->where('year', $request->only('year'))->where('semester', $request->only('semester'))->first();
        if (!$myPage) {
            return false;
        }

        $card = Card::where('userId', Auth::id())->where('type', 2)->where('myPageId', $myPage->id)->first();
        if (!$card) {
            return false;
        }

        $consulting = Consulting::where('id', $consultingId)->where('startDateTime', '>=' , date('Y-m-d', strtotime('now')))->first();
        if (!$consulting) {
            return false;
        }

        if ($consulting->applier()->count() >= 1) {
            return false;
        }

        $apply = ConsultingApply::where('userId', Auth::id())->where('consultingId', $consulting->id)->first();
        if ($apply) {
            return false;
        }

        $myClass = MyClass::where('userId', Auth::id())->where('classObjectId', $classObjectId)->first();
        if (!$myClass) {
            return false;
        }

        $ca = ConsultingApply::create([
            'userId' => Auth::id(),
            'consultingId' => $consulting->id,
            'state' => Auth::user()->consultingTarget,
            'classObjectId' => $myClass->classObjectId,
        ]);

        $item = new Item([
            'title' => $consulting->title." (".date('m.d H:i', strtotime($consulting->startDateTime))." ~ ".date('m.d H:i', strtotime($consulting->endDateTime)).")",
            'userId' => Auth::id(),
            'activationImage' => false,
            'consultingApplyId' => $ca->id,
            'type' => 1002,
        ]);

        $card->createItem()->save($item);
        $card->itemsNum = array_merge([$item->id], $card->itemsNum ? $card->itemsNum : []);
        $card->save();

        $adminLog = AdminLog::create([
            'userId' => Auth::id(),
            'userName' => Auth::user()->name,
            'authority' => Auth::user()->authority,
            'eventTitle' => $consulting->title,
            'eventType' => '31'
        ]);

        broadcast(
            new AdminLogEvent($adminLog->toArray())
        );


        return $item;
//        return true;
    }

    public function consultingApplyModi (Request $request, $consultingApplyId, $classObjectId) {
        if (Auth::user()->authority != 2) {
            return false;
        }

        $card = Card::where('userId', Auth::id())->where('type', 2)->first();
        if (!$card) {
            return false;
        }

//        $consulting = Consulting::where('id', $consultingId)->where('startDateTime', '>=' , date('Y-m-d', strtotime('now')))->first();
//        if (!$consulting) {
//            return false;
//        }

        $consultingApply = ConsultingApply::find($consultingApplyId);
        if (!$consultingApply) {
            return false;
        }

        if ($consultingApply->state) {
            return false;
        }

        if ($consultingApply->userId != Auth::id()) {
            return false;
        }

        $myClass = MyClass::where('classObjectId', $classObjectId)->where('userId', Auth::id())->first();
        if (!$myClass) {
            return false;
        }

        $consultingApply->classObjectId = $myClass->classObjectId;
        $consultingApply->save();

//        if ($consulting->applier()->count() >= 1) {
//            return false;
//        }

//        $apply = ConsultingApply::where('userId', Auth::id())->where('consultingId', $consulting->id)->first();
//        if ($apply) {
//            return false;
//        }

//        $myClass = MyClass::where('userId', Auth::id())->where('classObjectId', $classObjectId)->first();
//        if (!$myClass) {
//            return false;
//        }

//        $ca = ConsultingApply::create([
//            'userId' => Auth::id(),
//            'consultingId' => $consulting->id,
//            'state' => 0,
//            'classObjectId' => $myClass->classObjectId,
//        ]);
//
//        $item = new Item([
//            'title' => $consulting->title,
//            'userId' => Auth::id(),
//            'activationImage' => false,
//            'consultingApplyId' => $ca->id,
//            'type' => 1002,
//        ]);
//
//        $card->createItem()->save($item);
//        $card->itemsNum = array_merge([$item->id], $card->itemsNum ? $card->itemsNum : []);
//        $card->save();
//
//        $adminLog = AdminLog::create([
//            'userId' => Auth::id(),
//            'userName' => Auth::user()->name,
//            'authority' => Auth::user()->authority,
//            'eventTitle' => $consulting->title,
//            'eventType' => '31'
//        ]);
//
//        broadcast(
//            new AdminLogEvent($adminLog->toArray())
//        );

        return true;
//        return true;
    }

    public function consultantMyPageView (Request $request) {
        if (Auth::user()->authority != 4) {
            return redirect()->route('myPageView');
        }

//        $basics = Basic::where('consultantId', Auth::id())->get();
        $basics = Basic::all();

        $data = [];

        foreach ($basics as $basic) {
            $data = array_merge($data, $basic->admissionApplies()->get()->toArray());
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

        return view('myPage.consultant.con_basic_my_page', ['basicApplies' => $paginate]);
    }

    public function consultantMyPageConsultingView (Request $request) {
        if (Auth::user()->authority != 4) {
            return redirect()->route('myPageView');
        }

        $consultings = Consulting::where('consultantId', Auth::id())->get();

        $data = [];

        foreach ($consultings as $consulting) {
            $data = array_merge($data, $consulting->admissionApplies()->get()->toArray());
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

        return view('myPage.consultant.con_consulting_my_page', ['consultingApplies' => $paginate]);
    }

    public function consultantMyPageConsultingDetailView (Request $request, $applyId) {
        if (Auth::user()->authority != 4) {
            return redirect()->route('myPageView');
        }

        $consultingApply = ConsultingApply::find($applyId);
        if (!$consultingApply) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        $userId = $consultingApply->userId;

        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        else if ($user->authority != 2) {
            return false;
        }

        $consulting = $consultingApply->consulting()->first();
        if (!$consulting) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        if ($consulting->consultantId != Auth::id()) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        if (strtotime($consulting->startDateTime) > strtotime('now')) {
            return redirect()->route('consultantMyPageConsultingView');
        }
        if (strtotime($consulting->endDateTime) < strtotime('now')) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        return view('myPage.consultant.con_consulting_detail_my_page', ['applyId' => $consultingApply->id]);
    }

    public function oldApply(Request $request, $mode){
        $user = Auth::user();
        if ($user->authority != 2) {
            return false;
        }

        $validator = Validator::make(['mode' => $mode], [
            'mode' => ['required', 'integer', 'min:1', 'max:2'],
        ]);

        if ($validator->fails()) {
            return false;
        }

        $cardIds = Card::where('userId', $user->id)->where('type', 0)->get()->map(function ($item) {
            return $item->id;
        });

        $itemIds = Item::whereIn('cardId', $cardIds)->where('type', 4)->get()->map(function ($item) {
            return $item->id;
        });

        $classApplies = ClassApply::whereIn('itemId', $itemIds)->where('mode', $mode)->get();

        return $classApplies;
    }

    public function fetchApply(Request $request, $applyId){
        $user = Auth::user();
        if ($user->authority != 2) {
            return false;
        }

        $classApply = ClassApply::find($applyId);
        if (!$classApply) {
            return false;
        }

        $item = $classApply->item();
        if (!$item) {
            return false;
        }

        $card = $item->card();

        if ($card->userId == $user->id) {
            return $classApply;
        }
        else {
            return false;
        }
    }
}
