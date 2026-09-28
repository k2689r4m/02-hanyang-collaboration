<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use App\Models\ItemFileList;
use App\Models\ClassApply;

class DownloadController extends Controller
{
    //
    public function download (Request $request, $commentId) {
        $validator = Validator::make(['commentId' => $commentId], [
            'commentId' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('commentId')) {
                return ['fail' => 'id'];
            }
        }

        $comment = Comment::find($commentId);
        if (!$comment) {
            return ['fail' => 'id'];
        }

        if (!$comment->item()->isPermitted(Auth::id())) {
            return ['fail' => 'permission denied'];
        }

//        if (!is_null($comment->item()->card()->classObjectId) && !is_null($comment->item()->card()->teamId) && is_null($comment->item()->card()->myPageId)) {
//            //팀 일때
//            $myClass = $comment->item()->card()->myClass();
//            if (!$myClass) {
//                return null;
//            }
//
//            if (!$myClass->onTeamAccess) {
//                if (!$comment->item()->card()->isPermitted(Auth::id())) {
//                    return null;
//                }
//            }
//        }
//        else {
//            if (!$comment->item()->card()->isPermitted(Auth::id())) {
//                return null;
//            }
//        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.'comment/'.$comment->filePathName;

        return response()->download($path, $comment->fileName);
    }

    public function downloadImage(Request $request, $pathName) {
        $validator = Validator::make(['pathName' => $pathName], [
            'pathName' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('pathName')) {
                return ['fail' => 'pathName'];
            }
        }

        $itemFileList = ItemFileList::where('pathName', $pathName)->first();
        if (!$itemFileList) {
            return ['fail' => 'id'];
        }

        if (!$itemFileList->item()->isPermitted(Auth::id())) {
            return null;
        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.'images/'.$itemFileList->pathName;

        return response()->download($path, $itemFileList->fileName);
    }

    public function downloadFiles(Request $request, $pathName) {
        $validator = Validator::make(['pathName' => $pathName], [
            'pathName' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('pathName')) {
                return ['fail' => 'pathName'];
            }
        }

        $itemFileList = ItemFileList::where('pathName', $pathName)->first();
        if (!$itemFileList) {
            return ['fail' => 'id'];
        }

        if (!$itemFileList->item()->isPermitted(Auth::id())) {
            return null;
        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.'files/'.$itemFileList->pathName;

        return response()->download($path, $itemFileList->fileName);
    }

    public function downloadImage2(Request $request, $pathName, $applyId) {
        $validator = Validator::make(['pathName' => $pathName], [
            'pathName' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('pathName')) {
                return ['fail' => 'pathName'];
            }
        }

        $itemFileList = ItemFileList::where('pathName', $pathName)->first();
        if (!$itemFileList) {
            return ['fail' => 'id'];
        }

        if (!$itemFileList->item()->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $applyId : null)) {
            return null;
        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.'images/'.$itemFileList->pathName;

        return response()->download($path, $itemFileList->fileName);
    }

    public function showImage(Request $request, $pathName) {
        $validator = Validator::make(['pathName' => $pathName], [
            'pathName' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('pathName')) {
                return ['fail' => 'pathName'];
            }
        }

        $itemFileList = ItemFileList::where('pathName', $pathName)->first();
        if (!$itemFileList) {
            return ['fail' => 'id'];
        }

        if (!$itemFileList->item()->isPermitted(Auth::id())) {
            return null;
        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.'images/'.$itemFileList->pathName;

        return response()->file($path);
    }

    public function showImage2(Request $request, $pathName, $applyId) {
        $validator = Validator::make(['pathName' => $pathName], [
            'pathName' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('pathName')) {
                return ['fail' => 'pathName'];
            }
        }

        $itemFileList = ItemFileList::where('pathName', $pathName)->first();
        if (!$itemFileList) {
            return ['fail' => 'id'];
        }

        if (!$itemFileList->item()->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $applyId : null)) {
            return null;
        }

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.'images/'.$itemFileList->pathName;

        return response()->file($path);
    }

    public function downloadApplyFile(Request $request, $applyId, $pathName) {
        $validator = Validator::make(['pathName' => $pathName, 'applyId' => $applyId], [
            'pathName' => ['required', 'string'],
            'applyId' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('pathName')) {
                return false;
            }
            else if ($errors->has('applyId')) {
                return false;
            }
        }

        $classApply = ClassApply::find($applyId);
        if (!$classApply) {
            return false;
        }
        if (Auth::user()->authority != 3 && $classApply->item()->userId != Auth::id()) {
            return false;
        }

        if (!is_null($classApply->progressPlan)) {
            foreach ($classApply->progressPlan as $file) {
                if ($file['filePathName'] == $pathName) {
                    $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
                    $path = $storagePath.'classApplyFile/'.$file['filePathName'];

                    return response()->download($path, $file['fileName']);
                }
            }
        }

        return false;
    }
}
