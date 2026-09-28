<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyPage;
use App\Models\MyClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\ClassList;
use App\Models\ClassObject;
use App\Models\ClassApply;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Rules\StringRequired;
use App\Rules\CheckBoxRequired;

use App\Classes\RequiredValidator;


class TestController extends Controller
{
    ////start test for lecture members
    public function lectureDetailTeamSelect (Request $request, $classObjectId) {

        $classObject= ClassObject::find($classObjectId);
        $teams = Team::where('classObjectId', $classObjectId)->get();
        $members = ClassList::where('classObjectId', $classObjectId)->get();
        foreach ($members as $member) {
//            return dd($member->user());
            if ($member->userId) {
                $member->userName = $member->user()->name;
            }
        }
        $members = $members->filter(function ($member) {
            return $member->userId;
        });
        $members = $members->sortBy('userName');

        $classApply = ClassApply::where('ClassObjectId', $classObjectId)->first();
        return view('lecture.lecture_detail_teamselect',
            ['classObjectId' => $classObjectId, 'teams' => $teams, 'members' => $members, 'classObject' => $classObject,
                'classApply'=> $classApply]);
    }

    public function teamNamesChange (Request $request) {
        $validator = Validator::make($request->all(), [
            'classObjectId' => ['required', 'integer'],
            'id' => ['required', 'array', 'size:20'],
            'id.*' => ['integer', 'nullable'],
            'names' => ['required', 'array', 'size:20'],
            'names.*' => ['string'],
        ]);

        if ($validator->fails()) {
//            $errors = $validator->errors();
//            return dd($errors);

            return redirect()->back();
        }

        foreach ($request['id'] as $key=>$id) {
            if ($id) {
                $team = Team::find($id);
                if ($team) {
                    if ($team->classObjectId == $request['classObjectId']) {
                        $team->name = $request['names'][$key];
                        $team->save();
                        continue;
                    }
                }
            }
        }

        $classApply = ClassApply::where('ClassObjectId', $request['classObjectId'])->first();
//        return dd($request->all());
        return redirect()->route('lectureDetailTeamSelect', ['classObjectId' => $request['classObjectId'], 'classApply' => $classApply]);
    }

    public function teamMemberChange (Request $request) {
        $validator = Validator::make($request->all(), [
           'classObjectId' => ['required', 'integer', 'min:1'],
           'userId' => ['required', 'array'],
           'userId.*' => ['integer', 'min:1'],
           'teamId' => ['required', 'array'],
           'teamId.*' => ['integer', 'min:0']
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $userIds = $request['userId'];
        $teamIds = $request['teamId'];
        if (count($userIds) != count($teamIds)) {
            return redirect()->back();
        }

        $classObjectId = $request['classObjectId'];
        foreach ($userIds as $key=>$userId) {
            $teamMember = TeamMember::where('userId', $userId)->with(['withTeam' => function ($query) use($classObjectId) {
                $query->where('classObjectId', $classObjectId);
            }])->get()->whereNotNull('withTeam');

            if ((int)$teamIds[$key] > 0) {
                $team = Team::find($teamIds[$key]);
                if (!$team || $team->classObjectId != $classObjectId) {

                }
                else {
                    if (count($teamMember)) {
                        if ($teamMember->first()->teamId != $team->id) {
                            $teamMember->first()->teamId = $team->id;
                            $teamMember->first()->save();
                        }
                    }
                    else {
                        TeamMember::create([
                            'userId' => $userId,
                            'teamId' => $teamIds[$key],
                        ]);
                    }
                }
            }
            else {
                if (count($teamMember)) {
                    $teamMember->first()->delete();
                }
            }
        }

        $classApply = ClassApply::where('classObjectId' , $request['classObjectId'])->first();
        return redirect()->route('lectureDetailTeamSelect', ['classObjectId' => $request['classObjectId'], 'classApply' => $classApply]);

//        return dd('asd');
//        $classApply = ClassApply::where('classObjectId' , $request['classObjectId'])->first();
//        return redirect()->route('lectureDetailTeamSelect', ['classObjectId' => $request['classObjectId'], 'classApply' => classApply]);
    }
    ////end test for lecture members

    //
    public function myPageView (Request $request) {
        //테스트를 위해 검색 결과 첫 인덱스만 가져옴
        $myPage = MyPage::where('userId', Auth::id())->first();

        return view('test.test_my_page', ['myPage' => $myPage]);
    }

    public function testImageView (Request $request) {
        $myPage = MyPage::where('userId', Auth::id())->first();

        if (!$myPage) {
            return redirect()->back();
        }

        $directories = Storage::disk('upload')->directories();
        $files = null;
        if (count($directories)) {
            $files = Storage::disk('upload')->files($myPage['id']);
        }

        return view('test.image_upload_test', ['myPage' => $myPage, 'files' => $files]);
    }

    public function imageUpload (Request $request) {
        $request->validate([
            'myPageId' => ['required', 'integer']
        ]);

        $myPage = MyPage::find($request['myPageId']);
        if (!$myPage) {
            return redirect()->back();
        }

        //
        //check permissions
        //

        $directories = Storage::disk('upload')->directories();
        $key = array_search($myPage->id, $directories);
        if (!$key) {
            Storage::disk('upload')->makeDirectory($myPage->id);
        }

        $imageName = time().'.'.$request->image->extension();
//        $request->image->move(public_path('uploads').'/'.$myPage->id, $imageName);
        Storage::disk('upload')->putFileAs($myPage->id, $request->image, $imageName);

        return redirect('testImage');
    }

    public function imageDownload (Request $request, $myPageId, $fileName) {
        $storagePath = Storage::disk('upload')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.$myPageId.'/'.$fileName;
//
//        return response()->file($path);

        return response()->download($path, 'test.jpg');
//
//        return Storage::disk('upload')->download($myPageId.'/'.$fileName);
    }

    public function test2 (Request $request) {
        $COMMON_USER = 1;
        $PROFESSOR = 2;

        $user = Auth::user();
        if (!$user) {
            //middleware가 처리함
            return redirect()->back();
        }
        elseif ($user->authority == $COMMON_USER) {
            $myClasses = ClassList::where('userId', $user->id)->with('class_object')->get();

            return dd($myClasses);
        }
        elseif ($user->authority == $PROFESSOR) {
            $myClasses = MyClass::where('userId', $user->id)->with('class_object')->get();

            return dd($myClasses);
        }

//        $myClassObjects=[];
//        for($i=0;$i<count($classLists_);$i++){
//            $myClassObjects[$i]['myClass'] = $classLists_[$i];
//            $myClassObjects[$i]['myTeams'] = Team::where('classObjectId', $classLists_[$i]->classObjectId)->with('withTeamMembers')->get();
//        }

        return dd($request->all());
    }

    public function test (Request $request) {
        $myClass = MyClass::find($request['id']);
        if (!$myClass) {
            return false;
        }

        $cardsData = [];
        $cardsData['cardsNum'] = $myClass->cardsNum;
        $cardsData['cards'] = $myClass->cards();

        return $cardsData;
    }

    public function dashboardProView (Request $request){

        
        return view('dashboard_pro');
    }
    public function dashboardStuView (Request $request){

        return view('dashboard_stu');
    }


    public function tttt (Request $request) {
        return view('test_test');
    }
    public function tttt2 (Request $request) {
        //return dd($request->all());

        $rv = new RequiredValidator();
        $result = $rv->validate($request->all(), ['str', 'chb']);

        if ($result) {
            return dd($result);
        }

//        $validator = Validator::make($request->all(), [
//            'str' => [new StringRequired],
//            'chb' => ['required', new CheckBoxRequired]
//        ]);
//
//        if ($validator->fails()) {
//            return dd($validator->errors());
//        }

        return dd($request->all());
    }


}
