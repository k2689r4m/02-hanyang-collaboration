<?php

namespace App\Http\Controllers;

use http\Params;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//이렇게 선언하고 사용
use Illuminate\Support\Facades\Validator;

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
use App\Models\TeamMaster;
use App\Models\Card;
use App\Models\Item;
use App\Models\ReflectionLog;
use App\Models\ProblemAnalysis;
use App\Models\ClassApply;
use App\Models\MyPage;
use App\Models\Calendar;
use App\Models\Expert;
use App\Models\ClassManager;
use App\Models\Daehak;


use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;


use Illuminate\Support\Arr;



class LectureController extends Controller
{
    //test
    public function testClassAdd (Request $request) {
        $myClass = MyClass::whereNotNull('classObjectId')->get()->map(function ($item) {
            return $item->classObjectId;
        })->toArray();

        $classObjects = ClassObject::select('gwamokNm', 'suupNo', 'suupYear', 'suupTermNm')->whereNotIn('id', $myClass)->get();

        $users = User::select('id', 'name', 'email', 'social')->where('authority', 1)->get();

        return view('lecture.test_class_add', ['classObjects' => $classObjects, 'users' => $users]);
    }

    public function testClassAdd2 (Request $request) {
        if (Auth::user()->authority != 2) {
            return redirect()->back()->withErrors(['auth' => '수업 개설 권한이 없습니다.']);
        }

        $validator = Validator::make($request->all(), [
            'korName' => ['required', 'string'],
//            'department' => ['required', 'string'],
            'suupNo' => ['required', 'integer'],
            'ids' => ['array'],
            'ids.*' => ['integer'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $classObject = ClassObject::where('suupNo', $request['suupNo'])->first();
        if (!$classObject) {
            return redirect()->back()->withErrors(['suupNo' => '존재하지 않는 수업번호입니다.']);
        }
        $myClass = MyClass::where('classObjectId', $classObject->id)->first();
        if ($myClass) {
            return redirect()->back()->withErrors(['suupNo' => '이미 존재하는 과목입니다.']);
        }

        $myPage = MyPage::where('userId', Auth::id())->where('year', $classObject->suupYear)->where('semester', ((int)$classObject->suupTerm - 5) / 5)->first();
        if (!$myPage) {
            $myPage = MyPage::create([
                'userId' => Auth::id(),
                'year' => $classObject->suupYear,
                'semester' => ((int)$classObject->suupTerm - 5) / 5
            ]);

            $card = Card::where('myPageId', $myPage->id)->where('type', 0)->first();
            if (!$card) {
                if (Auth::user()->authority == 2) {
                    $card = Card::create([
                        'userId' => Auth::id(),
                        'myPageId' => $myPage->id,
                        'type' => 0,
                        'title' => '개설신청',
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

        $card = Card::where('myPageId', $myPage->id)->where('type', 0)->first();
        if (!$card) {
            $card = Card::create([
                'userId' => Auth::id(),
                'myPageId' => $myPage->id,
                'type' => 0,
                'title' => '개설신청',
            ]);
        }

        $item = new Item([
            'title' => $request['korName'],
            'label' => '0',
            'userId' => Auth::id(),
            'type' => '4',
        ]);
        $card->items3()->save($item);

        $card->itemsNum = array_merge([$item->id], $card->itemsNum);
        $card->save();

//        $classObject = ClassObject::create([
//            'suupTermNm' => '1학기',
//            'pyegangYn' => '0',
//            'suupTimes' => date('Y-m-d H:i:s', strtotime('now')),
//            'sincheongInwon' => '1',
//            'hakjeom' => '1',
//            'gnjHakgwaNm' => $request['department'],
//            'isuGrade' => '1',
//            'suupYear' => '2021',
//            'suupTerm' => '10',
//            'daepyoGangsaNm' => Auth::user()->name,
//            'gnjSosokNm' => $request['department'],
//            'gwamokNm' => $request['korName'],
//            'daepyoGangsaHakgwa' => $request['department'],
//            'suupTypeGb' => '1',
//            'onlineGb' => '1',
//            'haksuNo' => '1',
//            'suupNo' => $request['suupNo'],
//            'gnjDaehakNm' => '1',
//            'gyogangsa' => Auth::user()->name,
//            'teuksuSuupInfo' => '1',
//            'isuGbNm' => '1',
//            'campusCd' => '1',
//        ]);

        $classApply = new ClassApply([
            'itemId' => $item->id,
            'state' => 'complete',
            'code' => $classObject->suupNo,
            'mode' => '2',
            'type' => '1',
            'grade' => '1',
            'size1' => '3',
            'size2' => '100',
            'proSize1' => '1',
            'department' => $classObject->gnjSosokNm,
            'special' => '1',
            'special2' => '1',
            'special3' => '1',
            'special4' => '1',
            'korName' => $request['korName'],
            'engName' => 'test',
            'gradesPoint' => '1',
            'lecturePoint' => '1',
            'exercisePoint' => '1',
            'description' => '1',
            'meca' => '1',
            'agency' => '1',
            'expert' => '1',
            'role1' => '1',
            'role3' => '0',
            'role4' => '0',
            'role5' => '0',
            'expected1' => true,
            'expected2' => false,
            'expected3' => false,
            'expected4' => false,
            'expected5' => false,
            'expected6' => false,
            'expected7' => false,
//            'aplName' => Auth::user()->name,
//            'aplSign' => '1',
//            'aplOrg' => '1',
//            'aplTel' => '1',
//            'aplPhone' => '1',
//            'aplEmail' => '1',
            'applicant' => [["org" => "1", "tel" => "1", "name" => Auth::user()->name, "email" => "1@1.1", "phone" => "1"]],
            'agree1' => true,
            'duration' => date('Y-m-d', strtotime('now')),
            'duration2' => date('Y-m-d', strtotime('now')),
            'basic1' => '1',
            'basic2' => '1',
            'basic3' => '1',
            'basic4' => '1',
            'basic5' => '1',
            'basic6' => '1',
            'basicPlan' => '1',
            'sceContent' => '1',
            'sceGoal' => '1',
            'sceTitle' => '1',
            'sceRole' => '1',
            'sceDetail' => '1',
            'planDetail' => json_decode('[{"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}, {"level": "", "content": "", "method1": 0, "method2": 0, "method3": 0, "subject": "", "stuContent": ""}]'),
        ]);

        $classObject->refresh();

        $classObject->withClassApply()->save($classApply);

        $myClass = MyClass::create([
            'userId' => Auth::id(),
            'classObjectId' => $classObject->id,
        ]);

//        $users = User::where('authority', 1)->get();
        $users = User::whereIn('id', $request['ids'] ? $request['ids'] : [])->get();

        foreach ($users as $user) {
            ClassList::create([
                'userId' => $user->id,
                'classObjectId' => $classObject->id,
                'name' => $user->name,
            ]);
        }

        $teams = [];
        foreach (range(1,2) as $team) {
            $teams[] = new Team([
                'name' => "팀$team",
            ]);
        }
        $myClass->teams2()->saveMany($teams);

        return redirect()->route('lectureList');
    }

    public function lectureList (Request $request) {
        $time = \Carbon\Carbon::now();
        $year = $request->has('year') ? $request['year'] : null;
        $semester = $request->has('semester') ? $request['semester'] : null;

        $validator = Validator::make($request->all(), [
            'year' => ['required', 'integer'],
            'semester' => ['required', 'integer', 'in:10,15,20,25'],
            'content' => ['string', 'min:2'],
        ]);

        $content = $request['content'];

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('content')) {
                $content = null;
//                return redirect()->back()->withErrors(['content' => '검색어의 길이는 최소 2글자 입니다.']);
            }
            if ($errors->has('year')) {
                $year = $time->format('Y');
            }
            if ($errors->has('semester')) {
                $semester = ($time->format('n') > 6 ? '20' : '10');
            }
        }

        $perPage = 10;

        $user = Auth::user();
        $applies = [];
        $daehaks = Daehak::all();
        if ($user->authority == 2) {
            $applies = Card::where('userId', $user->id)
                ->where('type', 0)
                ->orderBy('id', 'desc')
                ->with('classApplyItems')
                ->with(['withMyPage' => function ($query) use($year, $semester) {
                    $query->where('year', $year)->where('semester', ($semester - 5) / 5);
                }])
                ->get()->filter(function ($card) {
                    return $card->classApplyItems->count() && $card->withMyPage;
                })->map(function ($card) {
                    return $card->classApplyItems->filter(function ($item) {
                        return $item->classApplyObject;// && $item->classApplyObject->classObject;
                    });
                })
                ->flatMap(function ($values) {
                    return $values;
                })
                ->map(function ($item) {
                    return $item->classApplyObject;
                });
        }
        else if ($user->authority == 5) {
            $classObjectIds = Expert::where('userId', Auth::id())->get()->map(function ($item) {
                return $item->classObjectId;
            });

            $applies = ClassApply::whereIn('classObjectId', $classObjectIds)->with('classObject')->get();
        }
        else if ($user->authority == 6) {
            $classObjectIds = ClassManager::where('userId', Auth::id())->get()->map(function ($item) {
                return $item->classObjectId;
            });

            $applies = ClassApply::whereIn('classObjectId', $classObjectIds)->with('classObject')->get();
        }

        $applies = $applies->filter(function ($item, $key) use($applies, $content, $year, $semester, $daehaks) {
            $item->year = $year."년";
            $item->semester = '';
            switch ($semester) {
                case 10:
                    $item->semester = "1학기";
                    break;
                case 15:
                    $item->semester = "여름학기";
                    break;
                case 20:
                    $item->semester = "2학기";
                    break;
                case 25:
                    $item->semester = "겨울학기";
                    break;
                default:
                    $item->year = '';
            }

            $daehak = $daehaks->where('department', $item->department)->first();
            if ($daehak) {
                $item->gnjDaehakNm = $daehak->name;
            }

            if ($item->classObject) {
//                $item->korName = $item->classObject->gwamokNm;
                $item->department = $item->classObject->gnjHakgwaNm;
                $item->department = $item->classObject->gnjHakgwaNm;
                $item->year = $item->classObject->suupYear.'년';
                switch ($item->classObject->suupTerm) {
                    case 10:
                        $item->semester = "1학기";
                        break;
                    case 15:
                        $item->semester = "여름학기";
                        break;
                    case 20:
                        $item->semester = "2학기";
                        break;
                    case 25:
                        $item->semester = "겨울학기";
                        break;
                    default:
                        $item->year = '';
                }
                $item->gnjDaehakNm = $item->classObject->gnjDaehakNm;
            }

            if ($content && !str_contains($item->korName, $content)) {
                return false;
            }
            return true;
        });

        $paginate = new LengthAwarePaginator(
            $applies->forPage(Paginator::resolveCurrentPage(), $perPage),
            $applies->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );


//        $this->completeCernvert($paginate);

        return view('./lecture/lecture_list',['applies' => $paginate, 'content' => $content, 'year' => $year, 'semester' => $semester]);
    }

    private function completeCernvert ($applies){

        
//        $time = \Carbon\Carbon::now();
//        $year = $time->format('Y');
//        $term = $time->format('n');
//        $term = 8;
//        if($term > 7){
//            $term = 20;
//        }else{
//            $term = 10;
//        }
//
//        $count = 0;
//        foreach ($applies as $apply){
//            if($apply->state == 'complete' && $apply->classObjectId != ''){
////                return dd($year = (string)$apply->classObject->suupYear);
//                if($year > (string)$apply->classObject->suupYear || $term > (string)$apply->classObject->suupTerm){
//                    $update = ClassApply::find($apply->id)->update(['state' => 'end']);
//                    $count += 1;
//                }
//            }
//        }

    }



//    public function lectureDetailInfoBranch(Request $request,$classApplyId){
//        $classApply = ClassApply::where('id',$classApplyId)->first();
//
//        if ($classApply->state == 'complete' || 'idle' || 'ing') {
////            return route('lectureDetailInfo', ['classApplyId' => $classApplyId]);
//            return view('lectureDetailInfo', ['classApplyId' => $classApply->id]);
//        }
//        elseif($classApply->state == 'wait'){
////            return route('lectureDetailBeforeApproval', ['classApplyId' => $classApplyId]);
//            return view('lectureDetailBeforeApproval', ['classApplyId' => $classApply->id]);
//        }
//    }

    //정보
    public function lectureDetailInfo (Request $request, $classApplyId){
        $classApply = ClassApply::find($classApplyId);
//        return dd($classApply);
        if(!$classApply){
            return redirect()->back();
        }

        if($classApply->state == 'wait'){
            if ($classApply->item()->card()->userId != Auth::id()) {
                return redirect()->back();
            }

//            $suupNumbers = ClassObject::select('gwamokNm', 'suupNo')->where('daepyoGangsaNo', Auth::user()->gaeinNo)->with('myClass')->get();
            //임시 코드
//            $classApplyIds = ClassApply::whereNotNull('classObjectId')->get()->map(function ($item) {
//                return $item->classObjectId;
//            });

//            $myClassObjects = ClassObject::select('gwamokNm', 'suupNo')->where('daepyoGangsaNo', Auth::user()->gaeinNo)->get()->filter(function ($item) use($classApplyId) {
//                $classApplies = ClassApply::where('code', $item->suupNo)->where('id', '!=', $classApplyId)->first();
//                return !$item->myClass && !$classApplies;
//            });
//            $suupNumbers = ClassObject::select('gwamokNm', 'suupNo')->whereNotIn('id', $classApplyIds)->take(10)->get()->filter(function ($item) use($classApplyId) {
//                $classApplies = ClassApply::where('code', $item->suupNo)->where('id', '!=', $classApplyId)->first();
//                return !$item->myClass && !$classApplies;
//            })->whereNotIn('suupNo', $myClassObjects->map(function ($item) {
//                return $item->suupNo;
//            }));

            //임시코드 엔드
//            $suupNumbers = $suupNumbers

            return view('./lecture/lecture_detail_before_approval',['classApply' => $classApply]);
//            return view('./lecture/lecture_detail_before_approval',['classApply' => $classApply, 'suupNumbers' => $suupNumbers, 'myClassObjects' => $myClassObjects]);
        }elseif($classApply->state == 'complete'){
            if ($classApply->item()->card()->userId != Auth::id()) {
                return redirect()->back();
            }
            $myPage = $classApply->item()->card()->myPage();
            if (!$myPage) {
                return redirect()->back();
            }
            $classApplyIds = ClassApply::whereNotNull('classObjectId')->get()->map(function ($item) {
                return $item->classObjectId;
            });
//            $classApply->load('classObject');

            $myClassObjects = ClassObject::select('gwamokNm', 'suupNo')->where('daepyoGangsaNo', Auth::user()->gaeinNo)->where('suupYear', $myPage->year)->where('suupTerm', $myPage->semester * 5 + 5)->get()->filter(function ($item) use($classApplyId) {
                $classApplies = ClassApply::where('code', $item->suupNo)->where('id', '!=', $classApplyId)->first();
                return !$item->myClass && !$classApplies;
            });
//            $suupNumbers = ClassObject::select('gwamokNm', 'suupNo')->whereNotIn('id', $classApplyIds)->take(10)->get()->filter(function ($item) use($classApplyId) {
//                $classApplies = ClassApply::where('code', $item->suupNo)->where('id', '!=', $classApplyId)->first();
//                return !$item->myClass && !$classApplies;
//            })->whereNotIn('suupNo', $myClassObjects->map(function ($item) {
//                return $item->suupNo;
//            }));
            if($classApply->classObjectId){
                return view('./lecture/lecture_detail_info',['classApply' => $classApply]);
            }else{
                return view('./lecture/lecture_detail_before_approval',['classApply' => $classApply, 'myClassObjects' => $myClassObjects]);//, 'suupNumbers' => $suupNumbers]);
            }


        }else{
//            $classApplyIds = ClassApply::whereNotNull('classObjectId')->get()->map(function ($item) {
//                return $item->classObjectId;
//            });
            $classApply->load('classObject');
//            $myClassObjects = ClassObject::select('gwamokNm', 'suupNo')->where('daepyoGangsaNo', Auth::user()->gaeinNo)->get()->filter(function ($item) use($classApplyId) {
//                $classApplies = ClassApply::where('code', $item->suupNo)->where('id', '!=', $classApplyId)->first();
//                return !$item->myClass && !$classApplies;
//            });
//            $suupNumbers = ClassObject::select('gwamokNm', 'suupNo')->whereNotIn('id', $classApplyIds)->take(10)->get()->filter(function ($item) use($classApplyId) {
//                $classApplies = ClassApply::where('code', $item->suupNo)->where('id', '!=', $classApplyId)->first();
//                return !$item->myClass && !$classApplies;
//            })->whereNotIn('suupNo', $myClassObjects->map(function ($item) {
//                return $item->suupNo;
//            }));
            return view('./lecture/lecture_detail_info',['classApply' => $classApply]);

        }
    }

    public function lectureInfoCodeUpdate(Request $request, $classApplyId){
        $classApply = ClassApply::find($classApplyId);
        if (!$classApply) {
            return redirect()->back();
        }

        if ($classApply->item()->card()->userId != Auth::id()) {
            return redirect()->back();
        }

        if($classApply->code !== $request['lectureCode'] && $classApply->code !== null){
            return redirect()->back()->withErrors(['error' => '한번 등록된 코드는 변경이 불가합니다.']);
        }
        //진행중으로 바뀌면 안됨
        //$target->state = 'ing';

        $target = ClassApply::find($classApplyId);

        //
//        if($target->state != 'complete'){
//            $target->state = 'complete';
//        }
        $target->code = $request['lectureCode'];
//        $target = ClassApply::find($classApply->id);

        $classObject = ClassObject::where('suupNo', $target->code)->first();
        if (!$classObject) {
            return redirect()->route('lectureDetailInfo')->withErrors(['error' => '코드에 맞는 과목이 없습니다.']);
        }

        $myPage = $target->item()->card()->myPage();
        if (!$myPage) {
            return redirect()->route('lectureDetailInfo')->withErrors(['error' => '잘못된 요청입니다.']);
        }
        if ($classObject->suupYear != $myPage->year) {
            return redirect()->route('lectureDetailInfo')->withErrors(['error' => '잘못된 요청입니다.']);
        }
        if ($classObject->suupTerm != $myPage->semester * 5 + 5) {
            return redirect()->route('lectureDetailInfo')->withErrors(['error' => '잘못된 요청입니다.']);
        }

        $target->classObjectId = $classObject->id;
        $myClass = MyClass::create([
            'userId' =>  $target->item()->userId,
            'classObjectId' =>  $classObject->id,
        ]);

//        $team = Team::where('classObjectId', $classObject->id)->count();
        foreach (range(1, 2) as $index) {
            Team::create([
                'name' => "팀$index",
                'classObjectId' => $classObject->id,
            ]);
        }

        $target->save();
//        if($target->code != $request['lectureCode'] && $target->code == null ){
//            return dd('asd');
//        }
//        $update = ClassApply::where('id', $classApplyId)->update(['code' => $request['lectureCode']]);

        return redirect()->route('lectureDetailInfo', ['classApplyId' => $classApplyId])->withErrors(['error' => '저장되었습니다.']);
    }

    public function lectureDetailBeforeApprovalCodeUpdate (Request $request, $classApplyId) {
        $classApply = ClassApply::find($classApplyId);
        if (!$classApply) {
            return redirect()->back();
        }

        if ($classApply->item()->card()->userId != Auth::id()) {
            return redirect()->back();
        }

//        $update = ClassApply::where('id', $classApplyId)->update(['code' => $request['lectureCode']]);
        return redirect()->route('lectureDetailInfo', ['classApplyId' => $classApplyId]);
    }

//    public function lectureDetailBeforeApproval (Request $request, $classApplyId){
//        $classApply = ClassApply::find($classApplyId);
//        return view('./lecture/lecture_detail_before_approval',['classApply' => $classApply]);
//    }

//    public function lectureDetailCodeRegister (Request $request){
//        $update = ClassApply::where('id', )->update(['code' => $request['lectureCode']]);
//        if(1){
//            return view('./lecture/lecture_detail_info',['classApply' => $classApply]);
//        }else{
//            return view('./lecture/lecture_detail_before_approval',['classApply' => $classApply]);
//        }
//
//    }



    public function lectureDetailMember (Request $request, $classObjectId) {
//        $classObject = ClassObject::where('id', $classObjectId)->first();
        $perPage = 10;
        $classObject = ClassObject::find($classObjectId);
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

                $classApply = ClassApply::where('classObjectId', $classObjectId)->first();

                return view('./lecture/lecture_detail_member',
                    ['classObjectId' => $classObjectId, 'classLists' => $paginate,  'classApply' => $classApply, 'classObject' => $classObject] );
            }
            else {
                $errors = $validator->errors();

                if ($errors->has('content')) {
                    return redirect()->route('lectureDetailMember', ['classObjectId' => $classObjectId])->withErrors(['content_length' => '검색어를 두자 이상 입력하세요.']);
                }
                return redirect()->back()->withErrors($validator->errors());
            }
        }

//        return dd($classObject);
        $classLists = ClassList::whereNotNull('userId')->where('classObjectId', $classObjectId)->paginate($perPage);
//        $classLists = $classObject->withClassLists()->paginate($perPage);
        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();
        return view('./lecture/lecture_detail_member',
            ['classObjectId' => $classObjectId, 'classLists' => $classLists,  'classApply' => $classApply, 'classObject' => $classObject] );
    }


    //팀배정
    public function lectureDetailTeamSelect (Request $request, $classObjectId) {

        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();

        return view('./lecture/lecture_detail_teamselect',
            ['classObjectId' => $classObjectId, 'classObjectName' => $classObjectName, 'classApply' => $classApply]);
    }

    public function teamMaster (Request $request, $userId, $teamId) {
        $user = User::find($userId);
        if (!$user) {
            return redirect()->back()->withErrors(['message' => '존재하지 않는 학생입니다.']);
        }
        
        $team = Team::find($teamId);
        if (!$team) {
            return redirect()->back()->withErrors(['message' => '존재하지 않는 팀입니다.']);
        }

        $myClass = MyClass::where('classObjectId', $team->classObjectId)->first();
        if (!$myClass) {
            return redirect()->back()->withErrors(['message' => '존재하지 않는 수업입니다.']);
        }
        if ($myClass->isMember($user->id)) {
            return redirect()->back()->withErrors(['message' => '수강중인 학생이 아닙니다.']);
        }

        $teamIds = Team::where('classObjectId', $team->classObjectId)->get()->map(function ($team) {
            return $team->id;
        });

        TeamMaster::where('userId', $userId)->where('classObjectId', $team->classObjectId)->delete();

        $tm = TeamMaster::where('teamId', $team->id)->first();
        if ($tm) {
            $tm->userId = $user->id;
            $tm->save();
        }
        else {
            TeamMaster::create([
                'userId' => $user->id,
                'teamId' => $team->id,
                'classObjectId' => $team->classObjectId,
            ]);
        }

        return redirect()->back()->withErrors(['message' => '설정되었습니다.']);
    }


    //문제분석
    public function lectureDetailProblem (Request $request, $classObjectId) {
        $classObject = ClassObject::where('classObjectId',$classObjectId);
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
                    $item = $item->withProblemAnalysis;
                    $item->teamId = $card->teamId;
                    if (is_null($item->teamId)) {
                        $item->teamName = '';
                    }
                    else {
                        $team = Team::find($item->teamId);
                        if (!$team) {
                            $item->teamId = null;
                            $item->teamName = '';
                        }
                        else {
                            $item->teamName = $team->name;
                        }
                    }

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
        $classApply = ClassApply::where('classObjectId' , $classObjectId)->first();
        return view('./lecture/lecture_detail_problem', [ 'classObject' => $classObject, 'problems' => $paginate , 'classApply' => $classApply]);
    }
    /////////////////////////////////////////////////////////////////


    //////////////////////////////////////////////////////////////////






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

        return view('./lecture/lecture_detail_problemdetail', [ 'classObject' => $classObject, 'team' => $card->team(), 'problemAnalysis' => $problemAnalysis ]);
    }

    //팀 활동 보고서
    public function lectureDetailTeam (Request $request, $classObjectId) {
        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        $teams = Team::where('classObjectId', $classObjectId)->get();
        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();
        return view('./lecture/lecture_detail_team',
            [ 'classObjectId' => $classObjectId, 'teams' => $teams, 'classObjectName' => $classObjectName ,'classApply' => $classApply]);
    }
    //팀 활동 보고서 디테일
    public function lectureDetailTeamReport (Request $request,$classObjectId, $teamId) {
        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        $teamId = Team::where('id', $teamId)->first();

        return view('./lecture/lecture_detail_teamdetail',
            [ 'classObjectId' => $classObjectId, 'teamId' => $teamId, 'classObjectName' => $classObjectName ]);
    }

    //평가지
    public function lectureDetailEvolutionPaper (Request $request, $classObjectId) {
        $classObject = ClassObject::find($classObjectId);
        $teams = Team::where('classObjectId', $classObjectId)->get();


        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();
//            return dd($classApply);
        return view('./lecture/lecture_detail_evolutionpaper',
            [ 'classObjectId' => $classObjectId, 'teams' => $teams, 'classObject' => $classObject, 'classApply' => $classApply ]);
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
        $classApply = ClassApply::where('classObjectId', $classObjectId)->first();
        $classObject = ClassObject::where('id', $classObjectId)->first();
        if (!$classObject) {
            return redirect()->route('lectureList', ['classObjectId' => $classObjectId]);
        }
        $cards = Card::where('classObjectId', $classObjectId)->with('withItemsWithReflections', function ($query) {
            $query->where('type', 2);
        })->get()->where('withItemsWithReflections', '!=', '[]');
        $data = [];
        if ($cards) {
            foreach ($cards as $card) {
                foreach ($card->withItemsWithReflections as $item) {
                    $item = $item->withReflectionLog;
                    $item->teamId = $card->teamId;
                    if (is_null($item->teamId)) {
                        $item->teamName = '';
                    }
                    else {
                        $team = Team::find($item->teamId);
                        if (!$team) {
                            $item->teamId = null;
                            $item->teamName = '';
                        }
                        else {
                            $item->teamName = $team->name;
                        }
                    }
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
        return view('./lecture/lecture_detail_mind',
            [ 'classObject' => $classObject, 'reflections' => $paginate , 'classApply' => $classApply ]);
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

    public function lectureDetailStatistics(Request $request, $classObjectId){
        $classObject = ClassObject::find($classObjectId);
        if (!$classObject) {
            return redirect()->back();
        }

        $classApply = ClassApply::where('classObjectId',$classObjectId)->first();
        $classLists = ClassList::whereNotNull('userId')->where('classObjectId', $classObjectId)->with('withUser')->get();

        return view('.lecture.lecture_detail_statistics',['classObject' => $classObject, 'classApply' => $classApply, 'classLists' => $classLists]);
    }

    public function dashboardProView (Request $request){
        $user = Auth::user();
        //권한 검사
        if($user->authority != 2){
            return redirect()->route('home');
        }
        //$items = Item::where('userId', $user->id)->with('withCard')->get();

        $myClasses = MyClass::select('id', 'classObjectId', 'userId')->where('userId', Auth::id())->with(['classObject2' => function ($query) {
            $query->select('id', 'gwamokNm');
        }])->with(['classApply' => function ($query) {
            $query->select('id', 'classObjectId', 'meca', 'korName');
        }])->with('classMembers')->with(['classTeams2' => function ($query) {
        }])->with('prt')->get()->map(function ($item) {
            $item->teamCount = $item->classTeams2->filter(function ($team) {
                return $team->teamMembers2->count();
            })->count();

            return $item;
        });

//        return dd($myClasses);

        $classObjectIds = $myClasses->map(function ($class) {
            return $class->classObjectId;
        });

        $today = date('Y-m-d 00:00:00', strtotime('now'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now'));
        $tc = Calendar::whereIn('classObjectId', $classObjectIds)
            ->whereBetween('dateTime', [$today, $dayend])
            ->select('id', 'classObjectId', 'dateTime', 'content')
            ->orderBy('dateTime', 'asc')
//            ->latest()
            ->take(2)
            ->with(['classApply' => function ($q) {
                $q->select('id', 'korName', 'classObjectId');
            }])->get();

        $today = date('Y-m-d 00:00:00', strtotime('now + 1 DAY'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now + 1 DAY'));
        $nc = Calendar::whereIn('classObjectId', $classObjectIds)
            ->whereBetween('dateTime', [$today, $dayend])
            ->select('id', 'classObjectId', 'dateTime', 'content')
            ->orderBy('dateTime', 'asc')
//            ->latest()
            ->take(2)
            ->with(['classApply' => function ($q) {
                $q->select('id', 'korName', 'classObjectId');
            }])->get();
//        $myPages = MyPage::where('userId',Auth::id())->first();
//        $cards = Card::where('myPageId',$myPages->id)->where('type','0')->first();
//        $count = [];
//        $i = 0;
//        for($i = 0 ; $i < count((array)$cards->itemsNum) ; $i++){
//            $cards->itemsNum[$i];
//            $_item = Item::where('id',$cards->itemsNum[$i])
//                ->where('type',4)
////                ->with('withClassApply')
//                    ->with(['withClassApply' => function ($query) {
//                        $query->where('state', 'complete');
//                    } ] )
//                ->orderBy('id','desc')
//                ->first();
//            if ($_item) {
//                $count[] = $_item;
//            }
//        }
//
//        $items = collect($count)->filter();

        return view('lecture.dashboard_pro', ['myClasses' => $myClasses, 'today' => $tc, 'nextDay' => $nc]);
    }
    public function proGraphData (Request $request, $classObjectId) {
        $myClass = MyClass::where('classObjectId', $classObjectId)->first();
        if (!$myClass) {
            return false;
        }

        switch (Auth::user()->authority) {
            case 2:
                if ($myClass->userId != Auth::id()) {
                    return false;
                }
                break;
            case 5:
                if (!(Expert::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first())) {
                    return false;
                }
                break;
            case 6:
                if (!(ClassManager::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first())) {
                    return false;
                }
                break;
            default:
                return false;
        }

        $data = [];

        $teams = Team::select('id', 'name')->where('classObjectId', $classObjectId)->with('teamMembers2')->get()->filter(function ($team) {
            return $team->teamMembers2->count();
        });

        $teamIds = $teams->map(function ($team) {
            return $team->id;
        });

        $classMembers = User::whereIn('id', ClassList::where('classObjectId', $classObjectId)->whereNotNull('userId')->get()->map(function ($member) {
            return $member->userId;
        }))->get();

        $teamMembers = TeamMember::whereIn('teamId', $teamIds)->get();

        $scores = Card::where('classObjectId', $classObjectId)->with(['items3' => function ($query) {
            $query->with(['user' => function ($query) {
                $query->select('id', 'authority')->where('authority', '1');
            }])->with('comments2');
        }])->get();

        $coopScore = $scores->filter(function ($card, $key) {
            return $card->items3->where('type', 0)->count();
        })->map(function ($card) {
            return $card->items3->where('type', 0);
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($item) {
            return $item->user;
        })->groupBy('userId')->map(function ($user) {
//            return floor($user->count()*0.4 * 10) / 10;
            return  $user->count();
        })->sortByDesc(function ($score, $user) {
            return $score;
        })->map(function ($score, $userId) use($teamIds, $teamMembers, $teams, $classMembers) {
            $member = $teamMembers->where('userId', $userId)->first();
            $team = null;
            if ($member) {
                $team = $teams->where('id', $member->teamId)->first();
            }

            $user = $classMembers->where('id', $userId)->first();

            return ['userName' => $user->name, 'teamName' => $team ? $team->name : null, 'score' => ''.$score];
        });

        $_scores = $coopScore->take(3);

        $teamScores = $teams->map(function ($team) use ($_scores) {
            $teamScore = 0;
            foreach ($team->teamMembers2 as $member) {
                $teamScore += isset ($_scores[$member->userId]) ? $_scores[$member->userId]['score'] : 0;
            }

            $teamScore /= $team->teamMembers2->count();

            return ['id' => $team->id, 'name' => $team->name, 'score' => ''.$teamScore];
        })->sortByDesc(function ($team, $key) {
            return $team['score'];
        });

        $teamData = [];
        if ($teamScores->count() >= 2) {
            $teamData = ['1' => $teamScores->first(), $teamScores->count() => $teamScores->last()];
        }
        else if ($teamScores->count() >= 1) {
            $teamData = ['1' => $teamScores->first()];
        }

        $classScore = ''.floor($_scores->avg('score') * 10) / 10;

        $data['coop'] = ['class' => $classScore, 'individual' => $_scores->values()->toArray(), 'team' => $teamData];


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $achievementScore = $scores->filter(function ($card, $key) {
            return $card->items3->where('type', '!=', 0)->count();
        })->map(function ($card) {
            return $card->items3->where('type', '!=', 0);
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($item) {
            return $item->user;
        })->groupBy('userId')->map(function ($user) {
//            return floor($user->count()*0.3 * 10) / 10;
            return  $user->count();
        })->sortByDesc(function ($score, $user) {
            return $score;
        })->map(function ($score, $userId) use($teamIds, $teamMembers, $teams, $classMembers) {
            $member = $teamMembers->where('userId', $userId)->first();
            $team = null;
            if ($member) {
                $team = $teams->where('id', $member->teamId)->first();
            }

            $user = $classMembers->where('id', $userId)->first();

            return ['userName' => $user->name, 'teamName' => $team ? $team->name : null, 'score' => ''.$score];
        });

        $_scores = $achievementScore->take(3);

        $teamScores = $teams->map(function ($team) use ($_scores) {
            $teamScore = 0;
            foreach ($team->teamMembers2 as $member) {
                $teamScore += isset ($_scores[$member->userId]) ? $_scores[$member->userId]['score'] : 0;
            }

            $teamScore /= $team->teamMembers2->count();

            return ['id' => $team->id, 'name' => $team->name, 'score' => ''.$teamScore];
        })->sortByDesc(function ($team, $key) {
            return $team['score'];
        });

        $teamData = [];
        if ($teamScores->count() >= 2) {
            $teamData = ['1' => $teamScores->first(), $teamScores->count() => $teamScores->last()];

        }
        else if ($teamScores->count() >= 1) {
            $teamData = ['1' => $teamScores->first()];
        }

        $classScore = ''.floor($_scores->avg('score') * 10) / 10;

        $data['achievement'] = ['class' => $classScore, 'individual' => $_scores->values()->toArray(), 'team' => $teamData];


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $feedbackScore = $scores->filter(function ($card, $key) {
            return $card->items3->count();
        })->map(function ($card) {
            return $card->items3;
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($item) {
            return $item->user;
        })->filter(function ($item, $key) {
            return $item->comments2->count();
        })->map(function ($item) {
            return $item->comments2;
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($comment, $key) {
            return $comment->withUser;
        })->groupBy('userId')->map(function ($user) {
//            return  floor($user->count()*0.3 * 10) / 10;
            return  $user->count();
        })->sortByDesc(function ($score, $user) {
            return $score;
        })->map(function ($score, $userId) use($teamIds, $teamMembers, $teams, $classMembers) {
            $member = $teamMembers->where('userId', $userId)->first();
            $team = null;
            if ($member) {
                $team = $teams->where('id', $member->teamId)->first();
            }

            $user = $classMembers->where('id', $userId)->first();

            return ['userName' => $user->name, 'teamName' => $team ? $team->name : null, 'score' => ''.$score];
        });

        $_scores = $feedbackScore->take(3);

        $teamScores = $teams->map(function ($team) use ($_scores) {
            $teamScore = 0;
            foreach ($team->teamMembers2 as $member) {
                $teamScore += isset ($_scores[$member->userId]) ? $_scores[$member->userId]['score'] : 0;
            }

            $teamScore /= $team->teamMembers2->count();

            return ['id' => $team->id, 'name' => $team->name, 'score' => ''.$teamScore];
        })->sortByDesc(function ($team, $key) {
            return $team['score'];
        });

        $teamData = [];
        if ($teamScores->count() >= 2) {
            $teamData = ['1' => $teamScores->first(), $teamScores->count() => $teamScores->last()];
        }
        else if ($teamScores->count() >= 1) {
            $teamData = ['1' => $teamScores->first()];
        }

        $classScore = ''.floor($_scores->avg('score') * 10) / 10;

        $data['feedback'] = ['class' => $classScore, 'individual' => $_scores->values()->toArray(), 'team' => $teamData];


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $test = $classMembers->map(function ($member) use ($coopScore, $achievementScore, $feedbackScore) {
            $userId = $member->id;
            return
                [
                    'userId' => $userId,
                    'score' =>
                    ($coopScore->contains(function ($value, $key) use ($userId) {
                    return $key == $userId;
                }) ? (int)$coopScore[$userId]['score'] * 0.4 : 0) +
                ($achievementScore->contains(function ($value, $key) use ($userId) {
                    return $key == $userId;
                }) ? (int)$achievementScore[$userId]['score'] * 0.3 : 0) +
                ($feedbackScore->contains(function ($value, $key) use ($userId) {
                    return $key == $userId;
                }) ? (int)$feedbackScore[$userId]['score'] * 0.3 : 0),
                    'userName' =>
                        $coopScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ?
                            $coopScore[$userId]['userName'] :
                            (
                                $achievementScore->contains(function ($value, $key) use ($userId) {
                                    return $key == $userId;
                                }) ?
                                    $achievementScore[$userId]['userName'] :
                                    (
                                        $feedbackScore->contains(function ($value, $key) use ($userId) {
                                            return $key == $userId;
                                        }) ? $feedbackScore[$userId]['userName'] : null
                                    )
                            )
                        ,
                    'teamName' =>
                        $coopScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ?
                            $coopScore[$userId]['teamName'] :
                            (
                                $achievementScore->contains(function ($value, $key) use ($userId) {
                                    return $key == $userId;
                                }) ?
                                    $achievementScore[$userId]['teamName'] :
                                    (
                                        $feedbackScore->contains(function ($value, $key) use ($userId) {
                                            return $key == $userId;
                                        }) ? $feedbackScore[$userId]['teamName'] : null
                                    )
                            )
                        ,
                ];
        })->filter(function ($item) {
            return $item['userName'];
        })->sortByDesc('score');

        $memberCount = $test->count();
        $prevScore = [-1, 100];

        $ud = [];
        foreach ($test->values() as $rank=>$user) {
            if ($memberCount < 2) {
                $ud[] = array_merge($user, ['rankScore' => 100]);
            }
            else {
                if ($prevScore[0] == $user['score']) {
                    $ud[] = array_merge($user, ['rankScore' => $prevScore[1]]);
                }
                else {
                    $prevScore[0] = $user['score'];

                    $r = ($rank) / ($memberCount - 1);
                    if ($r <= 0.1) {
                        $prevScore[1] = 100;
                    }
                    else if ($r <= 0.2) {
                        $prevScore[1] = 95;
                    }
                    else if ($r <= 0.3) {
                        $prevScore[1] = 90;
                    }
                    else if ($r <= 0.4) {
                        $prevScore[1] = 89;
                    }
                    else if ($r <= 0.6) {
                        $prevScore[1] = 85;
                    }
                    else if ($r <= 0.7) {
                        $prevScore[1] = 80;
                    }
                    else if ($r <= 0.8) {
                        $prevScore[1] = 79;
                    }
                    else if ($r <= 0.9) {
                        $prevScore[1] = 75;
                    }
                    else {
                        $prevScore[1] = 70;
                    }

                    $ud[] = array_merge($user, ['rankScore' => $prevScore[1]]);
                }
            }
        }

        $data['userRank'] = $ud;


        ///////////////////////////////////////////////////////////////////////////////////////////////////

        $teamScores = $teams->map(function ($team) use ($coopScore, $achievementScore, $feedbackScore) {
            $teamScore = 0;
            foreach ($team->teamMembers2 as $member) {
                $userId = $member->userId;
                $teamScore +=
                    ($coopScore->contains(function ($value, $key) use ($userId) {
                        return $key == $userId;
                    }) ? (int)$coopScore[$userId]['score'] * 0.4 : 0) +
                    ($achievementScore->contains(function ($value, $key) use ($userId) {
                        return $key == $userId;
                    }) ? (int)$achievementScore[$userId]['score'] * 0.3 : 0) +
                    ($feedbackScore->contains(function ($value, $key) use ($userId) {
                        return $key == $userId;
                    }) ? (int)$feedbackScore[$userId]['score'] * 0.3 : 0);
            }

//            $teamScore /= $team->teamMembers2->count();

            return ['id' => $team->id, 'name' => $team->name, 'score' => ''.$teamScore];
        })->sortByDesc(function ($team, $key) {
            return $team['score'];
        });

        $teamCount = $teamScores->count();
        $prevScore = [-1, 100];

        $td = [];
        foreach ($teamScores->values() as $rank=>$team) {
            if ($teamCount < 2) {
                $td[] = array_merge($team, ['rankScore' => 100]);
            }
            else {
                if ($prevScore[0] == $team['score']) {
                    $td[] = array_merge($team, ['rankScore' => $prevScore[1]]);
                }
                else {
                    $prevScore[0] = $team['score'];

                    $r = ($rank) / ($teamCount - 1);
                    if ($r <= 0.1) {
                        $prevScore[1] = 100;
                    }
                    else if ($r <= 0.2) {
                        $prevScore[1] = 95;
                    }
                    else if ($r <= 0.3) {
                        $prevScore[1] = 90;
                    }
                    else if ($r <= 0.4) {
                        $prevScore[1] = 89;
                    }
                    else if ($r <= 0.6) {
                        $prevScore[1] = 85;
                    }
                    else if ($r <= 0.7) {
                        $prevScore[1] = 80;
                    }
                    else if ($r <= 0.8) {
                        $prevScore[1] = 79;
                    }
                    else if ($r <= 0.9) {
                        $prevScore[1] = 75;
                    }
                    else {
                        $prevScore[1] = 70;
                    }

                    $td[] = array_merge($team, ['rankScore' => $prevScore[1]]);
                }
            }
        }

        $tdc = collect($td);
        array_push($td, ['id' => null, 'name' => '팀', 'score' => $tdc->avg('score'), 'rankScore' => $tdc->avg('rankScore')]);

        $data['teamRank'] = $td;

        return $data;

//        return dd($data);
    }

    public function dashboardStuView (Request $request){
        //권한 검사
        $user = Auth::user();
        if ($user->authority != 1) {
            return redirect()->route('home');
        }


        $classMember = ClassList::select('classObjectId')->where('userId', Auth::id())->get();
        $classObjectIds = $classMember->map(function ($item) {
            return $item->classObjectId;
        });

        $myClasses = MyClass::select('id', 'classObjectId', 'userId')->whereIn('classObjectId', $classObjectIds)->with(['classObject2' => function ($query) {
            $query->select('id', 'gwamokNm');
        }])->with(['classApply' => function ($query) {
            $query->select('id', 'classObjectId', 'meca', 'korName');
        }])->with('classMembers')->with(['classTeams2' => function ($query) {
        }])->with('prt')->get()->map(function ($item) {
            $item->teamCount = $item->classTeams2->filter(function ($team) {
                return $team->teamMembers2->count();
            })->count();

            return $item;
        });

        $teamIds = TeamMember::select('teamId')->where('userId', Auth::id())->get()->map(function ($item) {
            return $item->teamId;
        });

        $today = date('Y-m-d 00:00:00', strtotime('now'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now'));
        $tc = Calendar::whereIn('teamId', $teamIds)
            ->whereBetween('dateTime', [$today, $dayend])
            ->select('id', 'classObjectId', 'dateTime', 'content')
            ->latest()
            ->take(2)
            ->with(['classApply' => function ($q) {
                $q->select('id', 'korName', 'classObjectId');
            }])->get();

        $today = date('Y-m-d 00:00:00', strtotime('now + 1 DAY'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now + 1 DAY'));
        $nc = Calendar::whereIn('teamId', $teamIds)
            ->whereBetween('dateTime', [$today, $dayend])
            ->select('id', 'classObjectId', 'dateTime', 'content')
            ->latest()
            ->take(2)
            ->with(['classApply' => function ($q) {
                $q->select('id', 'korName', 'classObjectId');
            }])->get();

        return view('lecture.dashboard_stu', ['myClasses' => $myClasses, 'today' => $tc, 'nextDay' => $nc]);
    }
    public function stuGraphData (Request $request, $classObjectId) {
        $myClass = MyClass::where('classObjectId', $classObjectId)->first();
        if (!$myClass) {
            return false;
        }
        $classList = ClassList::where('userId', Auth::id())->where('classObjectId', $myClass->classObjectId)->first();
        if (!$classList) {
           return false;
        }

        $data = [];

        $teams = Team::select('id', 'name')->where('classObjectId', $classObjectId)->with(['teamMembers2' => function ($query) {
            $query->where('userId', Auth::id());
        }])->get()->filter(function ($team) {
            return $team->teamMembers2->count();
        });

        $classMembers = User::whereIn('id', ClassList::where('classObjectId', $classObjectId)->whereNotNull('userId')->get()->map(function ($member) {
            return $member->userId;
        }))->get();

        $teamMembers = collect([]);
        if ($teams->count()) {
            $teamMembers = TeamMember::where('teamId', $teams->first()->id)->get();
        }


        $scores = Card::where('classObjectId', $classObjectId)->with(['items3' => function ($query) {
            $query->with(['user' => function ($query) {
                $query->select('id', 'authority')->where('authority', '1');
            }])->with('comments2');
        }])->get();

        $coopScore = $scores->filter(function ($card, $key) {
            return $card->items3->where('type', 0)->count();
        })->map(function ($card) {
            return $card->items3->where('type', 0);
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($item) {
            return $item->user;
        })->groupBy('userId')->map(function ($user) {
//            return floor($user->count()*0.4 * 10) / 10;
            return  $user->count();
        })->sortByDesc(function ($score, $user) {
            return $score;
        })->map(function ($score, $userId) use($teamMembers, $teams, $classMembers) {
            $member = $teamMembers->where('userId', $userId)->first();
            $team = null;
            if ($member) {
                $team = $teams->where('id', $member->teamId)->first();
            }

            $user = $classMembers->where('id', $userId)->first();

            return ['userName' => $user->name, 'teamName' => $team ? $team->name : null, 'score' => ''.$score];
        });


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $achievementScore = $scores->filter(function ($card, $key) {
            return $card->items3->where('type', '!=', 0)->count();
        })->map(function ($card) {
            return $card->items3->where('type', '!=', 0);
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($item) {
            return $item->user;
        })->groupBy('userId')->map(function ($user) {
//            return floor($user->count()*0.3 * 10) / 10;
            return  $user->count();
        })->sortByDesc(function ($score, $user) {
            return $score;
        })->map(function ($score, $userId) use($teamMembers, $teams, $classMembers) {
            $member = $teamMembers->where('userId', $userId)->first();
            $team = null;
            if ($member) {
                $team = $teams->where('id', $member->teamId)->first();
            }

            $user = $classMembers->where('id', $userId)->first();

            return ['userName' => $user->name, 'teamName' => $team ? $team->name : null, 'score' => ''.$score];
        });
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $feedbackScore = $scores->filter(function ($card, $key) {
            return $card->items3->count();
        })->map(function ($card) {
            return $card->items3;
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($item) {
            return $item->user;
        })->filter(function ($item, $key) {
            return $item->comments2->count();
        })->map(function ($item) {
            return $item->comments2;
        })->flatMap(function ($values) {
            return $values;
        })->filter(function ($comment, $key) {
            return $comment->withUser;
        })->groupBy('userId')->map(function ($user) {
//            return  floor($user->count()*0.3 * 10) / 10;
            return  $user->count();
        })->sortByDesc(function ($score, $user) {
            return $score;
        })->map(function ($score, $userId) use($teamMembers, $teams, $classMembers) {
            $member = $teamMembers->where('userId', $userId)->first();
            $team = null;
            if ($member) {
                $team = $teams->where('id', $member->teamId)->first();
            }

            $user = $classMembers->where('id', $userId)->first();

            return ['userName' => $user->name, 'teamName' => $team ? $team->name : null, 'score' => ''.$score];
        });
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $data['info'] = ['teamName' => $teams->count() ? $teams->first()->name : null, 'classMemberCount' => $classMembers->count()];

        //전체
        $data[0][0][0] = ''.$coopScore->sum('score');
        $data[0][0][1] = ''.$achievementScore->sum('score');
        $data[0][0][2] = ''.$feedbackScore->sum('score');

        //팀
        $data[0][1][0] = ''.$coopScore->sum(function ($elem) {
            return $elem['teamName'] ? $elem['score'] : 0;
        });
        $data[0][1][1] = ''.$achievementScore->sum(function ($elem) {
            return $elem['teamName'] ? $elem['score'] : 0;
        });
        $data[0][1][2] = ''.$feedbackScore->sum(function ($elem) {
            return $elem['teamName'] ? $elem['score'] : 0;
        });

        //본인
        $data[0][2][0] = isset($coopScore[Auth::id()]) ? $coopScore[Auth::id()]['score'] : 0;
        $data[0][2][1] = isset($achievementScore[Auth::id()]) ? $achievementScore[Auth::id()]['score'] : 0;
        $data[0][2][2] = isset($feedbackScore[Auth::id()]) ? $feedbackScore[Auth::id()]['score'] : 0;

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $test = $classMembers->map(function ($member) use ($coopScore, $achievementScore, $feedbackScore) {
            $userId = $member->id;
            return
                [
                    'userId' => $userId,
                    'score' =>
                        ($coopScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ? (int)$coopScore[$userId]['score'] * 0.4 : 0) +
                        ($achievementScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ? (int)$achievementScore[$userId]['score'] * 0.3 : 0) +
                        ($feedbackScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ? (int)$feedbackScore[$userId]['score'] * 0.3 : 0),
                    'userName' =>
                        $coopScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ?
                            $coopScore[$userId]['userName'] :
                            (
                            $achievementScore->contains(function ($value, $key) use ($userId) {
                                return $key == $userId;
                            }) ?
                                $achievementScore[$userId]['userName'] :
                                (
                                $feedbackScore->contains(function ($value, $key) use ($userId) {
                                    return $key == $userId;
                                }) ? $feedbackScore[$userId]['userName'] : null
                                )
                            )
                    ,
                    'teamName' =>
                        $coopScore->contains(function ($value, $key) use ($userId) {
                            return $key == $userId;
                        }) ?
                            $coopScore[$userId]['teamName'] :
                            (
                            $achievementScore->contains(function ($value, $key) use ($userId) {
                                return $key == $userId;
                            }) ?
                                $achievementScore[$userId]['teamName'] :
                                (
                                $feedbackScore->contains(function ($value, $key) use ($userId) {
                                    return $key == $userId;
                                }) ? $feedbackScore[$userId]['teamName'] : null
                                )
                            )
                    ,
                ];
        })->filter(function ($item) {
            return $item['userName'];
        })->sortByDesc('score');

        $memberCount = $test->count();
        $prevScore = [-1, 100];
        $prevRank = 1;

        $ud = [];
        foreach ($test->values() as $rank=>$user) {
            if ($memberCount < 2) {
                $ud[] = array_merge($user, ['rankScore' => 100, 'rank' => $prevRank]);
            }
            else {
                if ($prevScore[0] == $user['score']) {
                    $ud[] = array_merge($user, ['rankScore' => $prevScore[1], 'rank' => $prevRank]);
                }
                else {
                    $prevScore[0] = $user['score'];

                    $r = ($rank) / ($memberCount - 1);
                    if ($r <= 0.1) {
                        $prevScore[1] = 100;
                    }
                    else if ($r <= 0.2) {
                        $prevScore[1] = 95;
                    }
                    else if ($r <= 0.3) {
                        $prevScore[1] = 90;
                    }
                    else if ($r <= 0.4) {
                        $prevScore[1] = 89;
                    }
                    else if ($r <= 0.6) {
                        $prevScore[1] = 85;
                    }
                    else if ($r <= 0.7) {
                        $prevScore[1] = 80;
                    }
                    else if ($r <= 0.8) {
                        $prevScore[1] = 79;
                    }
                    else if ($r <= 0.9) {
                        $prevScore[1] = 75;
                    }
                    else {
                        $prevScore[1] = 70;
                    }

                    $prevRank = $rank + 1;
                    $ud[] = array_merge($user, ['rankScore' => $prevScore[1], 'rank' => $prevRank]);
                }
            }
        }

        $ud = collect($ud);

        //본인
        $data[1][0] = ''.($ud->filter(function ($item) {
            return $item['userId'] == Auth::id();
        })->first()['rankScore'] ?? 0);

        //팀
        $data[1][1] = ''.$ud->filter(function ($item) {
                return $item['teamName'];
            })->avg('rankScore');

        //수업
        $data[1][2] = ''.$ud->avg('rankScore');

        $myData = $ud->where('userId', Auth::id())->first();
        if ($myData) {
            $data[2] = [''.$myData['rank'], ''.$myData['rankScore']];
        }
        else {
            if ($ud->count()) {
                $data[2] = [''.($ud->last()['rank'] + 1), '0'];
            }
            else {
                $data[2] = ['0', '0'];
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////

//        return dd($data);

        return $data;
    }

    public function dashboardConView (Request $request){
        //권한 검사
        $user = Auth::user();
        if ($user->authority != 4) {
            return redirect()->route('home');
        }

        return view('lecture.dashboard_con');
    }

    public function dashboardOutView(Request $request){
        $user = Auth::user();
        //권한 검사
        if(!$user){
            return redirect()->back();
        }
        //$items = Item::where('userId', $user->id)->with('withCard')->get();

        $classes = collect([]);
        $auth = Auth::user()->authority;
        if ($auth == 5) {
            $classes = Expert::where('userId', Auth::id())->with(['my_class' => function ($query) {
                $query->with(['classApply' => function ($query) {
                    $query->select('id', 'classObjectId', 'meca');
                }])->with('classMembers')->with('classTeams2')->with('prt');
            }])->get()->map(function ($item) {
                $myClass = $item->my_class;
                $myClass->teamCount = $myClass->classTeams2->filter(function ($team) {
                    return $team->teamMembers2->count();
                })->count();

                return $myClass;
            });
        }
        else if ($auth == 6) {
            $classes = ClassManager::where('userId', Auth::id())->with(['my_class' => function ($query) {
                $query->with(['classApply' => function ($query) {
                    $query->select('id', 'classObjectId', 'meca');
                }])->with('classMembers')->with('classTeams2')->with('prt');
            }])->get()->map(function ($item) {
                $myClass = $item->my_class;
                $myClass->teamCount = $myClass->classTeams2->filter(function ($team) {
                    return $team->teamMembers2->count();
                })->count();

                return $myClass;
            });
        }
        else {
            return redirect()->back();
        }


//        $myClasses = MyClass::select('id', 'classObjectId', 'userId')->where('userId', Auth::id())->with(['classObject2' => function ($query) {
//            $query->select('id', 'gwamokNm');
//        }])->with(['classApply' => function ($query) {
//            $query->select('id', 'classObjectId', 'meca');
//        }])->with('classMembers')->with('classTeams')->with('prt')->get();
//
        $classObjectIds = $classes->map(function ($class) {
            return $class->classObjectId;
        });

//        $teamIds = TeamMember::select('teamId')->where('userId', Auth::id())->get()->map(function ($item) {
//            return $item->teamId;
//        });

//        $teamIds = Team::select('id')->whereIn('classObjectId', $classObjectIds)->get()->map(function ($item) {
//            return $item->id;
//        });

        $today = date('Y-m-d 00:00:00', strtotime('now'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now'));
        $tc = Calendar::whereIn('classObjectId', $classObjectIds)
            ->whereBetween('dateTime', [$today, $dayend])
            ->select('id', 'classObjectId', 'dateTime', 'content')
            ->latest()
            ->take(2)
            ->with(['classApply' => function ($q) {
                $q->select('id', 'korName', 'classObjectId');
            }])->get();

        $today = date('Y-m-d 00:00:00', strtotime('now + 1 DAY'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now + 1 DAY'));
        $nc = Calendar::whereIn('classObjectId', $classObjectIds)
            ->whereBetween('dateTime', [$today, $dayend])
            ->select('id', 'classObjectId', 'dateTime', 'content')
            ->latest()
            ->take(2)
            ->with(['classApply' => function ($q) {
                $q->select('id', 'korName', 'classObjectId');
            }])->get();

//        return dd($tc);

        return view('lecture.dashboard_out', ['classes' => $classes, 'today' => $tc, 'nextDay' => $nc]);
    }



    public function lectureExport (Request $request){
        $user = Auth::user();
        if ($user->authority != 2 && $user->authority != 3) {
            return redirect()->back();
        }

        $cards = Card::where('userId', $user->id)->where('type', 0)->orderBy('id', 'desc')->with('classApplyItems')->get();
        $applies = [];

        foreach ($cards as $card) {
            $items = $card->classApplyItems ? $card->classApplyItems : [];
            foreach ($items as $item) {
                if ($item->classApplyObject) {
                    $applies[] = $item->classApplyObject;
                }
            }
        }

        $content = $request->has('content') ? $request['content'] : null;
//        $content = $request->has('search') ? $request['search'] : null;

        $applies = collect($applies);
        $applies = $applies->filter(function ($item, $key) use($applies, $content) {
            $item->year = '';
            $item->semester = '';
            $item->gnjDaehakNm = '';
            $item->userCount = $item->classListCount2();

            if ($item->classObject) {
//                $item->korName = $item->classObject->gwamokNm;
                $item->department = $item->classObject->gnjHakgwaNm;
                $item->year = $item->classObject->suupYear.'년';
                switch ($item->classObject->suupTerm) {
                    case 10:
                        $item->semester = "1학기";
                        break;
                    case 15:
                        $item->semester = "여름학기";
                        break;
                    case 20:
                        $item->semester = "2학기";
                        break;
                    case 25:
                        $item->semester = "겨울학기";
                        break;
                    default:
                        $item->year = '';
                }
                $item->gnjDaehakNm = $item->classObject->gnjDaehakNm;
            }
            if ($content && !strpos($item->korName, $content)) {
                return false;
            }
            return true;
        });

        $Contents = $applies->toArray();
        $array = [];

        //백업
        $arrayApplies = [0];
        $arrayApplies = $applies->toArray();

        if($request->has('search') && $request['search'] !== null){
            foreach ($Contents as $key=>$content) {
                if (strpos( $content['korName'], $request['search']) !== false) {
                    $array[] = $content;
                }
            }
            $arrayApplies = $array;
        }

        $newArr = [];
        $newArr[] =  [
            '번호' => '번호',
            '코드' => '코드',
            '수업명' =>'수업명',
            '단대' => '단대',
            '학과' => '학과',
            '학기' => '학기',
            '인원' => '인원',
            '상태' => '상태',
        ];
        for($i = 0 ;$i< count($arrayApplies) ; $i++){
            if($arrayApplies[$i]['state'] == 'wait'){
                $newArr[] =  [
                    '번호' => $i+1,
                    '코드' => $arrayApplies[$i]['code'],
                    '수업명' =>$arrayApplies[$i]['korName'],
                    '단대' => ' ',
                    '학과' => ' ',
                    '학기' => 0,
                    '인원' => 0,
                    '상태' => '대기중',
                ];
            }else{
                $term = '';
                if($arrayApplies[$i]['class_object']['suupTerm'] == '10'){
                    $term = '1학기';
                }elseif($arrayApplies[$i]['class_object']['suupTerm'] == '15'){
                    $term = '여름학기';
                }elseif($arrayApplies[$i]['class_object']['suupTerm'] == '20'){
                    $term = '2학기';
                }elseif($arrayApplies[$i]['class_object']['suupTerm'] == '25'){
                    $term = '겨울학기';
                }
                $test = collect($arrayApplies);

                $newArr[] =  [
                    '번호' => $i+1,
                    '코드' => $arrayApplies[$i]['class_object']['suupNo'],
                    '수업명' =>$arrayApplies[$i]['korName'],
                    '단대' => $arrayApplies[$i]['class_object']['gnjDaehakNm'],
                    '학과' => $arrayApplies[$i]['class_object']['gnjHakgwaNm'],
                    '학기' => $arrayApplies[$i]['class_object']['suupYear'].'년'.$term,
                    '인원' => $arrayApplies[$i]['userCount'],
                    '상태' => '진행중',
                ];
            }
        }
        $newArr = collect($newArr);
        return Excel::download(new InvoicesExport($newArr), 'ClassList.xlsx');
    }


    public function testMember (Request $request)
    {
        $classObject = ClassObject::find($request['classObjectId']);

        $users = User::select('id', 'name', 'email', 'social')->where('authority', 1)->get();

        return view('lecture.test_member', ['classObject' => $classObject, 'users' => $users]);
    }

    public function testMemberAdd (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => ['array'],
            'ids.*' => ['integer'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $classObject = ClassObject::find($request['classObjectId']);

        if (!$classObject) {
            return redirect()->back();
        }

        $myClass = MyClass::where('classObjectId', $classObject->id)->first();

        if (!$myClass) {
            return redirect()->back();
        }

        $users = User::whereIn('id', $request['ids'] ? $request['ids'] : [])->get();

        foreach ($users as $user) {
            ClassList::updateOrCreate([
                'userId' => $user->id,
                'classObjectId' => $classObject->id,
                'name' => $user->name,
            ]);
        }

        return redirect()->route('lectureDetailMember',
            ['classObjectId' => $classObject->id]
        )->withErrors(['res' => '멤버 추가 완료']);
//        return redirect()->route('lectureList');
    }

    public function addTeam(Request $request, $classObjectId){
        $teams = Team::where('classObjectId' , $classObjectId)->get();
        if($teams->count() >= 20){
            return redirect()->back()->withErrors(['error' => '팀을 20개 이상 생성할 수 없습니다.']);
        }
        Team::insert([
            'name' => '새 팀',
            'classObjectId' => $classObjectId,
            "created_at"=> now(),
            "updated_at"=> now(),
        ]);
        return redirect()->back()->withErrors(['error' => '팀이 생성되었습니다.']);
    }

    public function deleteTeam(Request $request, $teamId){
//        dd($teamId);
        $team = Team::where('id', $teamId)->with(['withCards' => function ($query) {
            $query->with('items3');
        }])->first();
        if (!$team) {
            return redirect()->back()->withErrors(['teamId' => '팀 정보가 유효하지 않습니다.']);
        }

        $cards = $team->withCards;

        $items = $cards->flatMap(function ($values) {
            return $values->items3;
        });

        $items->each(function ($element) {
            $element->removeWithCard();
        });

        $team->delete();

        return redirect()->back()->withErrors(['success' => '삭제되었습니다.']);
    }

    public function classETCSetting (Request $request) {




//        $validator = Validator::make($request->all(), [
//            'classObjectId' => ['required', 'integer', 'min:1'],
////            'experts' => ['required', 'json'],
////            'classManagers' => ['required', 'json'],
//        ]);


//
//        if ($validator->fails()) {
//            return false;
//        }

//        return dd($request->all());

//        $validator = Validator::make($request->all(), [
//            'id' => ['required', 'integer', 'min:1'],
//            'userId' => ['required', 'integer', 'min:1'],
//            'classObjectId' => ['required', 'integer', 'min:1'],
//            'onClassTalk' => ['required', 'boolean'],
//            'onTeamTalk' => ['required', 'boolean'],
//            'onOrientation' => ['required', 'boolean'],
//            'onReflectionLog' => ['required', 'boolean'],
//            'onEvaluation' => ['required', 'boolean'],
//            'onTeamActivity' => ['required', 'boolean'],
//            'onProblemAnalysis' => ['required', 'boolean'],
//            'onTeamAccess' => ['required', 'boolean'],
//            'onTeamOrientation' => ['required', 'boolean'],
//            'onSetting' => ['required', 'boolean'],
//        ]);
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('id')) {
//                return ['fail' => 'validate error: id'];
//            }
//            else if ($errors->has('userId')) {
//                return ['fail' => 'validate error: userId'];
//            }
//            else if ($errors->has('classObjectId')) {
//                return ['fail' => 'validate error: classObjectId'];
//            }
//            else if ($errors->has('version')) {
//                return ['fail' => 'validate error: version'];
//            }
//            else if ($errors->has('cardsNum')) {
//                return ['fail' => 'validate error: cardsNum'];
//            }
//            else if ($errors->has('onClassTalk')) {
//                return ['fail' => 'validate error: onClassTalk'];
//            }
//            else if ($errors->has('onTeamTalk')) {
//                return ['fail' => 'onTeamTalk'];
//            }
//            else if ($errors->has('onOrientation')) {
//                return ['fail' => 'validate error: onOrientation'];
//            }
//            else if ($errors->has('onReflectionLog')) {
//                return ['fail' => 'validate error: onReflectionLog'];
//            }
//            else if ($errors->has('onEvaluation')) {
//                return ['fail' => 'validate error: onEvaluation'];
//            }
//            else if ($errors->has('onTeamActivity')) {
//                return ['fail' => 'validate error: onTeamActivity'];
//            }
//            else if ($errors->has('onProblemAnalysis')) {
//                return ['fail' => 'validate error: onProblemAnalysis'];
//            }
//            else if ($errors->has('onProblemAnalysis')) {
//                return ['fail' => 'validate error: onProblemAnalysis'];
//            }
//            else if ($errors->has('onTeamAccess')) {
//                return ['fail' => 'validate error: onTeamAccess'];
//            }
//            else if ($errors->has('onTeamOrientation')) {
//                return ['fail' => 'validate error: onTeamOrientation'];
//            }
//            else if ($errors->has('onSetting')) {
//                return ['fail' => 'validate error: onSetting'];
//            }
//        }

        $classObject = ClassObject::find($request['classObjectId']);
        if (!$classObject) {
            return ['fail' => 'classObjectId'];
        }
        $classObjectId = $classObject->id;

        $experts = (array)json_decode($request['experts']);

        $experts = collect($experts)->filter(function ($item) use($classObjectId) {
            $validator = Validator::make(['userId' => $item->id, 'write' => $item->write], [
                'userId' => ['required', 'integer', 'min:1'],
                'write' => ['required', 'boolean'],
            ]);

            if ($validator->fails()) {
                return false;
            }

            $user = User::find($item->id);
            if (!$user) {
                return false;
            }
            if ($user->authority != 5) {
                return false;
            }

            $expert = Expert::where('classObjectId', $classObjectId)->where('userId', $item->id)->first();
            if ($expert) {
                return false;
            }

            return true;
        });

        $experts->each(function ($item) use($classObjectId) {
            Expert::create([
               'userId' => $item->id,
               'classObjectId' => $classObjectId,
                'write' => $item->write,
            ]);
        });

        $managers = (array)json_decode($request['classManagers']);
        $managers = collect($managers)->filter(function ($item) use($classObjectId) {
            $validator = Validator::make(['userId' => $item->id, 'write' => $item->write], [
                'userId' => ['required', 'integer', 'min:1'],
                'write' => ['required', 'boolean'],
            ]);

            if ($validator->fails()) {
                return false;
            }

            $user = User::find($item->id);
            if (!$user) {
                return false;
            }
            if ($user->authority != 6) {
                return false;
            }

            $manager = ClassManager::where('classObjectId', $classObjectId)->where('userId', $item->id)->first();
            if ($manager) {
                return false;
            }

            return true;
        });

        $managers->each(function ($item) use($classObjectId) {
            ClassManager::create([
                'userId' => $item->id,
                'classObjectId' => $classObjectId,
                'write' => $item->write,
            ]);
        });


        return ['success' => true];
    }

    public function classETCView (Request $request) {

        $classObject = ClassObject::find($request['classObjectId']);
        if (!$classObject) {
            return ['fail' => 'classObjectId'];
        }

        $classObjectId = $classObject->id;

        //모든 외부 전문가 가져오기
        $users_ = User::select('id', 'name', 'email')->where('authority', 5)->get();

        $experts = [];
        for($i=0;$i<count($users_);$i++){
            $expert = Expert::where('classObjectId', $classObjectId)->where('userId', $users_[$i]->id)->first();

            if($expert){
                $experts[] = [
                    'id' => $users_[$i]->id,
                    'email' => $users_[$i]->email,
                    'name' => $users_[$i]->name,
                    'write' => $expert->write == 1,
                    'selected' => true,
                ];
            }
            else{
                $experts[] = [
                    'id' => $users_[$i]->id,
                    'email' => $users_[$i]->email,
                    'name' => $users_[$i]->name,
                    'write' => false,
                    'selected' => false,
                ];
            }

        }

        $users_ = User::select('id', 'name', 'email')->where('authority', 6)->get();

        $classManagers = [];
        for($i=0;$i<count($users_);$i++){
            $classManager = ClassManager::where('classObjectId', $classObjectId)->where('userId', $users_[$i]->id)->first();

            if($classManager){
                $classManagers[] = [
                    'id' => $users_[$i]->id,
                    'email' => $users_[$i]->email,
                    'name' => $users_[$i]->name,
                    'write' => $classManager->write == 1,
                    'selected' => true,
                ];
            }
            else{
                $classManagers[] = [
                    'id' => $users_[$i]->id,
                    'email' => $users_[$i]->email,
                    'name' => $users_[$i]->name,
                    'write' => false,
                    'selected' => false,
                ];
            }

        }

        return ['experts' => $experts, 'classManagers' => $classManagers];
    }

}


/*backup LectureList 첫번쨰 소스
 *
 *
 * //        $perPage = 3;
//        $myClasses = MyClass::where('userId', $user->id)->paginate($perPage);
//        if (!$myClasses) {
//            return redirect()->back();
//        }
//
//        if($request->has('content')){
//            $validator = Validator::make($request->all(),[
//                'content' => ['nullable', 'string','min:2']
//            ]);
//
//            if(!$validator->fails()){
//                $content = $request['content'];
//                $myClasses = MyClass::where('userId', Auth::id())
//                    ->with(['withClassObject' => function ($query) use($content) {
//                        $query->where('name', 'like', '%'.$content.'%');
//                    }])->get()->whereNotNull('wi
//                    thClassObject');
//
//                $paginate = new LengthAwarePaginator(
//                    $myClasses->forPage(Paginator::resolveCurrentPage(), $perPage),
//                    $myClasses->count(),
//                    $perPage,
//                    Paginator::resolveCurrentPage(),
//                    ['path' => Paginator::resolveCurrentPath()]
//                );
//
//                $myClasses = $paginate;
//            }
//            else {
//                $errors = $validator->errors();
//
//                if ($errors->has('content')) {
//                    return redirect('/lectureList')->withErrors(['content_length' => '검색어를 두자 이상 입력하세요.']);
//                }
//            }
//        }
 *
 *
 * */
