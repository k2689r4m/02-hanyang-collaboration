<?php

namespace App\Http\Controllers;

use http\Params;
use Illuminate\Http\Request;
use App\Models\MyClass;
use App\Models\Team;
use App\Models\User;
use App\Models\Item;
use App\Models\ClassList;
use App\Models\TeamMember;
use App\Models\Expert;
use App\Models\ClassManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    //

    public function profileView (Request $request) {
        return view('profile.info');
    }

    public function profileEditView (Request $request) {
        return view('profile.edit');
    }

    public function profileEdit (Request $request) {
        $request['email'] = Auth::user()->email;
        if(Auth::user()->social === null){
            $validator = Validator::make($request->all(), [
    //            'contact' => ['required', 'string'],//'regex:/(010)[0-9]{8}/'],
    //            'name' => ['required', 'string'],
                'password' => ['required'],
                'new_password' => ['confirmed', 'min:6', 'max:12', 'nullable'],
                'avatar' => ['nullable', 'image'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }

            $credentials = $request->only('email', 'password');

            if (Auth::once($credentials)) {
                $user = Auth::user();
                if ($request->has('new_password') && !is_null($request['new_password'])) {
                    $user->password = Hash::make($request['new_password']);
                }
                if ($request->hasfile('avatar')) {
                    $image = $request->file('avatar');
                    $imageName = $user->id;
                    Storage::disk('local')->putFileAs('/avatar', $image, $imageName);
                }

                $user->save();
                if ($request->has('new_password') && !is_null($request['new_password'])) {
                    return redirect()->route('profileView')->withErrors(['success' => '수정이 완료되었습니다.']);
//                Auth::logout();
                }

                return redirect()->route('profileView');
            }

            return redirect()->back()->withErrors(['password' => '올바른 비밀번호를 입력해 주세요.'])->withInput($request->input());
        }
        else{
            $user = Auth::user();
            if ($request->hasfile('avatar')) {
                $image = $request->file('avatar');
                $imageName = $user->id;
                Storage::disk('local')->putFileAs('/avatar', $image, $imageName);
            }
            $user->save();
            return redirect()->route('profileView');
        }
    }

    public function avatar(Request $request, $userId) {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        if (Storage::disk('local')->exists('avatar/'.$user->id)) {
            $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $path = $storagePath.'avatar/'.$user->id;

            return response()->download($path, 'avatar');
        }
        else {

            //디폴트 프로필 이미지 로직
//            $imageCount = 2;
//            $default =  (strtotime(Auth::user()->created_at) % Auth::id()) % $imageCount;
//
//            if (Storage::disk('local')->exists('avatar/default'.$default)) {
//                $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
//                $path = $storagePath.'avatar/default'.$default;
//
//                return response()->download($path, 'avatar');
//            }
//            else {
//                return redirect('images/icon/user_none.png');
//            }


            return redirect('images/icon/user_none.png');
        }
    }

    public function fetch (Request $request) {
        $validator = Validator::make($request->all(), [
            'subTypes' => ['required', 'string', 'in:classObject,team'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('subTypes')) {
                return ['fail' => 'validate error: subTypes'];
            }
        }

        $parent = null;
        if ($request['subTypes'] == 'classObject') {
            $myClass = MyClass::where('classObjectId', $request['id'])->first();
            if (!$myClass) {
                return ['fail' => 'invalid parameter'];
            }
            if (!$myClass->isPermitted()) {
                return ['fail' => 'permission denied'];
            }

            if (($request->has('itemId') && !is_null($request['itemId'])) || ($request->has('teamId') && !is_null($request['teamId']))) {
                if ($request->has('teamId') && !is_null($request['teamId'])) {
                    $parent = TeamMember::where('teamId', $request['teamId'])->with(['user_info' => function ($query) {
                        $query->select('id', 'name');
                    }])->get();
                }
                else {
                    $item = Item::find($request['itemId']);
                    if ($item) {
                        $card = $item->card();
                        if ($card) {
                            if (is_null($card->teamId)) {
                                $parent = ClassList::where('classObjectId', $request['id'])->with(['user_info' => function ($query) {
                                    $query->select('id', 'name');
                                }])->get();
                            }
                            else {
                                $parent = TeamMember::where('teamId', $card->teamId)->with(['user_info' => function ($query) {
                                    $query->select('id', 'name');
                                }])->get();
                            }
                        }
                    }
                }
            }
            else {
                $parent = ClassList::where('classObjectId', $request['id'])->with(['user_info' => function ($query) {
                    $query->select('id', 'name');
                }])->get();
            }
        }
        else if ($request['subTypes'] == 'team') {
            $team = Team::find($request['id']);
            $myClass = $team->myClass();

            if (!$myClass) {
                return ['fail' => 'invalid parameter'];
            }

            if (!$team->isPermitted()) {
                return ['fail' => 'permission denied'];
            }

            $parent = TeamMember::where('teamId', $request['id'])->with(['user_info' => function ($query) {
                $query->select('id', 'name');
            }])->get();
        }

        if (!$parent) {
            return ['fail' => 'parent'];
        }

//            $users = $parent->user_info;
        $user = [];
        $experts = Expert::where('classObjectId', $myClass->classObjectId)->with(['user_info' => function ($query) {
            $query->select('id', 'name');
        }])->get()->map(function ($item) {
            return $item->user_info;
        })->toArray();

        $user = array_merge($user, $experts);

        $managers = ClassManager::where('classObjectId', $myClass->classObjectId)->with(['user_info' => function ($query) {
            $query->select('id', 'name');
        }])->get()->map(function ($item) {
            return $item->user_info;
        })->toArray();

        $user = array_merge($user, $managers);

        $user[] = $myClass->user()->only(['id', 'name']);
        foreach ($parent as $p) {
            if (!is_null($p->user_info)) {
                $user[] = $p->user_info;
            }
        }

        return $user;
    }
}
