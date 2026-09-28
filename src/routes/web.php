<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\RestController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MyClassController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\HyApi;

use App\Models\ClassObject;
use App\Models\ClassApply;
use App\Models\ClassList;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('my_page_view');
//});

//Route::any('files/{pathToFile}', function($pathToFile) {
//    return false;
//});
//
//Route::any('readfile/{fileName}', function() {
//    return false;
//});
//
//Route::any('/public/uploads/', function() {
//    return redirect()->back();
//});
//
//Route::any('/public/storage/', function() {
//    return redirect()->back();
//});
//
//Route::any('/uploads/', function () { return redirect()->back(); });
//Route::any('/uploads/{any}/{any2}', function () { return redirect()->back(); });
//
//Route::any('/storage/uploads/{path?}', function () {
//    return 'access denied';
//})->where(['path' => '.*']);

Route::get('/tttt', [App\Http\Controllers\TestController::class, 'tttt']);
Route::post('/tttt', [App\Http\Controllers\TestController::class, 'tttt2'])->name('tttt2');

Route::get('/testtesttest', function () {
    return view('test_test_test_test_test');
});

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/adminLogin', [App\Http\Controllers\AdminController::class, 'adminLoginView'])->name('admin.loginView');
    Route::post('/adminLogin', [App\Http\Controllers\AdminController::class, 'adminLogin'])->name('admin.login');
});

//한양대 테스트
Route::get('/hy/login', [App\Http\Controllers\HyApi::class, 'login'])->name('auth.hy.login');
Route::get('/hy/callback', [App\Http\Controllers\HyApi::class, 'callback'])->name('auth.hy.callback');

//공지사항 이미지
Route::get('/notice/image/{imagePathName}', [ServiceController::class, 'noticeImageDownload'])->name('notice.image');

Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'adminDashboardView'])->name('admin.dashboardView');
    Route::get('/admin/dashboard/noti', [App\Http\Controllers\AdminController::class, 'noti'])->name('admin.noti');

    //한양대 api
    Route::get('/admin/apiButton', [App\Http\Controllers\AdminController::class, 'apiButton'])->name('admin.apiButton');
    Route::get('/admin/pwd', [App\Http\Controllers\AdminController::class, 'pwdCheckView'])->name('admin.pwdCheckView');
    Route::post('/admin/pwd', [App\Http\Controllers\AdminController::class, 'pwdCheck'])->name('admin.pwdCheck');
    Route::post('/admin/pwdEdit', [App\Http\Controllers\AdminController::class, 'pwdEdit'])->name('admin.pwdEdit');
//    Route::get('/admin/pwdEdit', [App\Http\Controllers\AdminController::class, 'pwdEditView'])->name('admin.pwdEditView');

    Route::post('/hy/dataAPI', [App\Http\Controllers\HyApi::class, 'dataAPI'])->name('auth.hy.dataAPI');

    //수업관리
    Route::get('/admin/class/adminExport', [App\Http\Controllers\AdminController::class, 'adminExport'])->name('admin.adminExport');

    //기초교육, 컨설팅, 프로그램 Export
    Route::get('/admin/class/applicantExport', [App\Http\Controllers\AdminController::class, 'applicantExport'])->name('admin.applicantExport');


    Route::get('/admin/class1', [App\Http\Controllers\AdminController::class, 'class1View'])->name('admin.class1View');
    Route::post('/admin/class1', [App\Http\Controllers\AdminController::class, 'class1'])->name('admin.class1');
    Route::get('/admin/class1/Delete/{classApplyId}', [App\Http\Controllers\AdminController::class, 'class1Delete'])->name('admin.class1Delete');

    Route::get('/admin/class2', [App\Http\Controllers\AdminController::class, 'class2View'])->name('admin.class2View');
    Route::post('/admin/class2', [App\Http\Controllers\AdminController::class, 'class2'])->name('admin.class2');


    Route::get('/admin/class3/class', [App\Http\Controllers\AdminController::class, 'class3View'])->name('admin.class3View');
    Route::get('/admin/class3/professor', [App\Http\Controllers\AdminController::class, 'class3ProfessorView'])->name('admin.class3ProfessorView');

    Route::get('/admin/class4', [App\Http\Controllers\AdminController::class, 'class4View'])->name('admin.class4View');

    Route::get('/admin/statistics', [App\Http\Controllers\AdminController::class, 'classStatisticsView'])->name('admin.classStatisticsView');
    Route::get('/admin/certificate', [App\Http\Controllers\AdminController::class, 'classCertificateView'])->name('admin.classCertificateView');

    Route::get('/admin/class/detail1/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail1View'])->name('admin.classDetail1View');
    Route::get('/admin/class/detail2/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail2View'])->name('admin.classDetail2View');

    Route::get('/admin/class/detail3/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail3View'])->name('admin.classDetail3View');
    Route::post('/admin/class/detail3/names',[App\Http\Controllers\AdminController::class, 'classDetail3NamesChange'])->name('admin.classDetail3View.teamNamesChange');

    Route::get('/admin/class/detail4/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail4View'])->name('admin.classDetail4View');
    Route::get('/admin/class/detail4/{classApplyId}/{problemAnalysisId}', [App\Http\Controllers\AdminController::class, 'classDetail4DetailView'])
        ->name('admin.classDetail4DetailView');

    Route::get('/admin/class/detail5/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail5View'])->name('admin.classDetail5View');
    Route::get('/admin/class/detail5/{classApplyId}/{teamActivityId}', [App\Http\Controllers\AdminController::class, 'classDetail5DetailView'])
        ->name('admin.classDetail5DetailView');

    Route::get('/admin/class/detail6/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail6View'])->name('admin.classDetail6View');
    Route::get('/admin/class/detail7/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail7View'])->name('admin.classDetail7View');
    Route::get('/admin/class/detail7/detail/{classApplyId}/{reflectionId}', [App\Http\Controllers\AdminController::class, 'classDetail7DetailView'])->name('admin.classDetail7DetailView');

    Route::get('/admin/class/detail8/{classApplyId}', [App\Http\Controllers\AdminController::class, 'classDetail8View'])->name('admin.classDetail8View');


//      /names',
//      /members
    //참여자 관리
    Route::get('/admin/member/{par?,par2?}', [App\Http\Controllers\AdminController::class, 'memberView'])->name('admin.memberView');
//    Route::get('/admin/member', [App\Http\Controllers\AdminController::class, 'memberView2'])->name('admin.memberView2');

    Route::get('/admin/member/detail/pro/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailProView'])->name('admin.memberDetailProView');
    Route::post('/admin/member/detail/pro/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailPro'])->name('admin.memberDetailPro');

    Route::get('/admin/member/detail/stu/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailStuView'])->name('admin.memberDetailStuView');
    Route::post('/admin/member/detail/stu/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailStu'])->name('admin.memberDetailStu');

    Route::get('/admin/member/detail/out/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailOutView'])->name('admin.memberDetailOutView');
    Route::post('/admin/member/detail/out/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailOut'])->name('admin.memberDetailOut');

    Route::get('/admin/member/detail/etc/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailEtcView'])->name('admin.memberDetailEtcView');
//    Route::post('/admin/member/detail/etc/{userId}', [App\Http\Controllers\AdminController::class, 'memberDetailEtc'])->name('admin.memberDetailEtc');


    //기초교육관리
    Route::get('/admin/basic1', [App\Http\Controllers\AdminController::class, 'basic1View'])->name('admin.basic1View');
    Route::post('/admin/basic1', [App\Http\Controllers\AdminController::class, 'basic1'])->name('admin.basic1');
    Route::get('/admin/basic2', [App\Http\Controllers\AdminController::class, 'basic2View'])->name('admin.basic2View');
    Route::get('/admin/basic3', [App\Http\Controllers\AdminController::class, 'basic3View'])->name('admin.basic3View');
    Route::post('/admin/basic3', [App\Http\Controllers\AdminController::class, 'basic3'])->name('admin.basic3');
    Route::post('/admin/basic3/modi/{basicId}', [App\Http\Controllers\AdminController::class, 'basic3Modi']);
    Route::post('/admin/basic3/reload', [App\Http\Controllers\AdminController::class, 'basic3Reload'])->name('admin.basic3Reload');

    //컨설팅관리
    Route::get('/admin/consulting1', [App\Http\Controllers\AdminController::class, 'consulting1View'])->name('admin.consulting1View');
    Route::post('/admin/consulting1', [App\Http\Controllers\AdminController::class, 'consulting1'])->name('admin.consulting1');
    Route::get('/admin/consulting2', [App\Http\Controllers\AdminController::class, 'consulting2View'])->name('admin.consulting2View');

    Route::get('/admin/consulting3', [App\Http\Controllers\AdminController::class, 'consulting3View'])->name('admin.consulting3View');
    Route::post('/admin/consulting3', [App\Http\Controllers\AdminController::class, 'consulting3'])->name('admin.consulting3');
    Route::post('/admin/consulting3/modi/{consultingId}', [App\Http\Controllers\AdminController::class, 'consulting3Modi']);
    Route::post('/admin/consulting3/reload', [App\Http\Controllers\AdminController::class, 'consulting3Reload'])->name('admin.consulting3Reload');

    //공모전관리
    Route::get('/admin/competition1', [App\Http\Controllers\AdminController::class, 'competition1View'])->name('admin.competition1View');
    Route::post('/admin/competition1', [App\Http\Controllers\AdminController::class, 'competition1'])->name('admin.competition1');
    Route::get('/admin/competition2', [App\Http\Controllers\AdminController::class, 'competition2View'])->name('admin.competition2View');
    Route::get('/admin/competition3', [App\Http\Controllers\AdminController::class, 'competition3View'])->name('admin.competition3View');
    Route::post('/admin/competition3', [App\Http\Controllers\AdminController::class, 'competition3'])->name('admin.competition3');
    Route::post('/admin/competition3/modi/{competitionId}', [App\Http\Controllers\AdminController::class, 'competition3Modi']);
    Route::post('/admin/competition3/reload', [App\Http\Controllers\AdminController::class, 'competition3Reload'])->name('admin.competition3Reload');

    //공지사항
    Route::get('/admin/notice', [App\Http\Controllers\AdminController::class, 'noticeView'])->name('admin.noticeView');

    Route::get('/admin/notice/new', [App\Http\Controllers\AdminController::class, 'noticeNewView'])->name('admin.noticeNewView');
    Route::post('/admin/notice/new', [App\Http\Controllers\AdminController::class, 'noticeNew'])->name('admin.noticeNew');

    Route::get('/admin/notice/detail/{noticeId}', [App\Http\Controllers\AdminController::class, 'noticeDetailView'])->name('admin.noticeDetailView');
    Route::post('/admin/notice/detail/{noticeId}', [App\Http\Controllers\AdminController::class, 'noticeDetail'])->name('admin.noticeDetail');

    Route::get('/admin/notice/Delete/{noticeId}', [App\Http\Controllers\AdminController::class, 'noticeDelete'])->name('admin.noticeDelete');

    //FAQ
    Route::get('/admin/faq', [App\Http\Controllers\AdminController::class, 'faqView'])->name('admin.faqView');

    Route::get('/admin/faq/new', [App\Http\Controllers\AdminController::class, 'faqNewView'])->name('admin.faqNewView');
    Route::post('/admin/faq/new', [App\Http\Controllers\AdminController::class, 'faqNew'])->name('admin.faqNew');

    Route::get('/admin/faq/detail/{faqId}', [App\Http\Controllers\AdminController::class, 'faqDetailView'])->name('admin.faqDetailView');
    Route::post('/admin/faq/detail/{faqId}', [App\Http\Controllers\AdminController::class, 'faqDetail'])->name('admin.faqDetail');

    Route::get('/admin/faq/delete/{faqId}', [App\Http\Controllers\AdminController::class, 'faqDelete'])->name('admin.faqDelete');

    //1:1
    Route::get('/admin/req', [App\Http\Controllers\AdminController::class, 'reqView'])->name('admin.reqView');

    Route::get('/admin/req/Detail/{reqId}', [App\Http\Controllers\AdminController::class, 'reqDetailView'])->name('admin.reqDetailView');
    Route::post('/admin/req/Detail/{reqId}', [App\Http\Controllers\AdminController::class, 'reqDetail'])->name('admin.reqDetail');
});

//service 탭
Route::get('/notice', [ServiceController::class, 'noticeView'])->name('noticeView');
Route::get('/notice/first', [ServiceController::class, 'firstNoticeView'])->name('firstNoticeView');
Route::get('/notice/detail', [ServiceController::class, 'noticeDetailView'])->name('noticeDetailView');

Route::get('/faq', [ServiceController::class, 'faqView'])->name('faqView');
Route::get('/faq/detail', [ServiceController::class, 'faqDetailView'])->name('faqDetailView');

//권한 필요하지만 자체적으로 체크하게 함
Route::get('/ask', [ServiceController::class, 'reqView'])->name('reqView');
Route::get('/ask/write', [ServiceController::class, 'reqWriteView'])->name('reqWriteView');
Route::post('/ask/write', [ServiceController::class, 'reqWrite'])->name('reqWrite');
Route::get('/ask/{reqId}', [ServiceController::class, 'reqDetailView'])->name('reqDetailView');
Route::get('/ask/edit/{reqId}', [ServiceController::class, 'reqEditView'])->name('reqEditView');
Route::post('/ask/edit/{reqId}', [ServiceController::class, 'reqEdit'])->name('reqEdit');
Route::get('/ask/delete/{reqId}', [ServiceController::class, 'reqDelete'])->name('reqDelete');

Route::middleware('cauth')->group(function () {
    //임시로 홈 마이페이지 연결
//    Route::get('/', function (Request $request) {
//        $user = Auth::user();
//        if($user->authority == 1) {
//            return (new LectureController)->myStuList($request);
//        }
//        else if ($user->authority == 2) {
//            return (new LectureController)->myPageView($request);
//        }
//        else if ($user->authority == 4) {
//            return (new LectureController)->consultantMyPageView($request);
//        }
//    })->name('dashboardView');
//    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::get('/dash', [App\Http\Controllers\MyPageController::class, 'dash'])->name('dash');

    Route::get('/myPage/myStuCom', [App\Http\Controllers\MyPageController::class, 'myStuCom'])->name('myStuCom');
    Route::post('/myPage/myStuCom', [App\Http\Controllers\MyPageController::class, 'myStuComApply'])->name('myStuComApply');
    Route::post('/myPage/myStuCom/reload', [App\Http\Controllers\MyPageController::class, 'myStuComReload'])->name('myStuComReload');
    //user profile
    Route::get('/profile', [UserController::class, 'profileView'])->name('profileView');
    Route::get('/profile/edit', [UserController::class, 'profileEditView'])->name('profileEditView');
    Route::get('/avatar/{userId}', [UserController::class, 'avatar'])->name('avatar');
    Route::post('/profile/edit', [UserController::class, 'profileEdit'])->name('profileEdit');
    //search
    Route::get('/search', [SearchController::class, 'searchView'])->name('searchView');
    Route::get('/myPage', function (Request $request) {
        $user = Auth::user();
        if($user->authority == 1) {
            return (new MyPageController)->myStuList($request);
        }
        else if ($user->authority == 2) {
            return (new MyPageController)->myPageView($request);
        }
        else if ($user->authority == 4) {
            return (new MyPageController)->consultantMyPageView($request);
        }
        else if ($user->authority == 5) {
            return redirect()->route('dash');
        }
        else if ($user->authority == 6) {
            return redirect()->route('dash');
        }
    })->name('myPageView');

    Route::get('/myPage/proList/{year}/{semester}', [App\Http\Controllers\MyPageController::class, 'proList'])->name('proList');

    Route::get('/myPage/classDetail/{classListId}', [App\Http\Controllers\MyPageController::class, 'myStuListDetail'])->name('myStuListDetail');

    Route::get('/myPage/basic/list/{year}/{month}', [App\Http\Controllers\MyPageController::class, 'basicLists']);
    Route::post('/myPage/basic/apply/{basicId}', [App\Http\Controllers\MyPageController::class, 'basicApply']);
    Route::get('/myPage/consulting/list/{year}/{month}', [App\Http\Controllers\MyPageController::class, 'consultingLists']);
    Route::post('/myPage/consulting/apply/{consultingId}/{classObjectId}', [App\Http\Controllers\MyPageController::class, 'consultingApply']);
    Route::post('/myPage/consulting/apply/modi/{consultingApplyId}/{classObjectId}', [App\Http\Controllers\MyPageController::class, 'consultingApplyModi']);

    Route::get('/myPage/oldApply/{mode}', [App\Http\Controllers\MyPageController::class, 'oldApply'])->name('oldApply');
    Route::get('/myPage/fetchApply/{applyId}', [App\Http\Controllers\MyPageController::class, 'fetchApply'])->name('fetchApply');

    //consultant
    Route::get('/myPage/consulting', [App\Http\Controllers\MyPageController::class, 'consultantMyPageConsultingView'])->name('consultantMyPageConsultingView');
    Route::get('/myPage/consulting/detail/{applyId}', [App\Http\Controllers\MyPageController::class, 'consultantMyPageConsultingDetailView'])->name('consultantMyPageConsultingDetailView');

    Route::get('/myClass', [App\Http\Controllers\MyClassController::class, 'myClassView'])->name('myClassView');
    Route::get('/myClass/classList/{year}/{semester}', [App\Http\Controllers\MyClassController::class, 'classList'])->name('classList');

    //restful api access in vue component
    Route::post('/createCard', [App\Http\Controllers\CardController::class, 'createCard'])->name('createCard');
    Route::post('/removeCard', [App\Http\Controllers\CardController::class, 'removeCard'])->name('removeCard');
    Route::post('/updateCard', [App\Http\Controllers\CardController::class, 'updateCard'])->name('updateCard');
    Route::get('/fetchCard', [App\Http\Controllers\CardController::class, 'fetchCard'])->name('fetchCard');

    Route::post('/createItem', [App\Http\Controllers\ItemController::class, 'createItem'])->name('createItem');
    Route::post('/removeItem', [App\Http\Controllers\ItemController::class, 'removeItem'])->name('removeItem');
    Route::post('/updateItem', [App\Http\Controllers\ItemController::class, 'updateItem'])->name('updateItem');
    Route::get('/fetchItem/{cardId}', [App\Http\Controllers\ItemController::class, 'fetchItem'])->name('fetchItem');

    ///////test start
    Route::post('/test', [App\Http\Controllers\TestController::class, 'test']);
    Route::get('/test', function () {
        return view('test.test');
    });
    Route::get('/testMyPage', [App\Http\Controllers\TestController::class, 'myPageView']);

    Route::get('/testImage', [App\Http\Controllers\TestController::class, 'testImageView'])->name('imageUploadView');
    Route::post('/imageUpload', [App\Http\Controllers\TestController::class, 'imageUpload'])->name('imageUpload');
    Route::get('/download/{myPageId}/{fileName}/{fileRealName}', [App\Http\Controllers\TestController::class, 'imageDownload'])->name('downloadImage');
    ///////test end
    ///
    //apis
    Route::get('/fetch/class/members/{classObjectId}', [App\Http\Controllers\MyClassController::class, 'fetchClassMembers']);
    Route::get('/fetch/team/members/{teamId}', [App\Http\Controllers\MyClassController::class, 'fetchTeamMembers']);


    Route::get('/fetch/experts', [App\Http\Controllers\MyClassController::class, 'experts']);
    Route::get('/fetch/managers', [App\Http\Controllers\MyClassController::class, 'managers']);

    Route::get('/fetch/calendar/{classObjectId}/{teamId}/{year}/{month}', [App\Http\Controllers\MyClassController::class, 'fetchCalendar']);
    Route::post('/post/calendar', [App\Http\Controllers\MyClassController::class, 'postCalendar']);

    //delete캘린더
    Route::post('/update/calendar', [App\Http\Controllers\MyClassController::class, 'updateCalendar']);
    Route::post('/delete/calendar/{calendarId}', [App\Http\Controllers\MyClassController::class, 'deleteCalendar']);

    Route::post('/fetch', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'types' => ['required', 'string', 'in:card,item,user,chat,myClass,comment,oneComment'],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('id')) {
                return ['fail' => 'validate error: id'];
            }
            else if ($errors->has('types')) {
                return ['fail' => 'validate error: types'];
            }
        }

        switch($request['types']) {
            case 'item':
                return (new ItemController)->fetch($request);
            case 'card':
                return (new CardController)->fetch($request);
            case 'user':
                return (new UserController)->fetch($request);
            case 'chat':
                return (new ChatController)->fetch($request);
            case 'myClass':
                return (new MyClassController)->fetch($request);
            case 'comment':
                return (new CommentController)->fetch($request);
            case 'oneComment':
                return (new CommentController)->fetchOne($request);
        }
    });
    Route::post('/classSetting', [App\Http\Controllers\MyClassController::class, 'classSetting']);
    Route::post('/create', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'types' => ['required', 'string', 'in:card,item,talk'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('id')) {
                return ['fail' => 'validate error: id'];
            }
            else if ($errors->has('types')) {
                return ['fail' => 'validate error: types'];
            }
        }

        switch($request['types']) {
            case 'card':
                return (new CardController)->create($request);
            case 'item':
                return (new ItemController)->create($request);
            default:
                return (new CardController)->createChatCard($request);
        }
    });
    Route::post('/delete', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'types' => ['required', 'string', 'in:card,item,comment'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('id')) {
                return ['fail' => 'validate error: id'];
            }
            else if ($errors->has('types')) {
                return ['fail' => 'validate error: types'];
            }
        }

        switch($request['types']) {
            case 'card':
                return (new CardController)->delete($request);
            case 'item':
                return (new ItemController)->delete($request);
            case 'comment':
                return (new CommentController)->delete($request);
        }
    });
    Route::post('/update', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'types' => ['required', 'string', 'in:card,item,comment'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return ['fail' => true, 'sub' => false, $errors];
        }

        switch($request['types']) {
            case 'card':
                return (new CardController)->update($request);
            case 'item':
                $validator = Validator::make($request->all(), [
                    'subTypes' => ['required', 'string', 'in:move,edit,comment'],
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return ['fail' => true, 'sub' => false, $errors];
                }

                switch ($request['subTypes']) {
                    case 'move':
                        return (new ItemController)->move($request);
                    case 'edit':
                        return (new ItemController)->update($request);
                    case 'comment':
                        return (new ItemController)->comment($request);
                }
            case 'comment':
                return (new CommentController)->update($request);
            default:
                break;
        }
    });
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'chat']);
    Route::get('/download/{commentId}', [App\Http\Controllers\DownloadController::class, 'download']);
    Route::get('/images/{pathName}', [App\Http\Controllers\DownloadController::class, 'downloadImage']);
    Route::get('/files/{pathName}', [App\Http\Controllers\DownloadController::class, 'downloadFiles']);
    Route::get('/images/{pathName}/consultant/{applyId}', [App\Http\Controllers\DownloadController::class, 'downloadImage2']);
    Route::get('/images/{pathName}/file', [App\Http\Controllers\DownloadController::class, 'showImage']);
    Route::get('/images/{pathName}/file/consultant/{applyId}', [App\Http\Controllers\DownloadController::class, 'showImage2']);
//    Route::get('/apply/{applyId}/{pathName}', [App\Http\Controllers\DownloadController::class, 'downloadApplyFile'])->name('downloadApplyFile');
    Route::post('/brain/update', [App\Http\Controllers\ItemController::class, 'brainUpdate']);
    Route::post('/brain/move', [App\Http\Controllers\ItemController::class, 'brainMove']);

    //임시 대시보드(미들웨어 학생꺼 교수꺼 다시 해야함)
    Route::get('/dashboardPro',[App\Http\Controllers\LectureController::class, 'dashboardProView'])->name('dashboardProView');
    Route::get('/dashboardPro/proLog',[App\Http\Controllers\MyPageController::class, 'proLogFetch'])->name('proLogFetch');
    Route::get('/dashboardPro/actLog',[App\Http\Controllers\MyPageController::class, 'actLogFetch'])->name('actLogFetch');
    Route::get('/dashboardPro/proGraphData/{classObjectId}',[App\Http\Controllers\LectureController::class, 'proGraphData'])->name('proGraphData');

    Route::get('/dashboardStu',[App\Http\Controllers\LectureController::class, 'dashboardStuView'])->name('dashboardStuView');
    Route::get('/dashboardStu/stuGraphData/{classObjectId}',[App\Http\Controllers\LectureController::class, 'stuGraphData'])->name('stuGraphData');

    Route::get('/dashboardCon',[App\Http\Controllers\LectureController::class, 'dashboardConView'])->name('dashboardConView');
    Route::get('/dashboardOut',[App\Http\Controllers\LectureController::class, 'dashboardOutView'])->name('dashboardOutView');
    Route::get('/dashboardOut/outGraphData/{classObjectId}',[App\Http\Controllers\LectureController::class, 'proGraphData'])->name('outGraphData');
});

Route::middleware('lecture.manage')->group(function () {
    //test
    Route::get('/testClassAdd',[App\Http\Controllers\LectureController::class, 'testClassAdd'])->name('testClassAdd');
    Route::post('/testClassAdd',[App\Http\Controllers\LectureController::class, 'testClassAdd2'])->name('testClassAdd2');


    //Lecture
    Route::get('/lectureList',[App\Http\Controllers\LectureController::class, 'lectureList'])->name('lectureList');
    Route::get('/lectureList/lectureDetailInfo/{classApplyId}',[App\Http\Controllers\LectureController::class, 'lectureDetailInfo'])->name('lectureDetailInfo');
    Route::post('/lectureList/lectureDetailInfo/{classApplyId}',[App\Http\Controllers\LectureController::class, 'lectureInfoCodeUpdate'])->name('lectureInfoCodeUpdate');
//    Route::post('/lectureList/lectureDetailInfo/{classApplyId}',[App\Http\Controllers\LectureController::class, 'lectureDetailBeforeApprovalCodeUpdate'])->name('lectureDetailBeforeApprovalCodeUpdate');


    Route::get('/lectureList/Export',[App\Http\Controllers\LectureController::class, 'lectureExport'])->name('lectureExport');

//참여자
    Route::get('/lectureList/lectureDetailMember/{classObjectId}',[App\Http\Controllers\LectureController::class, 'lectureDetailMember'])->name('lectureDetailMember');
    Route::get('/lectureList/lectureDetailMember/{classObjectId}/add', function (Request $request, $classObjectId) {
        $classObject = ClassObject::find($classObjectId);

        if (!$classObject) {
            return redirect()->back();
        }
        $myClass = $classObject->myClass()->first();
        if (!$myClass) {
            return redirect()->back();
        }
        if ($myClass->userId != Auth::id()) {
            return redirect()->back();
        }

        $users = User::where('authority', 1)->get();
        $classLists = ClassList::where('classObjectId', $classObject->id)->get();
        $_classLists = [];

        foreach ($users as $user) {
            if (is_null($user->hakbun)) {
                $_classLists[] = new ClassList([
                    'userId' => $user->id,
                    'name' => $user->name,
                ]);

                continue;
            }
            $member = $classLists->where('gaeinNo', $user->hakbun)->first();
            if (!$member) {
                $_classLists[] = new ClassList([
                    'userId' => $user->id,
                    'name' => $user->name,
                ]);
            }
            else {
                $member->userId = $user->id;
                $member->save();
            }
        }

        if (count($_classLists) > 0) {
            $myClass->classLists3()->saveMany($_classLists);
        }

        return redirect()->back();

//        return dd('button');
//        return dd(User::where('authority', 1)->get());

        if ($classObject) {
            foreach (User::where('authority', 1)->get() as $user) {
                $classList = ClassList::where('classObjectId', $classObject->id)->where('userId', $user->id)->first();
                if (!$classList) {
                    ClassList::create([
                        'userId' => $user->id,
                        'classObjectId' => $classObject->id,
                        'classObjectName' => $classObject->name,
                    ]);
                }
            }
        }
        return redirect()->back();
    })->name('testMemberAdd');

    /////////
    Route::get('/lectureList/lectureDetailMember/{classObjectId}/etcView',[App\Http\Controllers\LectureController::class, 'classETCView'])->name('classETCView');
    Route::post('/lectureList/lectureDetailMember/{classObjectId}/etcAdd',[App\Http\Controllers\LectureController::class, 'classETCSetting'])->name('classETCSetting');
//    Route::get('/lectureList/lectureDetailMember/{classObjectId}/etcAdd', function (Request $request) { return dd("sdfdsfsdfsdf");})->name('classETCSetting');

//팀배정
//Temporarily change route due to test
    Route::get('/lectureList/lectureDetailTeamSelect/{classObjectId}',[App\Http\Controllers\TestController::class, 'lectureDetailTeamSelect'])->name('lectureDetailTeamSelect');
    Route::post('/lectureList/lectureDetailTeamSelect/names',[App\Http\Controllers\TestController::class, 'teamNamesChange'])->name('lectureDetailTeamSelect.teamNamesChange');
    Route::post('/lectureList/lectureDetailTeamSelect/members',[App\Http\Controllers\TestController::class, 'teamMemberChange'])->name('lectureDetailTeamSelect.teamMemberChange');
    Route::get('/lectureList/teamMaster/{userId}/{teamId}',[App\Http\Controllers\LectureController::class, 'teamMaster'])->name('teamMaster');

    Route::get('/lectureList/lectureDetailTeamSelect/add/{classObjectId}',[App\Http\Controllers\LectureController::class, 'addTeam'])->name('addTeam');
    Route::get('/lectureList/lectureDetailTeamSelect/delete/{teamId}',[App\Http\Controllers\LectureController::class, 'deleteTeam'])->name('deleteTeam');

//문제분석
    Route::get('/lectureList/lectureDetailProblem/{classObjectId}',[App\Http\Controllers\LecCon2::class, 'lectureDetailProblem'])->name('lectureDetailProblem');
//문제분석 디테일
    Route::get('/lectureList/lectureDetailProblem/Analysis/{classObjectId}/{problemAnalysisId}',[App\Http\Controllers\LecCon2::class, 'lectureDetailProblemDetail'])->name('lectureDetailProblemDetail');

//팀활동보고서
    Route::get('/lectureList/lectureDetailTeam/{classObjectId}',[App\Http\Controllers\LecCon2::class, 'lectureDetailTeam'])->name('lectureDetailTeam');
//팀 활동 보고서 디테일 lecture_detail_teamdetail
    Route::get('/lectureList/lectureDetailTeam/Report/{classObjectId}/{teamActivityId}',[App\Http\Controllers\LecCon2::class, 'lectureDetailTeamReport'])->name('lectureDetailTeamReport');
    Route::post('/lectureList/lectureDetailTeam/Report/{classObjectId}/{teamActivityId}',[App\Http\Controllers\LecCon2::class, 'lectureDetailTeamReportFeedback'])->name('lectureDetailTeamReportFeedback');

//평가지
    Route::get('/lectureList/lectureDetailEvolutionPaper/{classObjectId}', [App\Http\Controllers\LectureController::class, 'lectureDetailEvolutionPaper'])->name('lectureDetailEvolutionPaper');
//평가지 디테일
    Route::get('/lectureList/lectureDetailEvolutionPaper/detail/{classObjectId}/{teamId}',[App\Http\Controllers\LectureController::class, 'lectureDetailEvolutionPaperDetail'])->name('lectureDetailEvolutionPaperDetail');

//성찰일지
    Route::get('/lectureList/lectureDetailMind/{classObjectId}',[App\Http\Controllers\LecCon2::class, 'lectureDetailMind'])->name('lectureDetailMind');
//Route::get('/lectureList/lectureDetailMind/{classObjectId}',[App\Http\Controllers\LecCon2::class, function (){ return dd('asdasd');}])->name('lectureDetailMind');
//성찰일지 디테일
//    Route::get('/lectureList/lectureDetailMind/detail/{classObjectId}/{teamId}',[App\Http\Controllers\LectureController::class, 'lectureDetailMindDetail'])->name('lectureDetailMindDetail');
    Route::get('/lectureList/lectureDetailMind/detail/{classObjectId}/{reflectionLogId}',[App\Http\Controllers\LectureController::class, 'lectureDetailMindDetail'])->name('lectureDetailMindDetail');

    Route::get('/lectureList/statistics/{classObjectId}',[App\Http\Controllers\LectureController::class, 'lectureDetailStatistics'])->name('lectureDetailStatistics');


    //팀배정 테스트
    Route::get('/lectureList/testMember/{classObjectId}',[App\Http\Controllers\LectureController::class, 'testMember'])->name('testMember');
    Route::post('/lectureList/testMember/{classObjectId}',[App\Http\Controllers\LectureController::class, 'testMemberAdd'])->name('testMemberAdd');
});

Auth::routes();
