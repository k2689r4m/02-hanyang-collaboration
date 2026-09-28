<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use App\Models\MyClass;
use App\Models\MyPage;
use App\Models\ClassObject;
use App\Models\ClassList;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Card;
use App\Models\Item;
use App\Models\Notice;
use App\Models\ReflectionLog;
use App\Models\ProblemAnalysis;
use App\Models\ClassApply;
use App\Models\req;
use App\Models\faq;
use App\Models\Competition;
use App\Models\CompetitionApply;
use App\Models\Basic;
use App\Models\BasicApply;
use App\Models\Consulting;
use App\Models\ConsultingApply;
use App\Models\TeamActivity;
use App\Models\AdminLog;
use App\Models\ProLog;
use App\Events\AdminLogEvent;
use App\Events\ProLogEvent;
use App\Models\Expert;
use App\Models\Daehak;
//현재 시간 가져오는 라이브러리
use Carbon\carbon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Auth\SessionGuard;

//sortable
//use Kyslik\ColumnSortable\Sortable;

class AdminController extends Controller
{

    private $daehakList = [
        '공학대학' => [
            '건축학부',
            '건설환경공학과',
            '교통·물류공학과',
            '전자공학부',
            '재료화학공학과',
            '기계공학과',
            '산업경영공학과',
            '생명나노공학과',
            '로봇공학과',
            '융합공학과',
            '국방정보공학과',
            '스마트융합공학부',
        ],
        '소프트웨어융합대학' => [
            '소프트웨어학부',
            'ICT융합학부',
            '인공지능학과',
            '융합전공',
        ],
        '약학대학' => [
            ''
        ],
        '과학기술융합대학' => [
            '응용수학과',
            '응용물리학과',
            '분자생명과학과',
            '화학분자공학과',
            '해양융합공학과',
            '나노광전자학과',
        ],
        '국제문화대학' => [
            '한국언어문학과',
            '문화인류학과',
            '문화콘텐츠학과',
            '중국학과',
            '일본학과',
            '영미언어·문화학과',
            '프랑스학과',
        ],
        '언론정보대학' => [
            '광고홍보학과',
            '신문방송학과',
            '정보사회학과',
            '정보사회미디어학과',
            '글로벌전략커뮤니케이션전공',
        ],
        '경상대학' => [
            '경제학부',
            '경영학부',
            '보험계리학과',
            '회계세무학과',
        ],
        '디자인대학' => [
            '주얼리·패션디자인',
            '산업디자인학과',
            '커뮤니케이션디자인학과',
            '영상디자인학과',
        ],
        '예체능대학' => [
            '스포츠과학부',
            '무용예술학과',
            '실용음악학과',
        ],
        '행정부서대학' => [
            '커리어개발센터'
        ],
    ];

    //로그인
    public function adminLoginView(Request $request){
        return view('admin.admin_login');
    }
    function adminLogin(Request $request){
        if (Auth::attempt($request->only('email', 'password'))) {
//            $request->session()->regenerate();

            if (!in_array(Auth::user()->authority, [3, 7, 9])) {
                Auth::logout();
                return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
            }

            return redirect()->route('admin.dashboardView');
        }
        return redirect()->route('admin.loginView')->withErrors(['error' => '로그인 정보를 다시 확인해 주세요.']);
    }

    public function pwdCheckView (Request $request) {
        return view('admin.pwdCheck');
    }
    public function pwdCheck (Request $request) {
        if (Auth::once(['email' => Auth::user()->email, 'password' => $request->password])) {
            $key = uniqid();
            $request->session()->put('key', $key);
            return view('admin.pwdEdit', ['key' => $key]);
        }
        return redirect()->back()->withErrors(['password' => '유효하지 않은 비밀번호 입니다.']);
    }
    public function pwdEdit (Request $request) {
        if ($request->session()->has('key')) {
            $validator = Validator::make(array_merge($request->all(), ['sKey' => $request->session()->get('key')]), [
                'key' => ['required', 'same:sKey'],
                'password' => ['required', 'confirmed', 'string', 'regex:/^(?=[^a-z\n]*[a-z])(?=[^\d\n]*\d).{6,12}$/']
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                return view('admin.pwdEdit', ['key' => $request->session()->get('key')])->withErrors($errors);
            }

            $user = Auth::user();
            $user->password = Hash::make($request['password']);

            $user->save();

            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['pwdChange' => '비밀번호가 변경되었습니다. 다시 로그인해주세요.']);
        }
        return redirect()->back();
    }
    //대시보드
    public function adminDashboardView(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }

        //년도, 학기 변수 선언
        $year = Carbon::now()->timezone('Asia/Seoul')->format('Y');

        $classObjects = [];
        //수업 년도/학기 요청 처리, request값 검사
        $myClasses = collect([]);
        if($request['term']  ){
            $year = substr($request['term'], 0,4);
            $term = substr($request['term'], 5,7);
            $myClasses = MyClass::select('id', 'classObjectId', 'userId')->with(['classObject2' => function ($query) use($year, $term) {
                $query->select('id', 'gwamokNm', 'suupYear', 'suupTerm')->where('suupYear', $year)->where('suupTerm', $term);
            }])->with('prt')->get()->whereNotNull('classObject2');
        }else{
//            $year = Carbon::now()->timezone('Asia/Seoul')->format('Y');
            $year = 2021;
            $term = 10;
            //where 변경
            $myClasses = MyClass::select('id', 'classObjectId', 'userId')->with(['classObject2' => function ($query) use($year, $term) {
                $query->select('id', 'gwamokNm', 'suupYear', 'suupTerm')->where('suupYear', $year)->where('suupTerm', $term);
            }])->with('prt')->get()->whereNotNull('classObject2');
        }

        $classApplies = ClassApply::orWhere('state','wait')->orWhere('state','complete')->orderBy('id','desc')->get();
        $completeClasses = ClassApply::where('state','complete')->orderBy('id', 'desc')->get();
        $groupLists = ClassApply::select('department')->groupBy('department')->get();
        $departments = [];
            foreach ($groupLists as $groupList){
                $departments[] = ClassApply::where('department', $groupList->department)->get();
            }
//        }
        $departments = collect($departments);
        //기초교육
        $basicApplies = BasicApply::orWhere('state',0)->orWhere('state',1)->with('basic')->with('user')->orderBy('id','desc')->get();
        //컨설팅
        $consultingApplies = ConsultingApply::where('state', '0')->orWhere('state','1')->with('consulting')->with('user')->get();
        $loop = 0;
        $beforeMeca = [
            'count1' => '0',
            'count2' => '0',
            'count3' => '0',
            'count4' => '0',
        ];
        foreach ($classApplies as $classApply){
            switch ($classApply->meca){
                case 1:
                    $beforeMeca['count1']++ ;
                    break;
                case 2:
                    $beforeMeca['count2']++ ;
                    break;
                case 3:
                    $beforeMeca['count3']++ ;
                    break;
                case 4:
                    $beforeMeca['count4']++ ;
                    break;
                default:
                    break;
            }
        }

        return view('admin.dashboard',
            ['classObjects' => $classObjects,'completeClasses' => $completeClasses, 'classApplies' => $classApplies,
                'departments' => $departments, 'basicApplies' => $basicApplies,
                'consultingApplies' => $consultingApplies,'beforeMeca' => $beforeMeca, 'loop' => $loop, 'myClasses' => $myClasses, 'year' => $year, 'term' => $term]);
    }
    public function noti (Request $request) {
        $before = date('Y-m-d H:i:s', strtotime('now - 1 DAY'));
        $after = date('Y-m-d H:i:s', strtotime('now'));

        $adminLog = AdminLog::whereBetween('created_at', [$before, $after])->orderBy('created_at', 'desc')->get();

        return $adminLog;
    }
    //임시 컨트롤러
    //임시 컨트롤러
    public function apiButton(Request $request){

        //개설 신청 'wait'
        $waitClassApplies = ClassApply::where('state','wait')->orderBy('id','desc')->get();
        //기초교육
        $basicApplies = BasicApply::where('state',0)->with('basic')->with('user')->orderBy('id','desc')->get();
        //컨설팅
        $consultingApplies = ConsultingApply::where('state', '0')->with('consulting')->with('user')->get();


        return view('admin.apiButton',['waitClassApplies' => $waitClassApplies, 'basicApplies' => $basicApplies, 'consultingApplies' => $consultingApplies]);
    }
    //수업관리
    private function waitData ($request, $class1) {
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }

        if($request->has('year') && $request->has('term')){
            $year = $request['year'];
            $term = $request['term'];
        }
        $meca = $request['meca'];
        if($request['meca'] == 'M'){
            $meca = 1;
        }else if($request['meca'] == 'E'){
            $meca = 2;
        }else if($request['meca'] == 'C'){
            $meca = 3;
        }else if($request['meca'] == 'A'){
            $meca = 4;
        }else{
            $meca = null;
        }
        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
            return $item->department;
        })->toArray() : [];
        $userName = $request['daepyoGangsaNm'];
        $gwamokNm = $request['gwamokNm'];
        $gnjHakgwaNm = $request['gnjHakgwaNm'];

        if ($class1) {
            return (
                MyPage::where('year', $request->has('year') ? '=' : '!=', $request->has('year') ? $year : null)->where('semester', $request->has('term') ? '=' : '!=', $request->has('term') ? ($term - 5) / 5 : null)
                    ->with(['withCards' => function($query) use($userName, $year, $term, $meca, $daehaks, $gwamokNm, $gnjHakgwaNm) {
                        $query->where('type', 0)->with(['items3' => function($query) use($userName, $year, $term, $meca, $daehaks, $gwamokNm, $gnjHakgwaNm) {
                            $query->where('type', 4)->with(['withClassApply' => function ($query) use($userName, $meca, $daehaks, $gwamokNm, $gnjHakgwaNm) {
                                $query->with(['itemUser' => function ($query) use($userName) {
                                    $query->with(['user' => function ($_query) use($userName) {
                                        $_query->where('name', 'like', '%'.$userName.'%');
                                    }]);
                                }])
                                    ->where('state', 'wait')->where('korName', 'like', '%'.$gwamokNm.'%')
                                    ->where('department', 'LIKE','%'.$gnjHakgwaNm.'%')
                                    ->where('meca', 'LIKE','%'.$meca.'%')
                                    ->get()
                                    ->filter(function ($item) use($daehaks) {
                                        if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
                                            return false;
                                        }
                                        return $item && $item->itemUser && $item->itemUser->user;
                                    })->sortByDesc(function ($item) {
                                        return $item->id;
                                    });
                            }]);
                        }]);
                    }])
                    ->get()
                    ->flatMap(function ($values) {
                        $values->withCards->each(function ($item, $key) use($values) {
                            $item->year = $values->year;
                            $item->semester = $values->semester;
                        });

                        return $values->withCards;
                    })
                    ->flatMap(function ($values) {
                        $values->items3->each(function ($item, $key) use($values) {
                            $item->year = $values->year;
                            $item->semester = $values->semester;
                        });

                        return $values->items3;
                    })
                    ->filter(function ($item) {
                        return $item->withClassApply;
                    })
                    ->map(function ($value) {
                        $value->withClassApply->year = $value->year;
                        $value->withClassApply->semester = $value->semester;
                        
                        return $value->withClassApply;
                    })
            );
        }

        return ClassApply::with(['itemUser' => function ($query) use($userName, $year, $term) {
            $query->with(['user' => function ($_query) use($userName) {
                $_query->where('name', 'like', '%'.$userName.'%');
            }]);
        }])
            ->where('state', 'wait')->where('korName', 'like', '%'.$request['gwamokNm'].'%')
            ->where('department', 'LIKE','%'.$request['gnjHakgwaNm'].'%')
            ->where('meca', 'LIKE','%'.$meca.'%')
            ->get()
            ->filter(function ($item) use($daehaks) {
                if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
                    return false;
                }
                return $item && $item->itemUser && $item->itemUser->user;
            })->sortByDesc(function ($item) {
                return $item->id;
            });
    }
    public function class1View(Request $request){
        $perPage = 10;
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }

        $classApplies = $this->waitData($request, true);
        $classApplies = new LengthAwarePaginator(
                $classApplies->forPage(Paginator::resolveCurrentPage(), $perPage),
                $classApplies->count(),
                $perPage,
                Paginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
            );
        return view('admin.class.class1',['classApplies' => $classApplies, 'year' => $year, 'term' => $term]);
    }
    public function class1(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'allow' => ['required', 'array'],
            'allow.*' => ['integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors(['error' => '승인할 내역을 선택해주세요.']);
        }

        foreach ($request['allow'] as $id){
            $target = ClassApply::find($id);
            $target->state = 'complete';

//            $myClasses = MyClass::select('classObjectId')->get();

//            if($target->code){
//                $targetObjectId = ClassObject::select('id','suupNo')->where('suupNo',$target->code)->first();
//                $targetSuupNo = $targetObjectId->suupNo;
//                foreach($myClasses as $myClass){
//                    $suupNo = $myClass->classObject()->suupNo;
//                    if($targetSuupNo === $suupNo){
//                        $error = '"'.$target->korName.'"'.' 코드가 중복되었습니다.';
//                        return redirect()->route('admin.class1View')
//                            ->withErrors(['error' => $error]);
//                    }
//                }
//            }
//            $classObject = ClassObject::where('suupNo', $target->code)->first();
//            if (!$classObject) {
//                return redirect()->route('admin.class1View')->withErrors(['error' => '코드에 맞는 과목이 없습니다.']);
//            }

//            $target->classObjectId = $classObject->id;
//            $myClass = MyClass::create([
//                'userId' =>  $target->item()->userId,
//                'classObjectId' =>  $classObject->id,
//            ]);

//            foreach (range(1, 20) as $index) {
//                Team::create([
//                    'name' => "팀$index",
//                    'classObjectId' => $classObject->id,
//                ]);
//            }

            $target->save();

            $user = $target->item()->user()->first();

            $adminLog = AdminLog::create([
                'userId' => $user->id,
                'userName' => $user->name,
                'authority' => $user->authority,
                'eventTitle' => $target->korName,
                'eventType' => '12'
            ]);

            broadcast(
                new AdminLogEvent($adminLog->toArray())
            );

//            $classApply = ClassApply::where('classObjectId', $classObject->id)->first();
            $proLog = ProLog::create([
                'userId' => Auth::id(),
                'targetId' => $user->id,
                'userName' => 'PBL센터',
                'authority' => -1,
                'eventType' => 40,
                'eventState' => 10,
                'title' => $target ? $target->korName : null,
            ]);

            broadcast(
                new ProLogEvent($proLog->toArray(), $user->id)
            );
        }

        return redirect()->route('admin.class1View' );
    }
    public function class1Delete(Request $request, $classApplyId){
        $classApply = ClassApply::find($classApplyId);
        $item = Item::find($classApply->itemId);

        if(!$classApply || $classApply->state !== 'wait'){
            return redirect()->back();
        }
        if(!$item->remove()){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        return redirect()->route('admin.class1View');
    }
    private function completeData ($request) {
        //현재 년도 학기
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }

        if($request->has('year') && $request->has('term')){
            $year = $request['year'];
            $term = $request['term'];
        }

        $meca = $request['meca'];
        if($request['meca'] == 'M'){
            $meca = 1;
        }else if($request['meca'] == 'E'){
            $meca = 2;
        }else if($request['meca'] == 'C'){
            $meca = 3;
        }else if($request['meca'] == 'A'){
            $meca = 4;
        }else{
            $meca = null;
        }

        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
            return $item->department;
        })->toArray() : [];

        $userName = $request['daepyoGangsaNm'];
//        $sosokNm = $request['gnjSosokNm'];

        return ClassApply::with(['itemUser' => function ($query) use($userName, $request) {
            $query->with(['user' => function ($_query) use($userName, $request) {
                $_query->where('name', 'like', '%'.$userName.'%')->where('sosokNm','like', '%'.$request['gnjSosokNm'].'%');
            }]);
        }])
            ->with(['classObject' => function ($query) use($year, $term, $request) {
                $query->where('suupYear', $year)
                    ->where('suupTerm', $term)
//                ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
                    ->where('gnjDaehakNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
                    ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
                    ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
                    ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%');
            }])
            ->where('state', 'complete')->where('korName', 'like', '%'.$request['gwamokNm'].'%')
            ->where('department', 'LIKE','%'.$request['gnjHakgwaNm'].'%')
            ->where('meca', 'LIKE','%'.$meca.'%')
            ->get()->map(function($item) use($year, $term, $request){
                if($item->classObject){
                    $item->suupYear = $item->classObject->suupYear;
                    $item->suupTerm = $item->classObject->suupTerm;
                    $item->code = $item->classObject->suupNo;
                    return $item;
                }else{
                    $myPage = $item->item()->card()->myPage();
                    if (!$myPage) {
                        return false;
                    }

                    if ($myPage->year != $year) {
                        return false;
                    }
                    if ($myPage->semester * 5 + 5 != $term) {
                        return false;
                    }

                    $item->suupYear = $myPage->year;
                    $item->suupTerm = $myPage->semester * 5 + 5;
                    $item->code = '';

//                    if($request['code'] != ''){
//                        return false;
//                    }

                    return $item;
                }
            })
            ->filter(function ($item) use($daehaks) {
                if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
                    return false;
                }
                return $item && $item->itemUser && $item->itemUser->user;
            })->sortByDesc(function ($item) {
                return $item->id;
            });

    }
    public function class2View(Request $request){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $perPage = 10;

        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }

        $classApplies = $this->completeData($request);

//        return dd($classApplies);


        $classApplies = new LengthAwarePaginator(
            $classApplies->forPage(Paginator::resolveCurrentPage(), $perPage),
            $classApplies->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('admin.class.class2',['classApplies' => $classApplies, 'year' => $year, 'term' => $term]);
    }
    public function class2(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
    }
    public function class3ProfessorView(Request $request){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $perPage = 10;
        $year = 2021;
        $term = 10;

        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
            return $item->department;
        })->toArray() : [];

        $userName = $request['daepyoGangsaNm'];
//        $classApplies = ClassApply::where('state', 'complete')->where('korName', 'like', '%'.$request['gwamokNm'].'%')
////            ->where('department', 'LIKE','%'.$request['department'].'%')
//                ->with(['itemUser' => function ($query) use($userName) {
//                    $query->with(['user' => function ($_query) use($userName) {
//                        $_query->where('name', 'like', '%'.$userName.'%');
//                    }]);
//                }])
//            ->with(['classObject' => function ($query) use($year, $term, $request) {
//            $query->where('suupYear', $year)
//                ->where('suupTerm', $term)
////                ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
//                ->where('gnjDaehakNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
////                ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
//                ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%');
////                ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%');
//        }])->get()->map(function ($item) use ($daehaks) {
//            if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
//                return null;
//            }
//
//            if (is_null($item->classObject))  return null;
//            $classObject = $item->classObject;
//            $classObject->classApplyId = $item->id;
//            $classObject->korName = $item->korName;
//            $classObject->department = $item->department;
//                $classObject->itemUser = $item->itemUser;
//            return $classObject;
//        })->filter(function ($item) {
//                return $item && $item->itemUser && $item->itemUser->user;
//        })->sortBy(function ($item) {
//            return $item->gwamokNm;
//        });
        $classApplies = $this->ingData($request);

        $classApplies = new LengthAwarePaginator(
            $classApplies->forPage(Paginator::resolveCurrentPage(), $perPage),
            $classApplies->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('admin.class.class3_professor',['classApplies' => $classApplies, 'year' => $year, 'term' => $term]);
    }
    private function ingData($request){
        $perPage = 10;
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }
        $meca = $request['meca'];
        if($request['meca'] == 'M'){
            $meca = 1;
        }else if($request['meca'] == 'E'){
            $meca = 2;
        }else if($request['meca'] == 'C'){
            $meca = 3;
        }else if($request['meca'] == 'A'){
            $meca = 4;
        }else{
            $meca = null;
        }


        $userName = $request['daepyoGangsaNm'];
        $choice = $request['choice'];

        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
            return $item->department;
        })->toArray() : [];

        return ClassApply::where('state', 'ing')
            ->where('korName', 'like', '%'.$request['gwamokNm'].'%')
            ->where('department', 'LIKE','%'.$request['department'].'%')
            ->where('meca', 'LIKE','%'.$meca.'%')
            ->with('prt')
//            daepyoGangsaNm이랑 itemUser->user->name이랑 검색이 겹쳐서 비활성화
//            ->with(['itemUser' => function ($query) use($userName) {
//                $query->with(['user' => function ($_query) use($userName) {
//                    $_query->where('name', 'like', '%'.$userName.'%');
//                }]);
//            }])
            ->with(['classObject' => function ($query) use($year, $term, $request) {
                $query->where('suupYear', $year)
                    ->where('suupTerm', $term)
//                ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
                    ->where('gnjDaehakNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
                    ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
                    ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
                    ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%')
                ;
            }])->get()->map(function ($item) use ($daehaks) {
                if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
                    return null;
                }

                if (is_null($item->classObject))  return null;
                $classObject = $item->classObject;
                $classObject->classApplyId = $item->id;
                $classObject->korName = $item->korName;
                $classObject->department = $item->department;
                $classObject->itemUser = $item->itemUser;
                if($item->meca == '1'){
                    $classObject->meca = 'M';
                }elseif($item->meca == '2'){
                    $classObject->meca = 'E';
                }elseif($item->meca == '3'){
                    $classObject->meca = 'C';
                }elseif($item->meca == '4'){
                    $classObject->meca = 'A';
                }

                if ($item->cards) {
                    $scores = $item->cards->map(function ($card) {
                        $co = 0;
                        $ac = 0;
                        $fe = 0;

                        if ($card->items3) {
                            $card->items3->map(function ($item) {
                                if ($item->withComments) {
                                    $item->comments = $item->withComments->count();
                                }

                                return ['type' => $item->type, 'comments' => $item->comments];
                            });

                            $co = $card->items3->where('type', 0)->count();
                            $ac = $card->items3->where('type', '!=', 0)->count();
                            $fe = $card->items3->sum('comments');
                        }

                        return ['co' => $co, 'ac'=> $ac, 'fe' => $fe];
                    });

                    $classObject->scores = ['co' => $scores->sum('co'), 'ac' => $scores->sum('ac'), 'fe' => $scores->sum('fe')];
                }
                else {
                    $classObject->scores = [];
                }

                return $classObject;
            })->filter(function ($item) {
                return $item && $item->itemUser && $item->itemUser->user;
            })->sortByDesc(function ($item) use($choice) {
                switch ($choice) {
                    case 'gnjSosokNm':
                        return $item->gnjSosokNm;
                    case 'class':
                        return $item->korName;
                    default:
                        return $item->classApplyId;
                }
            });
    }
    public function class3View(Request $request){
        $perPage = 10;
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }
        $classApplies = $this->ingData($request);
        $classApplies = new LengthAwarePaginator(
            $classApplies->forPage(Paginator::resolveCurrentPage(), $perPage),
            $classApplies->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('admin.class.class3', ['classApplies' => $classApplies, 'year' => $year, 'term' => $term]);
    }


    private function endData($request){
        $perPage = 10;
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }
        $meca = $request['meca'];
        if($request['meca'] == 'M'){
            $meca = 1;
        }else if($request['meca'] == 'E'){
            $meca = 2;
        }else if($request['meca'] == 'C'){
            $meca = 3;
        }else if($request['meca'] == 'A'){
            $meca = 4;
        }else{
            $meca = null;
        }

        $userName = $request['daepyoGangsaNm'];
        $choice = $request['choice'];

        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
            return $item->department;
        })->toArray() : [];

        return ClassApply::where('state', 'end')
            ->where('korName', 'like', '%'.$request['gwamokNm'].'%')
            ->where('department', 'LIKE','%'.$request['department'].'%')
            ->where('meca', 'LIKE','%'.$meca.'%')
            ->with('prt')
            ->with(['classObject' => function ($query) use($year, $term, $request) {
                $query->where('suupYear', $year)
                    ->where('suupTerm', $term)
//                ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
                    ->where('gnjDaehakNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
                    ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
                    ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
                    ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%')
                ;
            }])->get()->map(function ($item) use ($daehaks) {
                if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
                    return null;
                }

                if (is_null($item->classObject))  return null;
                $classObject = $item->classObject;
                $classObject->classApplyId = $item->id;
                $classObject->korName = $item->korName;
                $classObject->department = $item->department;
                $classObject->itemUser = $item->itemUser;
                $classObject->applyCode = $item->code;

                if($item->meca == '1'){
                    $classObject->meca = 'M';
                }elseif($item->meca == '2'){
                    $classObject->meca = 'E';
                }elseif($item->meca == '3'){
                    $classObject->meca = 'C';
                }elseif($item->meca == '4'){
                    $classObject->meca = 'A';
                }

                if ($item->cards) {
                    $scores = $item->cards->map(function ($card) {
                        $co = 0;
                        $ac = 0;
                        $fe = 0;
                        if ($card->items3) {
                            $card->items3->map(function ($item) {
                                if ($item->withComments) {
                                    $item->comments = $item->withComments->count();
                                }
                                return ['type' => $item->type, 'comments' => $item->comments];
                            });
                            $co = $card->items3->where('type', 0)->count();
                            $ac = $card->items3->where('type', '!=', 0)->count();
                            $fe = $card->items3->sum('comments');
                        }

                        return ['co' => $co, 'ac'=> $ac, 'fe' => $fe];
                    });

                    $classObject->scores = ['co' => $scores->sum('co'), 'ac' => $scores->sum('ac'), 'fe' => $scores->sum('fe')];
                }
                else {
                    $classObject->scores = [];
                }

                return $classObject;
            })->filter(function ($item) {
                return $item && $item->itemUser && $item->itemUser->user;
            })->sortByDesc(function ($item) use($choice) {
                switch ($choice) {
                    case 'gnjSosokNm':
                        return $item->gnjSosokNm;
                    case 'class':
                        return $item->korName;
                    default:
                        return $item->classApplyId;
                }
            });
    }
    public function class4View(Request $request){
//        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
//            return $item->department;
//        })->toArray() : [];
//
        $perPage = 10;
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }
//        $classApplies = ClassApply::where('state', 'end')->with(['classObject' => function ($query) use($year, $term, $request) {
//            $query->where('suupYear', $year)
//                ->where('suupTerm', $term)
//                ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
//                ->where('gnjSosokNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
//                ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
//                ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
//                ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%');
//        }])->get()->map(function ($item) {
//            if (is_null($item->classObject))  return null;
//            $classObject = $item->classObject;
//            $classObject->classApplyId = $item->id;
//            return $classObject;
//        })->filter(function ($item) use ($daehaks) {
//            if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
//                return false;
//            }
//
//            return $item && $item->itemUser && $item->itemUser->user;
//        })->sortByDesc(function ($item) {
//            return $item->classApplyId;
//        });

        $classApplies = $this->endData($request);
        $classApplies = new LengthAwarePaginator(
            $classApplies->forPage(Paginator::resolveCurrentPage(), $perPage),
            $classApplies->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('admin.class.class4', ['classApplies' => $classApplies, 'year' => $year, 'term' => $term]);
    }

    private function statisticsData ($request) {
        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }
        if($request['year'] && $request['term']){
            $year = $request['year'];
            $term = $request['term'];
        }

        $meca = $request['meca'];
        if($request['meca'] == 'M'){
            $meca = 1;
        }else if($request['meca'] == 'E'){
            $meca = 2;
        }else if($request['meca'] == 'C'){
            $meca = 3;
        }else if($request['meca'] == 'A'){
            $meca = 4;
        }else{
            $meca = null;
        }

        $daehaks = Auth::user()->authority == 7 ? Daehak::where('name', Auth::user()->daehakNm)->get()->map(function ($item) {
            return $item->department;
        })->toArray() : [];

        $choice = $request['choice'];
        $userName = $request['daepyoGangsaNm'];

        return ClassApply::where('state', 'complete')
            ->where('korName', 'like', '%'.$request['gwamokNm'].'%')
            ->where('department', 'LIKE','%'.$request['department'].'%')
            ->where('meca', 'LIKE','%'.$meca.'%')
//            ->with('prt')
            ->with(['itemUser' => function ($query) use($userName) {
                $query->with(['user' => function ($_query) use($userName) {
                    $_query->where('name', 'like', '%'.$userName.'%');
                }]);
            }])
            ->with(['classObject' => function ($query) use($year, $term, $request) {
                $query->where('suupYear', $year)
                    ->where('suupTerm', $term)
//                ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
                    ->where('gnjDaehakNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
                    ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
                    ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
//                ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%')
                ;
            }])
            ->with(['classTeams2' => function ($query) {
            }])->with(['cards' => function ($query) {
                $query->with(['items3' => function ($_query) {
                    $_query->with('withComments');
                }]);
            }])->withCount('classListCount3')->get()
            ->map(function ($item) {
                $item->teamCount = $item->classTeams2->filter(function ($team) {
                    return $team->teamMembers2->count();
                })->count();

                return $item;
            })
            ->map(function ($item) use ($daehaks) {
                if (Auth::user()->authority == 7 && !in_array($item->department, $daehaks)) {
                    return null;
                }

                if (is_null($item->classObject))  return null;
                $classObject = $item->classObject;
                $classObject->classApplyId = $item->id;
                $classObject->korName = $item->korName;
                $classObject->department = $item->department;
                $classObject->itemUser = $item->itemUser;
                $classObject->teamCount = $item->teamCount;
                $classObject->meca = $item->meca;
                $classObject->members = $item->class_list_count3_count;


                if ($item->cards) {
                    $scores = $item->cards->map(function ($card) {
                        $co = 0;
                        $ac = 0;
                        $fe = 0;
                        if ($card->items3) {
                            $card->items3->map(function ($item) {
                                if ($item->withComments) {
                                    $item->comments = $item->withComments->count();
                                }
                                return ['type' => $item->type, 'comments' => $item->comments];
                            });
                            $co = $card->items3->where('type', 0)->count();
                            $ac = $card->items3->where('type', '!=', 0)->count();
                            $fe = $card->items3->sum('comments');
                        }

                        return ['co' => $co, 'ac'=> $ac, 'fe' => $fe];
                    });

                    $classObject->scores = ['co' => $scores->sum('co'), 'ac' => $scores->sum('ac'), 'fe' => $scores->sum('fe')];
                }
                else {
                    $classObject->scores = [];
                }

                return $classObject;
            })->filter(function ($item) {
                return $item && $item->itemUser && $item->itemUser->user;
            })->sortByDesc(function ($item) use($choice) {
                switch ($choice) {
                    case 'gnjSosokNm':
                        return $item->gnjSosokNm;
                    case 'class':
                        return $item->korName;
                    default:
                        return $item->id;
                }
            });
    }
    public function classStatisticsView(Request $request){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $perPage = 10;

        $time = \Carbon\Carbon::now();
        $year = $time->format('Y');
        $term = $time->format('n');
        if($term <= 6){
            $term = '10';
        }elseif($term >= 7){
            $term = '20';
        }

        $classApplies = $this->statisticsData($request);

//        if($request['choice'] === 'gnjSosokNm'){
//            $classApplies = ClassApply::where('state', 'complete')->with(['classObject' => function ($query) use($year, $term, $request) {
//                $query->where('suupYear', $year)
//                    ->where('suupTerm', $term)
//                    ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
//                    ->where('gnjSosokNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
//                    ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
//                    ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
//                    ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%');
//            }])->get()->map(function ($item) {
//                if (is_null($item->classObject))  return null;
//                $classObject = $item->classObject;
//                $classObject->classApplyId = $item->id;
//                return $classObject;
//            })->filter(function ($item) {
//                return $item;
//            })->sortBy(function ($item) {
//                return $item->gnjSosokNm;
//            });
//        }elseif($request['choice'] === 'class'){
//            $classApplies = ClassApply::where('state', 'complete')->with(['classObject' => function ($query) use($year, $term, $request) {
//                $query->where('suupYear', $year)
//                    ->where('suupTerm', $term)
//                    ->where('gwamokNm', 'LIKE','%'.$request['gwamokNm'].'%')
//                    ->where('gnjSosokNm', 'LIKE','%'.$request['gnjSosokNm'].'%')
//                    ->where('gnjHakgwaNm','LIKE', '%'. $request['gnjHakgwaNm'].'%' )
//                    ->where('suupNo', 'LIKE', '%'. $request['suupNo'].'%')
//                    ->where('daepyoGangsaNm', 'LIKE', '%'. $request['daepyoGangsaNm'] .'%');
//            }])->get()->map(function ($item) {
//                if (is_null($item->classObject))  return null;
//                $classObject = $item->classObject;
//                $classObject->classApplyId = $item->id;
//                return $classObject;
//            })->filter(function ($item) {
//                return $item;
//            })->sortBy(function ($item) {
//                return $item->gwamokNm;
//            });
//        }

        $classApplies = new LengthAwarePaginator(
            $classApplies->forPage(Paginator::resolveCurrentPage(), $perPage),
            $classApplies->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('admin.class.statistics',['classApplies' => $classApplies, 'year' => $year, 'term' => $term]);

    }
    public function classCertificateView(Request $request){

        return view('admin.class.certificate');
    }
    // 수업관리 상세
    public function classDetail1View (Request $request, $classApplyId){
        $classApply = ClassApply::where('id', $classApplyId)->first();
        if(!$classApply){
            return redirect()->back();
        }
        return view('admin.class.class_detail1', ['classApply' => $classApply] );
    }
    public function classDetail2View (Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $perPage = 10;
        $classApply = ClassApply::where('id', $classApplyId)->first();
        if(!$classApply){
            return redirect()->back();
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }

        $type = $request['type'];
        $content = $request['content'];

        if ($request->has('type') && $request->has('content')) {
            $validator = Validator::make($request->all(), [
                'type' => ['required', 'string', 'in:name,email'],
                'content' => ['nullable', 'string', 'min:2']
            ]);
            if (!$validator->fails()) {
                $type = $request['type'];
                $content = $request['content'];

                $classLists = ClassList::where('classObjectId', $classApply->classObjectId)
                    ->with(['withUser' => function ($query) use($type, $content) {
                    $query->where($type, 'like', '%'.$content.'%');
                }])->get()->whereNotNull('withUser');

                $paginate = new LengthAwarePaginator(
                    $classLists->forPage(Paginator::resolveCurrentPage(), $perPage),
                    $classLists->count(),
                    $perPage,
                    Paginator::resolveCurrentPage(),
                    ['path' => Paginator::resolveCurrentPath()]
                );
                $classLists = $paginate;

                return view('admin.class.class_detail2',['classApply' => $classApply, 'classLists' => $classLists]);
            }else{
                $errors = $validator->errors();
                if ($errors->has('content')) {
                    return redirect()->route('admin.classDetail2View', [$classApply->id])->withErrors(['content_length' => '검색어를 두자 이상 입력하세요.']);
                }
                return redirect()->back()->withErrors($validator->errors());
            }
        }

        $classLists = ClassList::whereNotNull('userId')->where('classObjectId', $classApply->classObjectId)->with('withUser')->paginate($perPage);

        return view('admin.class.class_detail2',['classApply' => $classApply, 'classLists' => $classLists]);
    }
    public function classDetail3View (Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        //진행중인 수업의 학기가 / 1학기는 7월 2학기는 2월이 지나면 수업완료로 변경 되어야 함
        $classApply = ClassApply::find($classApplyId);
        if(!$classApply){
            return redirect()->back();
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }

//        $classObjectName = ClassObject::where('id', $classObjectId)->first();
        $teams = Team::where('classObjectId', $classApply->classObjectId)->get();
        $members = ClassList::where('classObjectId', $classApply->classObjectId)->get();
        foreach ($members as $member) {
            if ($member->userId) {
                $member->userName = $member->user()->name;
            }
        }
        $members = $members->filter(function ($member) {
            return $member->userId;
        });
        $members = $members->sortBy('userName');

        $classApply = ClassApply::where('ClassObjectId', $classApply->classObjectId)->first();
        return view('admin.class.class_detail3',
//            ['classObjectId' => $classApply->classObjectId, 'teams' => $teams, 'members' => $members,'classApply'=> $classApply]);
            ['classObjectId' => $classApply->classObjectId, 'teams' => $teams, 'members' => $members,'classApply'=> $classApply]);
    }
    public function classDetail4View (Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $perPage = 10;
        $classApply = ClassApply::find($classApplyId);

        if (!$classApply) {
            return redirect()->route('lectureList');
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }

        $cards = Card::where('classObjectId', $classApply->classObjectId)->with('withItemsWithProblems', function ($query) {
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

        $paginate = new LengthAwarePaginator(
            $data->forPage(Paginator::resolveCurrentPage(), $perPage),
            $data->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
        $classApply = ClassApply::where('classObjectId', $classApply->classObjectId)->first();
        return view('admin.class.class_detail4', [ 'problems' => $paginate ,'classApply'=> $classApply ]);
    }
    public function classDetail4DetailView (Request $request,$classApplyId, $problemAnalysisId){
        $classApply = ClassApply::find($classApplyId);
        $problemAnalysis = ProblemAnalysis::find($problemAnalysisId);
//        return dd($problemAnalysis);


        return view('admin.class.class_detail4_detail', ['classApply' => $classApply,'problemAnalysis' => $problemAnalysis]);
    }
    public function classDetail5View (Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }

        $classApply = ClassApply::find($classApplyId);
        if (!$classApply) {
            return redirect()->back();
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }

        $cards = Card::where('classObjectId', $classApply->classObjectId)->with('withItemsWithTeamActivity', function ($query) {
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

        return view ('admin.class.class_detail5',['teamActivities' => $paginate ,'classApply'=> $classApply]);
    }

    public function classDetail5DetailView(Request $request, $classApplyId, $teamActivityId){
        $classApply = ClassApply::find($classApplyId);
        $teamActivity = TeamActivity::find($teamActivityId);

        return view('admin.class.class_detail5_detail',['classApply' => $classApply, 'teamActivity' => $teamActivity]);
    }
    public function classDetail6View(Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $classApply = ClassApply::find($classApplyId);
        if(!$classApply){
            return redirect()->back();
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }


        $teams = Team::where('classObjectId', $classApply->classObjectId)->get();

        return view('admin.class.class_detail6',['teams' => $teams,'classApply' => $classApply]);
    }
    public function classDetail7View(Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $classApply = ClassApply::find($classApplyId);
        if(!$classApply){
            return redirect()->back();
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }

        $cards = Card::where('classObjectId', $classApply->classObjectId)->with('withItemsWithReflections', function ($query) {
            $query->where('type', 2)->orderBy('id','desc');
        })->get()->where('withItemsWithReflections', '!=', '[]');

        $data = [];
        if ($cards) {
            foreach ($cards as $card) {
                foreach ($card->withItemsWithReflections as $item) {
                    $data[] = $item;
                }
            }
        }
//        if(!$data){
//            return redirect()->back();
//        }

        $data = collect($data);

//        return dd($data);

        $perPage = 10;
        $paginate = new LengthAwarePaginator(
            $data->forPage(Paginator::resolveCurrentPage(), $perPage),
            $data->count(),
            $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('admin.class.class_detail7',['reflections' => $paginate, 'classApply' => $classApply]);
    }
    public function classDetail7DetailView(Request $request, $classApplyId, $reflectionId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $classApply = ClassApply::find($classApplyId)->first();
        $reflection = ReflectionLog::where('itemId',$reflectionId)->first();

//        return dd($reflection->studentId);

        return view('admin.class.class_detail7_detail',['reflection' => $reflection, 'classApply' => $classApply]);
    }
    public function classDetail8View(Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
        $classApply = ClassApply::find($classApplyId);
        $classLists = ClassList::whereNotNull('userId')->where('classObjectId', $classApply->classObjectId)->with('withUser')->get();
        if(!$classApply){
            return redirect()->back();
        }elseif($classApply->classObjectId === null){
            return redirect()->back();
        }

        return view('admin.class.class_detail8',['classApply' => $classApply ,'classLists' => $classLists]);
    }

//    public function classDetail8View(Request $request, $classApplyId){
//        if(Auth::user()->authority != 3){
//            Auth::logout();
//            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
//        }
//        $classApply = ClassApply::find($classApplyId);
//        if(!$classApply){
//            return redirect()->back();
//        }elseif($classApply->classObjectId === null){
//            return redirect()->back();
//        }
//        return view('admin.class.class_detail8',['classApply' => $classApply]);
//    }
    //참여자 관리
    public function memberView(Request $request){
        if(!in_array(Auth::user()->authority, [3, 9])){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $actions = [];
        $social = $request['social'];
        $perPage = 20;
        $actions = $request['action'];
        if($actions == '0'){
            $actions = ['0'];
        }

        $users = User::where(function($query) use($actions){
            if($actions == '' || in_array('0',$actions)){
                return $query->whereNotNull('authority');
            }else{
                return $query->whereIn('authority',$actions);
            }
        })->where(function($query) use($request, $actions){
                if($request['type'] == 'name'){
                    return $query->where('name','LIKE', '%'.$request['content'].'%');
                }elseif($request['type'] == 'email'){
                    return $query->where('email','LIKE', '%'.$request['content'].'%');
                }elseif($request['type'] == 'contact'){
                    return $query->where('contact','LIKE', '%'.$request['content'].'%');
                }elseif($request['type'] == 'gaeinNo'){
                    return $query->where('gaeinNo','LIKE', '%'.$request['content'].'%');
                }
            })->where(function($query) use($social){
                if($social == 's1'){
                    return true;
                }elseif($social == 's2'){
                    return $query->whereNull('social');
                }elseif($social == 's3'){
                    return $query->whereNotNull('social');
                }
            })->sortable()->paginate($perPage);
//            ->sortByDesc(function ($item) {
//                return $item->id;
//            });

        return view('admin.member.member',['users' => $users, 'actions' => $actions, 'social' => $social]);
    }

    public function memberDetailProView (Request $request, $userId){
        if(Auth::user()->authority != 3 && Auth::user()->authority != 8){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $user = User::where('id',$userId)->first();
        if(!$user){
            return redirect()->back();
        }

        $users = User::where('authority', 4)->get();

        return view('admin.member.member_detail_pro', ['user'=>$user, 'users' => $users]);
    }
    public function memberDetailPro (Request $request, $userId){
        if(Auth::user()->authority != 3 && Auth::user()->authority != 8){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $user = User::find($userId);
        if(!$user || $user->authority != 2 || $user->authority != 7){
//            return redirect()->back();
//            if($user){
//                return redirect()->route('admin.memberDetailProView',['userId' => $user->id])->withErrors(['error' => '접근 권한이 없습니다.']);
//            }else{
                return redirect()->back();
//            }

        }
        $validator = Validator::make($request->all(),[
            'basic' => ['required', 'boolean'],
            'consulting' => ['required', 'boolean'],
            'consultant' => ['exclude_if:consulting,0', 'integer', 'min:1'],
        ]);

        if($validator->fails()) {
            $errors = $validator->errors();
            return redirect()
                ->route('admin.memberDetailProView',['userId' => $userId])
                ->withErrors($errors)->withInput($request->input());
        }

        $user->basicTarget = $request['basic'];
        $user->consultingTarget = $request['consulting'];
        if ($request['consulting']) {
//            $user->consultantId = $request['consultant'];
        }

        $user->save();

        return redirect()->route('admin.memberView')->withErrors(['error' => '저장되었습니다.']);
    }
    public function memberDetailStuView (Request $request, $userId){
        if(Auth::user()->authority != 3 && Auth::user()->authority != 8){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $user = User::find($userId);
        if(!$user || $user->authority != 1 && $user->authority != 6){
            return redirect()->back();
        }
//        return dd($user->contact);
        return view('admin.member.member_detail_stu',['user' => $user]);
    }
    public function memberDetailStu(Request $request, $userId){
        $user = User::find($userId);
        if(Auth::user()->authority != 3 && Auth::user()->authority != 8){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        if(!$user || $user->authority != 1 && $user->authority != 6){
            return redirect()->back();
        }
        return redirect()->route('admin.memberView')->withErrors(['error' => '저장되었습니다.']);
    }
    public function memberDetailOutView (Request $request, $userId){
        if(Auth::user()->authority != 3 && Auth::user()->authority != 8){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $user = User::find($userId);
        if(!$user || $user->authority != 4 && $user->authority != 5 && $user->authority != 0){
            return redircet()->back();
        }

        return view('admin.member.member_detail_out',['user' => $user]);
    }
    public function memberDetailOut(Request $request, $userId) {
        if(Auth::user()->authority != 3 && Auth::user()->authority != 8){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $user = User::find($userId);
        $code = $request['code'];
        $authority = $request['authority'];

        if($authority == '일반'){
            $authority = '0';
        }elseif($authority == '컨설턴트'){
            $authority = '4';
        }elseif($authority == '공동교수자/외부전문가'){
            $authority = '5';
        }

        //아직 변경할 수 있는 값이 Code밖에 없기 때문에 code만 변경 해놨슴
        if($user->code !== $code || $user->authority !== $authority){
            User::find($userId)->update([
                'code' => $code,
                'authority' => $authority
            ]);
//            User::find($userId)->update(['authority' => $authority]);
            return redirect()->route('admin.memberView')->withErrors(['error' => '저장되었습니다.']);
        }



//        elseif($user->code == $code){
//            return redirect()->back()->withErrors(['error' => '코드가 기존 값과 동일합니다.']);
//        }

    }
    public function memberDetailEtcView (Request $request, $userId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $user = User::find($userId);
        if(!$user || $user->authority != 9){
            return redircet()->route('admin.memberView');
        }

        return view('admin.member.member_detail_out',['user' => $user]);
    }


    //기초교육, 컨설팅, 프로그램(공모전) 엑셀Export
    public function applicantExport(Request $request){

        $applies = ['1','2','3'];
        $applies = collect($applies);


        if($request['state'] == 'basic1'){
            switch ($request['targetAction']){
                case('allTarget'):
                    $applies = BasicApply::where('state',0)->orderBy('id','desc')->with('basic')->with('user')->get()->map(function($item){
                        $item->name = $item->user->name;
                        $item->email = $item->user->email;
                        $item->title = $item->basic->title;
                        $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                        $item->startDate = $item->basic->startDateTime; //시작 시간
                        $item->endDate = $item->basic->endDateTime; // 끝 시간

                        if($item->user->basicTarget == '1') {
                            $item->target = '대상자';
                        }else{
                            $item->target = '비대상자';
                        }

                        return $item;
                    });
                    break;
                case('nonTargetPerson'):
                    $applies = BasicApply::where('state',0)->orderBy('id','desc')->with('basic')->with('user')->whereHas('user', function($query) {
                            $query->where('basicTarget', 0);
                        })->get()->map(function($item){
                        $item->name = $item->user->name;
                        $item->email = $item->user->email;
                        $item->title = $item->basic->title;
                        $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                        $item->startDate = $item->basic->startDateTime; //시작 시간
                        $item->endDate = $item->basic->endDateTime; // 끝 시간
                        if($item->user->basicTarget == '1') {
                            $item->target = '대상자';
                        }else{
                            $item->target = '비대상자';
                        }


                        return $item;
                    });
                    break;
                case('targetPerson'):
                    $applies = BasicApply::where('state',0)->orderBy('id','desc')->with('basic')->with('user')
                        ->whereHas('user', function($query) {
                            $query->where('basicTarget', 1);
                        })->get()->map(function($item){
                            $item->name = $item->user->name;
                            $item->email = $item->user->email;
                            $item->title = $item->basic->title;
                            $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                            $item->startDate = $item->basic->startDateTime; //시작 시간
                            $item->endDate = $item->basic->endDateTime; // 끝 시간

                            if($item->user->basicTarget == '1') {
                                $item->target = '대상자';
                            }else{
                                $item->target = '비대상자';
                            }

                            return $item;
                        });
                    break;
                default:
                    $applies = BasicApply::where('state',0)->orderBy('id','desc')->with('basic')->with('user')->get()->map(function($item){
                        $item->name = $item->user->name;
                        $item->email = $item->user->email;
                        $item->title = $item->basic->title;
                        $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                        $item->startDate = $item->basic->startDateTime; //시작 시간
                        $item->endDate = $item->basic->endDateTime; // 끝 시간

                        if($item->user->basicTarget == '1') {
                            $item->target = '대상자';
                        }else{
                            $item->target = '비대상자';
                        }

                        return $item;
                    });
                    break;
            }
        }elseif($request['state'] == 'basic2'){
            $applies = BasicApply::where('state','1')->with('basic')->with('user')->orderBy('id','desc')->get()->map(function($item){
                if($item->user->basicTarget == '0' || 0){
                    $item->target = '비대상자';
                }elseif($item->user->basicTarget == '1' || 1){
                    $item->target = '대상자';
                }
                $item->name = $item->user->name;
                $item->email = $item->user->email;
                $item->title = $item->basic->title;
                $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                $item->startDate = $item->basic->startDateTime; //시작 시간
                $item->endDate = $item->basic->endDateTime; // 끝 시간

                return $item;
            });
        }elseif($request['state'] == 'consulting1'){
            switch ($request['targetAction']){
                case 'allTarget':
                    $applies = ConsultingApply::where('state', 0)->orderBy('id', 'desc')->with('consulting')->with('user')->get()
                    ->map(function($item){
                        if($item->user->consultingTarget == '0' || 0){
                            $item->target = '비대상자';
                        }elseif($item->user->consultingTarget == '1' || 1){
                            $item->target = '대상자';
                        }
                        $item->name = $item->user->name;
                        $item->email = $item->user->email;
                        $item->title = $item->consulting->title;
                        $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                        $item->startDate = $item->consulting->startDateTime; //시작 시간
                        $item->endDate = $item->consulting->endDateTime; // 끝 시간

                        return $item;
                    });
                    break;
                //대상
                case 'targetPerson':
                    $applies = ConsultingApply::where('state', 0)->orderBy('id', 'desc')->with('consulting')->with('user')
                        ->whereHas('user', function ($query) {
                            $query->where('consultingTarget', 1);
                        })->get()->map(function($item){
                            if($item->user->consultingTarget == '0' || 0){
                                $item->target = '비대상자';
                            }elseif($item->user->consultingTarget == '1' || 1){
                                $item->target = '대상자';
                            }
                            $item->name = $item->user->name;
                            $item->email = $item->user->email;
                            $item->title = $item->consulting->title;
                            $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                            $item->startDate = $item->consulting->startDateTime; //시작 시간
                            $item->endDate = $item->consulting->endDateTime; // 끝 시간

                            return $item;
                        });
                    break;
                //비대상
                case 'nonTargetPerson':
                    $applies = ConsultingApply::where('state', 0)->orderBy('id', 'desc')
                        ->with('consulting')
                        ->with('user')->whereHas('user', function ($query) {
                            $query->where('consultingTarget', 0);
                        })->get()->map(function($item){
                            if($item->user->consultingTarget == '0' || 0){
                                $item->target = '비대상자';
                            }elseif($item->user->consultingTarget == '1' || 1){
                                $item->target = '대상자';
                            }
                            $item->name = $item->user->name;
                            $item->email = $item->user->email;
                            $item->title = $item->consulting->title;
                            $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                            $item->startDate = $item->consulting->startDateTime; //시작 시간
                            $item->endDate = $item->consulting->endDateTime; // 끝 시간

                            return $item;
                        });
                    break;
                default:
                    $applies = ConsultingApply::where('state', '0')->with('consulting')->with('user')->get()->map(function($item){
                        if($item->user->consultingTarget == '0' || 0){
                            $item->target = '비대상자';
                        }elseif($item->user->consultingTarget == '1' || 1){
                            $item->target = '대상자';
                        }
                        $item->name = $item->user->name;
                        $item->email = $item->user->email;
                        $item->title = $item->consulting->title;
                        $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                        $item->startDate = $item->consulting->startDateTime; //시작 시간
                        $item->endDate = $item->consulting->endDateTime; // 끝 시간

                        return $item;
                    });
                    break;
            }
        }elseif($request['state'] == 'consulting2'){
            $applies = ConsultingApply::where('state','1')->with('consulting')->with('user')->get()->map(function ($item){
                if($item->user->basicTarget == '0' || 0){
                    $item->target = '비대상자';
                }elseif($item->user->basicTarget == '1' || 1){
                    $item->target = '대상자';
                }
                $item->name = $item->user->name;
                $item->email = $item->user->email;
                $item->title = $item->consulting->title;
                $item->date = date('Y.m.d', strtotime($item->created_at));  //신청일
                $item->startDate = $item->consulting->startDateTime; //시작 시간
                $item->endDate = $item->consulting->endDateTime; // 끝 시간

                return $item;
            });
        //공모전은 대상자 비대상자 없음
        }elseif($request['state'] == 'program1'){
            $applies = CompetitionApply::where('state', 1)->with('competition')->with('user')->orderBy('id', 'desc')->get()->map(function($item){
                $item->name = $item->user->name;
                $item->email = $item->user->email;
                $item->title = $item->competition->title;
                $item->year = $item->competition->year;
                $item->month = $item->competition->month;
                $item->day = $item->competition->day;

                return $item;
            });
        }elseif($request['state'] == 'program2'){
            $applies = CompetitionApply::where('state', 2)->with('competition')->with('user')->orderBy('id', 'desc')->get()->map(function($item){
                $item->name = $item->user->name;
                $item->email = $item->user->email;
                $item->title = $item->competition->title;
                $item->year = $item->competition->year;
                $item->month = $item->competition->month;
                $item->day = $item->competition->day;

                return $item;
            });
        }

//        return dd($applies);
        $newArr = [];
        if($request['state'] == 'basic1'){
            $newArr[] =  [
                '번호' => '번호',
                '신청자명' => '신청자명',
                '대상자여부' => '대상자여부',
                '신청자 아이디' => '신청자 아이디',
                '신청일' => '신청일',
                '교육일시' => '교육일시'
            ];
        }
        elseif($request['state'] == 'basic2'){
            $newArr[] =  [
                '번호'=> '번호',
                '신청자명'=> '신청자명',
                '대상자여부'=> '대상자여부',
                '신청자아이디'=> '신청자아이디',
                '신청일'=> '신청일',
                '신청한컨설팅명'=> '신청한컨설팅명',
                '시작시간'=> '시작시간',
                '종료시간'=> '종료시간',
            ];
        }elseif($request['state'] == 'consulting1'){
            $newArr[] =  [
                '번호' => '번호',
                '신청자명' => '신청자명',
                '대상자여부' => '대상자여부',
                '신청자아이디' => '신청자아이디',
                '신청일' => '신청일',
                '신청한컨설팅명' => '신청한컨설팅명',
                '시간' => '시간',
                '상태' => '상태',
            ];
        }elseif($request['state'] == 'consulting2'){
            $newArr[] =  [
                '번호' => '번호',
                '신청자명' => '신청자명',
                '대상자여부' => '대상자여부',
                '신청자아이디' => '신청자아이디',
                '신청일' => '신청일',
                '신청한컨설팅명' => '신청한컨설팅명',
                '시작시간' => '시작시간',
                '종료시간' => '종료시간',
                '상태' => '상태',
            ];
        }elseif($request['state'] == 'program1'){
            $newArr[] =  [
                '번호' => '번호',
                '신청자명' => '신청자명',
                '신청자 아이디' => '신청자 아이디',
                '한양대 아이디' => '한양대 아이디',
                '신청자 공모전명' => '신청자 공모전명',
                '공모전 날짜' => '공모전 날짜',
                '상태' => '상태',
            ];
        }elseif($request['state'] == 'program2'){
            $newArr[] =  [
                '번호' => '번호',
                '신청자명' => '신청자명',
                '신청자 아이디' => '신청자 아이디',
                '한양대 아이디' => '한양대 아이디',
                '신청자 공모전명' => '신청자 공모전명',
                '공모전 날짜' => '공모전 날짜',
                '상태' => '상태',
            ];
        }else{
            $newArr[] =  [
                '번호'=> '번호',
                '신청자명'=> '신청자명',
                '대상자여부'=> '대상자여부',
                '신청자아이디'=> '신청자아이디',
                '신청일'=> '신청일',
                '신청한컨설팅명'=> '신청한컨설팅명',
                '시작시간'=> '시작시간',
                '종료시간'=> '종료시간',
            ];
        }
        $newArr = collect($newArr);
        $newArr = $newArr->merge($applies->map(function ($element, $key) use($request) {
            switch ($request['state']) {

                case 'basic1':
                    return [
                        '번호' => $element->id ? $element->id : '',
                        '신청자명' => $element->name,
                        '대상자여부' => $element->target,
                        '신청자 아이디' => $element->email,
                        '신청일' => $element->date,
                        '교육일시' => $element->startDate,
                        '상태' => '신청'
                    ];
                case 'basic2' :
                    return [
                        '번호' => $element->id,
                        '신청자명' => $element->name,
                        '대상자여부' => $element->target,
                        '신청자아이디' => $element->email,
                        '신청일' => $element->date,
                        '신청한컨설팅명' => $element->title,
                        '시작시간' => $element->startDate,
                        '종료시간' => $element->endDate,
                        '상태' => '승인',
                    ];
                case 'consulting1':
                    return [
                        '번호' => $element->id,
                        '신청자명' => $element->name,
                        '대상자여부' => $element->target,
                        '신청자아이디' => $element->email,
                        '신청일' => $element->date,
                        '신청한컨설팅명' => $element->title,
                        '시간' => $element->startDate,
                        '상태' => '신청',
                    ];
                case 'consulting2':
                    return [
                        '번호' => $element->id,
                        '신청자명' => $element->name,
                        '대상자여부' => $element->target,
                        '신청자아이디' => $element->email,
                        '신청일' => $element->date,
                        '신청한컨설팅명' => $element->title,
                        '시작시간' => $element->startDate,
                        '종료시간' => $element->endDate,
                        '상태' => '승인',
                    ];
                case 'program1':
                    return [
                        '번호' => $element->id,
                        '신청자명' => $element->name,
                        '신청자 아이디' => $element->email,
                        '한양대 아이디' => '',
                        '신청한 공모전명' => $element->title,
                        '공모전 날짜' => $element->year.'.'.$element->month.'.'.$element->day,
                        '상태' => '신청',
                    ];
                case 'program2':
                    return [
                        '번호' => $element->id,
                        '신청자명' => $element->name,
                        '신청자 아이디' => $element->email,
                        '한양대 아이디' => '',
                        '신청한 공모전명' => $element->title,
                        '공모전 날짜' => $element->year.'.'.$element->month.'.'.$element->day,
                        '상태' => '승인',
                    ];
            }
        }));

        if($request['state'] == 'basic1' || $request['state'] == 'basic2' || $request['state'] == 'basic3'){
            return Excel::download(new InvoicesExport($newArr), '기초교육.xlsx');
        }elseif($request['state'] == 'consulting1' || $request['state'] == 'consulting2' || $request['state'] == 'consulting3'){
            return Excel::download(new InvoicesExport($newArr), '컨설팅.xlsx');
        }elseif($request['state'] == 'program1' || $request['state'] == 'program2' || $request['state'] == 'program3'){
            return Excel::download(new InvoicesExport($newArr), '프로그램.xlsx');
        }
        
    }

    //기초교육관리
    public function basic1View(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $basicApplies = BasicApply::where('state',0)->with('basic')->with('user')->orderBy('id','desc')->paginate(10);
        //전체 대상 비대상
        if($request->input('action')){
            switch ($request->input('action')) {
                //전체
                case 'allTarget':
                    $basicApplies = BasicApply::where('state',0)->orderBy('id','desc')->with('basic')->with('user')->paginate(10);
                    break;
                //대상
                case 'targetPerson':
                    $basicApplies = BasicApply::where('state',0)->orderBy('id','desc')->with('basic')->with('user')
                        ->whereHas('user', function($query) {
                        $query->where('basicTarget', 1);
                    })->paginate(10);
                    break;
                //비대상
                case 'nonTargetPerson':
                    $basicApplies = BasicApply::where('state',0)->orderBy('id','desc')
                        ->with('basic')
                        ->with('user')->whereHas('user', function($query) {
                            $query->where('basicTarget', 0);
                        })->paginate(10);
                    break;
            }
        }
        return view('admin.basic.basic1',['basicApplies' => $basicApplies]);
    }
    public function basic1 (Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }

        if(!$request['allow']){
            return redirect()->back()->withErrors(['error' => '승인할 내역을 선택해주세요.']);
        }
        else{

            foreach ($request['allow'] as $allow){
//                $update = BasicApply::where('id',$allow)->update(['state' => '1']);

                $ba = BasicApply::where('id', $allow)->first();
                if ($ba) {
                    if (strtotime($ba->basic()->first()->endDateTime) < strtotime('now')) {
                        $ba->state = 3; //종료
                    }else{
                        $ba->state = 1;
                    }
                    $ba->save();

                    $proLog = ProLog::create([
                        'userId' => Auth::id(),
                        'targetId' => $ba->userId,
                        'userName' => 'PBL센터',
                        'authority' => -1,
                        'eventType' => 1000,
                        'eventState' => 10,
                        'title' => $ba->title,
                    ]);

                    broadcast(
                        new ProLogEvent($proLog->toArray(), $ba->userId)
                    );
                }
            }
            return redirect()->route('admin.basic1View');
//            $basicApplies = BasicApply::where('state','0')->with('basic')->with('user')->orderBy('id','desc')->paginate(10);
//            return view('admin.class.class1',['basicApplies' => $basicApplies]);
        }
    }
    public function basic2View(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $basicApplies = BasicApply::where('state','1')->with('basic')->with('user')->orderBy('id','desc')->paginate(10);

        return view('admin.basic.basic2',['basicApplies' => $basicApplies]);
    }
    public function basic3View (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $week = date("w");
        $weeks = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];

        $validator = Validator::make($request->only('year', 'month'), [
            'year' => ['required', 'integer', 'min:1900', 'max:2500'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'day' => ['integer', 'min:1', 'max:31'],
        ]);

        if (!$validator->fails()) {
            $year = $request['year'];
            $month = $request['month'];
            if ($request->has('day')) {
                $day = $request['day'];
            }

            $week = date('w', strtotime($year.'-'.$month.'-'.$day));
        }

        $consultants = User::where('authority', 4)->get();

        return view('admin.basic.basic3', ['year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week], 'consultants' => $consultants]);
    }
    public function basic3Reload (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
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

        $basics = Basic::whereBetween('startDateTime', [$year.'-'.$month.'-1', ($month >= 12 ? $year + 1 : $year).'-'.($month >= 12 ? 1 : $month + 1).'-1'])
            ->with('consultant')
            ->with('applies')->get()
        ->toArray();

        $_days = [];

        foreach ($basics as $basic) {
            $start = (int)date('d', strtotime($basic['startDateTime']));
            $end = (int)date('d', strtotime($basic['endDateTime']));

            while ($start <= $end) {
                $basic['day'] = $start;
                $_days[] = $basic;

                $start += 1;
            }
        }

        $basics = collect($_days);

        $days = $basics->groupBy(function ($element) {
            return $element['day'];
//            return (int)date('d', strtotime($element->startDateTime));
        });

//        return dd($days->toArray());

        return ['days' => $days, 'year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week]];
    }
    public function basic3 (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }

        $validator = Validator::make($request->all(), [
            'startDateTime' => ['required', 'date', 'before:endDateTime'],
            'endDateTime' => ['required', 'date'],
//            'consultantId' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string'],
//            'maxMemberCount' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return false;
        }
//        else {
//            $start = strtotime($request['startDateTime']);
//            if ($start < strtotime(date('Y-m-d'))) {
//                return false;
//            }
//        }

//        $consultant = User::find($request['consultantId']);
//        if (!$consultant || $consultant->authority != 4) {
//            return false;
//        }

        $date = strtotime($request['date']);

        Basic::create([
            'startDateTime' => date('Y-m-d H:i:s', strtotime($request['startDateTime'])),
            'endDateTime' => date('Y-m-d H:i:s', strtotime($request['endDateTime'])),
//            'consultantId' => $request['consultantId'],
            'title' => $request['title'],
//            'maxMemberCount' => $request['maxMemberCount'],
        ]);

        return true;
    }
    public function basic3Modi (Request $request, $basicId) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }

        $validator = Validator::make($request->all(), [
            'startDateTime' => ['required', 'date', 'before:endDateTime'],
            'endDateTime' => ['required', 'date'],
//            'consultantId' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string'],
//            'maxMemberCount' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return false;
        }
//        else {
//            $start = strtotime($request['startDateTime']);
//            if ($start < strtotime(date('Y-m-d'))) {
//                return false;
//            }
//        }

//        $consultant = User::find($request['consultantId']);
//        if (!$consultant || $consultant->authority != 4) {
//            return false;
//        }

//        $date = strtotime($request['date']);

        $basic = Basic::find($basicId);
        if (!$basic) {
            return false;
        }

//        if (BasicApply::where('id', $basic->id)->where('state', 1)->count() > $request['maxMemberCount']) {
//            return false;
//        }

        $basic->startDateTime = date('Y-m-d H:i:s', strtotime($request['startDateTime']));
        $basic->endDateTime = date('Y-m-d H:i:s', strtotime($request['endDateTime']));
//        $basic->consultantId = $request['consultantId'];
        $basic->title = $request['title'];
//        $basic->maxMemberCount = $request['maxMemberCount'];
        $basic->save();

//        Basic::create([
//            'startDateTime' => date('Y-m-d H:i:s', strtotime($request['startDateTime'])),
//            'endDateTime' => date('Y-m-d H:i:s', strtotime($request['endDateTime'])),
//            'consultantId' => $request['consultantId'],
//            'title' => $request['title'],
//            'maxMemberCount' => $request['maxMemberCount'],
//        ]);

        return true;
    }
    //컨설팅 관리
    public function consulting1View(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $consultingApplies = ConsultingApply::where('state', '0')->with('consulting')->with('user')->paginate(10);
        if ($request->input('action')) {

            switch ($request->input('action')) {
                //전체
                case 'allTarget':
                    $consultingApplies = ConsultingApply::where('state', 0)->orderBy('id', 'desc')->with('consulting')->with('user')->paginate(10);
                    break;
                //대상
                case 'targetPerson':
                    $consultingApplies = ConsultingApply::where('state', 0)->orderBy('id', 'desc')->with('consulting')->with('user')
                        ->whereHas('user', function ($query) {
                            $query->where('consultingTarget', 1);
                        })->paginate(10);
                    break;
                //비대상
                case 'nonTargetPerson':
                    $consultingApplies = ConsultingApply::where('state', 0)->orderBy('id', 'desc')
                        ->with('consulting')
                        ->with('user')->whereHas('user', function ($query) {
                            $query->where('consultingTarget', 0);
                        })->paginate(10);
                    break;
            }
        }
        return view('admin.consulting.consulting1', ['consultingApplies' => $consultingApplies]);
    }
    public function consulting1 (Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        if(!$request['allow']){
            return redirect()->back()->withErrors(['error' => '승인할 내역을 선택해주세요.']);
        }
        else{
            foreach ($request['allow'] as $allow){
//                $update = consultingApply::where('id',$allow)->update(['state' => '1']);

                $ca = consultingApply::where('id',$allow)->first();
                if ($ca) {
                    $ca->state = 1;
                    $ca->save();

                    $proLog = ProLog::create([
                        'userId' => Auth::id(),
                        'targetId' => $ca->userId,
                        'userName' => 'PBL센터',
                        'authority' => -1,
                        'eventType' => 1010,
                        'eventState' => 10,
                        'title' => $ca->title,
                    ]);

                    broadcast(
                        new ProLogEvent($proLog->toArray(), $ca->userId)
                    );
                }

            }
            return redirect()->route('admin.consulting1View');
        }
    }
    public function consulting2View(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $consultingApplies = ConsultingApply::where('state','1')->with('consulting')->with('user')->paginate(10);
        return view('admin.consulting.consulting2',['consultingApplies' => $consultingApplies]);
    }
    public function consulting3View (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $week = date("w");
        $weeks = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];

        $validator = Validator::make($request->only('year', 'month'), [
            'year' => ['required', 'integer', 'min:1900', 'max:2500'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'day' => ['integer', 'min:1', 'max:31'],
        ]);

        if (!$validator->fails()) {
            $year = $request['year'];
            $month = $request['month'];
            if ($request->has('day')) {
                $day = $request['day'];
            }

            $week = date('w', strtotime($year.'-'.$month.'-'.$day));
        }

        $consultants = User::where('authority', 4)->get();

        return view('admin.consulting.consulting3', ['year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week], 'consultants' => $consultants]);
    }
    public function consulting3Reload (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
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

        $consultings = Consulting::whereBetween('startDateTime', [$year.'-'.$month.'-1', ($month >= 12 ? $year + 1 : $year).'-'.($month >= 12 ? 1 : $month + 1).'-1'])->with('consultant')->with('applies')->get();
        $days = $consultings->groupBy(function ($element) {
            return (int)date('d', strtotime($element->startDateTime));
        });

        return ['days' => $days, 'year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week]];
    }
    public function consulting3 (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'startDateTime' => ['required', 'date', 'before:endDateTime'],
            'endDateTime' => ['required', 'date'],
            'consultantId' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return false;
        }
//        else {
//            $start = strtotime($request['startDateTime']);
//            if ($start < strtotime(date('Y-m-d'))) {
//                return false;
//            }
//        }

        $consultant = User::find($request['consultantId']);
        if (!$consultant || $consultant->authority != 4) {
            return false;
        }

        $date = strtotime($request['date']);

        Consulting::create([
            'startDateTime' => date('Y-m-d H:i:s', strtotime($request['startDateTime'])),
            'endDateTime' => date('Y-m-d H:i:s', strtotime($request['endDateTime'])),
            'consultantId' => $request['consultantId'],
            'title' => $request['title'],
        ]);

        return true;
    }
    public function consulting3Modi (Request $request, $consultingId) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'startDateTime' => ['required', 'date', 'before:endDateTime'],
            'endDateTime' => ['required', 'date'],
//            'consultantId' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return false;
        }
//        else {
//            $start = strtotime($request['startDateTime']);
//            if ($start < strtotime(date('Y-m-d'))) {
//                return false;
//            }
//        }

//        $consultant = User::find($request['consultantId']);
//        if (!$consultant || $consultant->authority != 4) {
//            return false;
//        }

//        $date = strtotime($request['date']);

        $consulting = Consulting::find($consultingId);
        if (!$consulting) {
            return false;
        }

        $consulting->startDateTime = date('Y-m-d H:i:s', strtotime($request['startDateTime']));
        $consulting->endDateTime = date('Y-m-d H:i:s', strtotime($request['endDateTime']));
//        $consulting->consultantId = $request['consultantId'];
        $consulting->title = $request['title'];
        $consulting->save();

//        Consulting::create([
//            'startDateTime' => date('Y-m-d H:i:s', strtotime($request['startDateTime'])),
//            'endDateTime' => date('Y-m-d H:i:s', strtotime($request['endDateTime'])),
//            'consultantId' => $request['consultantId'],
//            'title' => $request['title'],
//        ]);

        return true;
    }

    //공모전관리
    public function competition1View (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $competitions = CompetitionApply::where('state', 1)->with('competition')->with('user')->orderBy('id', 'desc')->paginate(10);

        return view('admin.competition.competition1', ['competitions' => $competitions]);
    }
    public function competition1 (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'admitIds' => ['array', 'required'],
            'admitIds.*' => ['integer']
        ]);

        if ($validator->fails()) {
            if($validator->fails('admitIds')){
                return redirect()->route('admin.competition1View')->withErrors(['error' => '선택된 승인 내역이 없습니다.']);
            }
            return redirect()->route('admin.competition1View');
        }

        CompetitionApply::whereIn('id', $request['admitIds'])->update(['state' => 2]);

        return redirect()->route('admin.competition1View');
    }
    public function competition2View (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $competitions = CompetitionApply::where('state', 2)->with('competition')->with('user')->orderBy('id', 'desc')->paginate(10);

        return view('admin.competition.competition2', ['competitions' => $competitions]);
    }
    public function competition3View (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $week = date("w");
        $weeks = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];

        $validator = Validator::make($request->only('year', 'month'), [
            'year' => ['required', 'integer', 'min:1900', 'max:2500'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'day' => ['integer', 'min:1', 'max:31'],
        ]);

        if (!$validator->fails()) {
            $year = $request['year'];
            $month = $request['month'];
            if ($request->has('day')) {
                $day = $request['day'];
            }

            $week = date('w', strtotime($year.'-'.$month.'-'.$day));
        }

        return view('admin.competition.competition3', ['year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week]]);
    }
    public function competition3Reload (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
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

        $competitions = Competition::where('year', $year)->where('month', $month)->with('applies')->get();
        $days = $competitions->groupBy('day');

        return ['days' => $days, 'year' => $year, 'month' => $month, 'day' => $day, 'week' => $weeks[$week]];
    }
    public function competition3 (Request $request) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'content' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return false;
        }

        $date = strtotime($request['date']);

        Competition::create([
            'year' => date('Y', $date),
            'month' => date('m', $date),
            'day' => date('d', $date),
            'title' => $request['content']
        ]);

        return true;
    }
    public function competition3Modi (Request $request, $competitionId) {
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
//            'content' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return false;
        }

        $competition = Competition::find($competitionId);
        if (!$competition) {
            return false;
        }

        $date = strtotime($request['date']);

        $competition->year = date('Y', $date);
        $competition->month = date('m', $date);
        $competition->day = date('d', $date);
//        $competition->title = $request['content'];
        $competition->save();



//        Competition::create([
//            'year' => date('Y', $date),
//            'month' => date('m', $date),
//            'day' => date('d', $date),
//            'title' => $request['content']
//        ]);

        return true;
    }
    //공지관리
    public function noticeView(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $notices = Notice::orderBy('id','desc')->paginate(10);


        return view('admin.notice.notice',['notices' => $notices]);
    }
    public function noticeNewView(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        return view('admin.notice.notice_new');
    }
    public function noticeNew(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['image', 'nullable']
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return redirect()->back()->withErrors($errors)->withInput($request->input());
        }

        $noticeData = [];
        $noticeData['title'] = $request['title'];
        $noticeData['content'] = $request['content'];

        if ($request->hasFile('image')) {
            $imagePathName = uniqid();
            Storage::disk('local')->putFileAs('/notice', $request->file('image'), $imagePathName);

            $noticeData['imageName'] = $request->file('image')->getClientOriginalName();
            $noticeData['imagePathName'] = $imagePathName;
        }

        $notice = Notice::create($noticeData);

        return redirect()->route('admin.noticeView');
    }
    public function noticeDetailView(Request $request, $noticeId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $notice = Notice::find($noticeId);
        if (!$notice) {
            return redirect()->route('admin.noticeView');
        }
        return view('admin.notice.notice_detail', ['notice' => $notice]);
    }
    //수정 Logic
    public function noticeDetail(Request $request, $noticeId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $notice = Notice::find($noticeId);
        if (!$notice) {
            return redirect()->route('admin.noticeView');
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'image' => ['image', 'nullable']
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return redirect()->back()->withErrors($errors)->withInput($request->input());
        }

        $notice->title = $request['title'];
        $notice->content = $request['content'];

        if ($request->hasFile('image')) {
            $imagePathName = uniqid();

            Storage::disk('local')->delete('notice/'.$notice->imagePathName);
            Storage::disk('local')->putFileAs('/notice', $request->file('image'), $imagePathName);

            $notice->imageName = $request->file('image')->getClientOriginalName();
            $notice->imagePathName = $imagePathName;
        }

        $notice->save();

        return redirect()->route('admin.noticeView');
    }
    function noticeDelete(Request $request, $noticeId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $notice = Notice::where('id',$noticeId)->first();
        if(!$notice){
            return redirect()->route('admin.noticeView');
        }
        Storage::disk('local')->delete('notice/'.$notice->imagePathName);
        $notice->delete();

        return redirect()->route('admin.noticeView');
    }
    //1:1문의관리
    function reqView(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $reqs = req::orderBy('id','desc')->paginate(10);

        return view('admin.req.req',['reqs' => $reqs]);
    }
    function reqDetailView(Request $request, $reqId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $req = req::where('id',$reqId)->first();
        if(!$req){
            return redirect()->route('admin.req_detail');
        }

        return view('admin.req.req_detail',['req' => $req]);
    }
    function reqDetail(Request $request, $reqId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $req = req::find($reqId);
        if (!$req) {
            return redirect()->back();
        }

        $request->validate([
            'adminAnswer' => ['required', 'string']
        ]);

        $req->adminAnswer = $request['adminAnswer'];
        $req->save();

        return redirect()->route('admin.reqView')->withErrors(['error' => '저장되었습니다.']);
    }
    //FAQ관리
    public function faqView(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $faqs = faq::orderBy('id','desc')->paginate(10);

        return view('admin.faq.faq',['faqs' => $faqs]);
    }
    public function faqDelete(Request $request, $faqId){
        $faq = faq::find($faqId);
        if(!$faq){
            return redirect()->route('admin.faqView');
        }


        $faq->delete();
        return redirect()->route('admin.faqView');
    }
    public function faqNewView(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        return view('admin.faq.faq_new');
    }
    public function faqNew(Request $request){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $validator = Validator::make($request->all(),[
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'adminAnswer' => ['required', 'string']
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            return redirect()->back()->withErrors($errors)->withInput($request->input());
        }

//        return dd($faqData);
//        $faq = faq()->create($faqData);

        $faq = faq::create([
            'title' => $request['title'],
            'content' => $request['content'],
            'adminAnswer' => $request['adminAnswer'],
        ]);

        return redirect()->route('admin.faqView');
    }
    public function faqDetailView(Request $request, $faqId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $faq = faq::find($faqId);
        if(!$faq){
            return redirect()->route('admin.faqView');
        }

        return view('admin.faq.faq_detail', ['faq' => $faq]);
    }
    public function faqDetail(Request $request,$faqId){
        if(Auth::user()->authority != 3){
            Auth::logout();
            return redirect()->route('admin.loginView')->withErrors(['error' => '접근 권한이 없습니다.']);
        }
        $faq = faq::find($faqId);
        if(!$faq){
            return redirect()->route('admin.faqView');
        }
        $validator = Validator::make($request->all(),[
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'adminAnswer' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return redirect()->back()->withErrors($errors)->withInput($request->input());
        }
        $faq->title = $request['title'];
        $faq->content = $request['content'];
        $faq->adminAnswer = $request['adminAnswer'];
        $faq->save();
        return redirect()->route('admin.faqView');
    }

    public function adminExport (Request $request){
        $user = Auth::user();
        if ($user->authority != 3) {
            return redirect()->back();
        }

//        $cards = Card::where('userId', $user->id)->where('type', 0)->orderBy('id', 'desc')->with('classApplyItems')->get();
        $cards = Card::where('type', 0)->orderBy('id', 'desc')->with('classApplyItems')->get();

        $applies = [];

        //wait, complete, end ...
        $state = $request['state'];

//        return dd($state);

        $gwamokNm = $request['gwamokNm'];
        $gnjSosokNm = $request['gnjSosokNm'];
        $gnjHakgwaNm = $request['gnjHakgwaNm'];
        $year = $request['year'];
        $term = $request['term'];
        $suupNo = $request['suupNo'];
        $daepyoGangsaNm = $request['daepyoGangsaNm'];
        $meca = $request['meca'];
//        return dd($request['daepyoGangsaNm']);

        $classApplies = null;
        switch ($request['state']) {
            case 'wait':
                $classApplies = $this->waitData($request, false);
                break;
            case 'complete':
                $classApplies = $this->completeData($request);
                break;
            case 'statistics':
                $classApplies = $this->statisticsData($request);
                break;
            case 'ing':
                $classApplies = $this->ingData($request);
                break;
            case 'professor':
                $classApplies = $this->ingData($request);
                break;
            case 'end':
                $classApplies = collect([]);
                break;
            case 'certify':
                $classApplies = collect([]);
                break;
            default:
                $classApplies = collect([]);
                break;
        }
//        return dd($classApplies);
        $newArr = [];
        if($state == 'wait'){
            $newArr[] =  [
                '번호',
                '수업번호',
                '수업명',
                '학기',
                '교수명',
                '상태'
            ];
        }
        elseif($state == 'complete'){
            $newArr[] =  [
                '번호',
                '수업번호',
                '수업명',
                '유형',
                '단대',
                '학과',
                '학기',
                '교수명',
//                '인원',
                '상태'
            ];
        }elseif($state == 'ing'){
            $newArr[] =  [
                '번호' => '번호',
                '수업번호' => '수업번호',
                '수업명' =>'수업명',
                '단대' => '단대',
                '학과' => '학과',
                '교수명' => '교수명',
                '학기' => '학기',
                '유형' => '유형',
                '문제분석' => '문제분석',
                '팀활동' => '팀활동',
                '평가' => '평가',
                '성찰' => '성찰',
                '협렵' => '협력',
                '성취' => '성취',
                '피드백' => '피드백',
                '상태' => '상태',
            ];
        }elseif($state == 'professor'){
            $newArr[] =  [
                '번호' => '번호',
                '수업번호' => '수업번호',
                '수업명' =>'수업명',
                '단대' => '단대',
                '학과' => '학과',
                '학기' => '학기',
                '유형' => '유형',
                '교수명' => '교수명',
                '기초교육' => '기초교육',
                '컨설팅' => '컨설팅',                
                '요약보고서' => '요약보고서',                
                '포트폴리오' => '포트폴리오',
                '상태' => '상태',
            ];
        }elseif($state == 'end'){
            $newArr[] =  [
                '번호' => '번호',
                '수업번호' => '수업번호',
                '수업명' =>'수업명',
                '교수명' => '교수명',
                '단대' => '단대',
                '학과' => '학과',
                '학기' => '학기',
                '인원' => '인원',
                '상태' => '상태',
            ];
        }elseif($state == 'statistics'){
            $newArr[] =  [
                '번호' => '번호',
                '수업번호' => '수업번호',
                '수업명' =>'수업명',
                '단대' => '단대',
                '학과' => '학과',
                '교수명' => '교수명',
                '유형' => '유형',
                '학생수' => '학생수',
                '학기' => '학기',
//                '상태' => '상태',
                '팀수' => '팀수',
                '협력' => '협력',
                '성취' => '성취',
                '피드백' => '피드백',
                '총점' => '총점',
            ];
        }elseif($state == 'certify'){
            $newArr[] =  [
                '번호' => '번호',
                '수업번호' => '수업번호',
                '수업명' =>'수업명',
                '교수명' => '교수명',
                '단대' => '단대',
                '학과' => '학과',
                '학기' => '학기',
                '인원' => '인원',
                '상태' => '상태',
            ];
        }else{
            $newArr[] =  [
                '번호' => '번호',
                '수업번호' => '수업번호',
                '수업명' =>'수업명',
                '교수명' => '교수명',
                '단대' => '단대',
                '학과' => '학과',
                '학기' => '학기',
                '인원' => '인원',
                '상태' => '상태',
            ];
        }

        $newArr = collect($newArr);
        ////////////////////////////////////////////////////////////////////////

        $newArr = $newArr->merge($classApplies->map(function ($element, $key) use($state) {
            switch ($state) {
                case 'wait':
                    return  [
                        '번호' => $element->id,
                        '수업번호' => $element->code ?? '',
                        '수업명' => $element->korName,
//                        '단대' => ' ',
//                        '학과' => ' ',
                        '학기' => 0,
                        '교수명' => $element->itemUser->user->name ?? '',
//                        '인원' => 0,
                        '상태' => '대기중',
                    ];
                case 'complete':
//                    return dd ($element);
                    if($element->suupTerm){
                        if($element->suupTerm == '10'){
                            $element->suupTermNm = '1학기';
                        }elseif($element->suupTerm == '20') {
                            $element->suupTermNm = '2학기';
                        }elseif($element->suupTerm == '15') {
                            $element->suupTermNm = '여름학기';
                        }elseif($element->suupTerm == '25') {
                            $element->suupTermNm = '겨울학기';
                        }
                    }
                    return [
                        '번호' => $element->id,
                        '수업번호' => $element->code ? $element->code : '',
                        '수업명' =>$element->korName,
                        '유형' => $element->meca == 1 ? 'M' : ($element->meca == 2 ? 'E' : ($element->meca == 3 ? 'C' : 'A')),
                        '단대' => $element->itemUser->user->name ? $element->itemUser->user->sosokNm : '',
                        '학과' => $element->department,
                        '학기' => $element->suupYear ? $element->suupYear.'년'.$element->suupTermNm : '',
                        '교수명' => $element->itemUser->user->name ?? '',
//                        '인원' => $element->userCount, // X
                        '상태' => '승인완료',
                    ];
                case 'statistics':
                    return [
                        '번호' => $element->id,
                        '수업번호' => $element->suupNo,
                        '수업명' =>$element->korName,
                        '교수명' => $element->itemUser->user->name,
                        '유형' => $element->meca == 1 ? 'M' : ($element->meca == 2 ? 'E' : ($element->meca == 3 ? 'C' : 'A')),
                        '단대' => $element->gnjSosokNm,
                        '학과' => $element->gnjHakgwaNm,
                        '학생수' => $element->members > 1 ?  $element->members.'명' : '',
                        '학기' => $element->suupYear.'년'.$element->suupTermNm,
//                        '상태' => $element->state == 'wait' ? '대기' : '승인완료',
                        '팀수' => $element->teamCount,
                        '협력' => $element->scores['co'],
                        '성취' => $element->scores['ac'],
                        '피드백' => $element->scores['fe'],
                        '총점' => $element->scores['co'] + $element->scores['ac'] + $element->scores['fe'],
                    ];
                case 'ing':
                    $problems = 0;
                    $reflections = 0;
                    $teams = 0;
                    foreach($element->prt as $prt){
                        foreach ($prt->items2 as $item){
                            switch ($item->type){
                                case (1):
                                    $problems++;
                                    break;
                                case (2):
                                    $reflections++;
                                    break;
                                case (5):
                                    $teams++;
                                    break;
                            }
                        }
                    }
                    return [
                        '번호' => $element->id,
                        '수업번호' => $element->suupNo,
                        '수업명' => $element->korName,
                        '단대' => $element->gnjSosokNm,
                        '학과' => $element->gnjHakgwaNm,
                        '교수명' => $element->itemUser->user->name,
                        '학기' => $element->suupYear.'년'.$element->suupTermNm,
                        '유형' => $element->meca,
                        '문제분석' => $problems,
                        '팀활동2' => $teams,
                        '평가' => '',
                        '성찰' => $reflections,
                        '협력' => $element->scores['co'],
                        '성취' => $element->scores['ac'],
                        '피드백' => $element->scores['fe'],
                        '상태' => '진행중',
                    ];
                case 'professor':
                    return [
                        '번호' => $element->id,
                        '수업번호' => $element->suupNo,
                        '수업명' => $element->korName,
                        '단대' => $element->gnjSosokNm,
                        '학과' => $element->gnjHakgwaNm,
                        '학기' => $element->suupYear.'년'.$element->suupTermNm,
                        '유형' => $element->meca,
                        '교수명' => $element->itemUser->user->name,
                        '기초교육' => '',
                        '컨설팅' => '',
                        '요약보고서' => '',
                        '포트폴리오' => '',
                        '상태' => '교수학습현황',
                    ];
                case 'end':
                    return [];
                case 'certify':
                    return [];
                default:
                    return [];
            }
        }));
        return Excel::download(new InvoicesExport($newArr), '수업관리.xlsx');
    }





}
