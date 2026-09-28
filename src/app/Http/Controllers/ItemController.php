<?php

namespace App\Http\Controllers;

use http\Params;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Brain;
use App\Models\Item;
use App\Models\Team;
use App\Models\ItemFileList;
use App\Models\ProblemAnalysis;
use App\Models\ReflectionLog;
use App\Models\ClassApply;
use App\Models\ClassObject;
use App\Models\MyClass;
use App\Models\TeamActivity;
use App\Models\Comment;
use App\Models\operation;
use App\Models\Daehak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;
use App\Events\FixedCardItemEvent;
use App\Events\ItemEvent;
use App\Events\BrainEvent;
use App\Events\CommentEvent;
use App\Models\AdminLog;
use App\Models\ProLog;
use App\Models\ActLog;
use App\Events\AdminLogEvent;
use App\Events\ProLogEvent;
use App\Events\ActLogEvent;
use App\Events\SuperEvent;
use App\Classes\RequiredValidator;

class ItemController extends Controller
{
    //api v1
    public function createItem (Request $request) {

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['string', 'nullable'],
            'images' => ['array'],
            'images.*' => ['image'],
            'imagesNum' => ['array'],
            'imagesNum.*' => ['integer'],
//            'comments' => ['array', 'nullable'],
//            'comments.*' => ['object'],
//            'comments.*.name' => ['required', 'string'],
            'deadLine' => ['date', 'nullable'],
//            'party' => ['array'],
//            'party.*' => ['object'],
//            'party.*.name' => ['required', 'string'],
            'label' => ['integer', 'min:0', 'max:8'],
            'checks' => ['json'],
//            'checks.*' => ['object'],
//            'checks.*.content' => ['required', 'string'],
//            'checks.*.checked' => ['required', 'boolean'],
            'activationDeadline' => ['in:true,false'],
            'activationImage' => ['in:true,false'],
//            'activationParty' => ['in:true,false'],
            'activationLabel' => ['in:true,false'],
            'activationCheck' => ['in:true,false'],
            //이 밑은 잘 모르겠음
            'brain' => ['json'],
            'problemAnalysis' => ['json'],
            'reflectionLog' => ['json'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('cardId')) {
                return 'cardId';
            }
            if ($errors->has('title')) {
                return 'title';
            }
            if ($errors->has('content')) {
                return 'content';
            }
            if ($errors->has('images')) {
                return 'images';
            }
            if ($errors->has('images.*')) {
                return 'images.*';
            }
            if ($errors->has('activationImage')) {
                return 'activationImage';
            }
            if ($errors->has('imagesNum')) {
                return 'imagesNum';
            }
            if ($errors->has('imagesNum.*')) {
                return 'imagesNum.*';
            }
            if ($errors->has('deadLine')) {
                return 'deadLine';
            }
            if ($errors->has('activationDeadline')) {
                return 'activationDeadline';
            }
            if ($errors->has('label')) {
                return 'label';
            }
            if ($errors->has('activationLabel')) {
                return 'activationLabel';
            }
            if ($errors->has('checks')) {
                return 'checks';
            }
            if ($errors->has('activationCheck')) {
                return 'activationCheck';
            }
            if ($errors->has('brain')) {
                return 'brain';
            }
            if ($errors->has('problemAnalysis')) {
                return 'problemAnalysis';
            }
            if ($errors->has('reflectionLog')) {
                return 'reflectionLog';
            }
        }

        $card = Card::find($request['cardId']);
        if (!$card) {
            return false;
        }
        if ($card->myPage()->user()->id != Auth::id()) {
            return false;
        }

        $create = [];
        $create['cardId'] = $request['cardId'];
        $create['title'] = $request['title'];
        if ($request->has('content')) {
            $create['content'] = $request['content'];
        }
        if ($request->has('activationImage')) {
            $create['activationImage'] = $request['activationImage'] == 'true' ? true : false;
        }
        if ($request->has('deadLine')) {
            $create['deadLine'] = $request['deadLine'];
        }
        if ($request->has('activationDeadline')) {
            $create['activationDeadline'] = $request['activationDeadline'] == 'true' ? true : false;
        }
        if ($request->has('label')) {
            $create['label'] = $request['label'];
        }
        if ($request->has('activationLabel')) {
            $create['activationLabel'] = $request['activationLabel'] == 'true' ? true : false;
        }

        if ($request->has('activationCheck')) {
            $create['activationCheck'] = $request['activationCheck'] == 'true' ? true : false;
            if ($create['activationCheck']) {
                if ($request->has('checks')) {
                    $checks = json_decode($request['checks']);
                    $_check = [];
                    if ($checks) {
                        foreach ($checks as $check) {
                            if (gettype($check) == 'object') {
                                if (property_exists($check, 'content') && property_exists($check, 'checked')) {
                                    $__check = ['content' => $check->content, 'checked' => $check->checked];
                                    $validator = Validator::make($__check, [
                                        'content' => ['required', 'string'],
                                        'checked' => ['required', 'boolean'],
                                    ]);

                                    if (!$validator->fails()) {
                                        $_check[] = $__check;
                                    }
                                    else {
                                        return 'checks';
                                    }
                                }
                            }
                        }
                    }
                    $request['checks'] = $_check;

                    $create['checks'] = $request['checks'];
                }
            }
        }
        if ($request->has('brain')) {
            $brain = json_decode($request['brain']);
            $_brain = [];
            if ($brain) {
                foreach ($brain as $b) {
                    if (gettype($b) == 'object') {
                        if (
                            property_exists($b, 'userId') && property_exists($b, 'name')
                            && property_exists($b, 'content') && property_exists($b, 'date')
                            && property_exists($b, 'color')
                        ) {
                            $__brain = [
                                'userId' => $b->userId,
                                'name' => $b->name,
                                'content' => $b->content,
                                'date' => $b->date,
                                'color' => $b->color,
                            ];

                            $validator = Validator::make($__brain, [
                                'userId' => ['required', 'integer'],
                                'name' => ['required', 'string'],
                                'content' => ['required', 'string'],
                                'date' => ['required', 'date'],
                                'color' => ['required', 'integer', 'min:1', 'max:8'],
                            ]);

                            if (!$validator->fails()) {
                                $_brain[] = $__brain;
                            }
                            else {
                                return 'brain';
                            }
                        }
                    }
                }
            }
            $request['brain'] = $_brain;

            $create['brain'] = $request['brain'];
        }
        if ($request->has('problemAnalysis')) {
            $problemAnalysis = json_decode($request['problemAnalysis']);
//            return dd($problemAnalysis);
            $_problemAnalysis = [];
            if ($problemAnalysis) {
                foreach ($problemAnalysis as $p) {
                    if (gettype($p) == 'object') {
                        if (
                            property_exists($p, 'userId') && property_exists($p, 'paNumber')
                            && property_exists($p, 'paName') && property_exists($p, 'paCon1')
                            && property_exists($p, 'paCon2')&& property_exists($p, 'paCon3')
                            && property_exists($p, 'paCon4')
                        ) {
                            $__problemAnalysis = [
                                'userId' => $p->userId,
                                'paNumber' => $p->paNumber,
                                'paName' => $p->paName,
                                'paCon1' => $p->paCon1,
                                'paCon2' => $p->paCon2,
                                'paCon3' => $p->paCon3,
                                'paCon4' => $p->paCon4,
                            ];

                            $validator = Validator::make($__problemAnalysis, [
                                'userId' => ['required', 'integer'],
                                'paNumber' => ['required', 'string'],
                                'paName' => ['required', 'string'],
                                'paCon1' => ['required', 'string'],
                                'paCon2' => ['required', 'string'],
                                'paCon3' => ['required', 'string'],
                                'paCon4' => ['required', 'string'],
                            ]);

                            if (!$validator->fails()) {
                                $_problemAnalysis[] = $__problemAnalysis;
                            }
                            else {
                                return 'problemAnalysis';
                            }
                        }
                    }
                }
            }
            $request['problemAnalysis'] = $_problemAnalysis;

            $create['problemAnalysis'] = $request['problemAnalysis'];
        }
        if ($request->has('reflectionLog')) {
            $reflectionLog = json_decode($request['reflectionLog']);
//            return dd($problemAnalysis);
            $_reflectionLog = [];
            if ($reflectionLog) {
                foreach ($reflectionLog as $r) {
                    if (gettype($r) == 'object') {
                        if (
                            property_exists($r, 'userId') && property_exists($r, 'refNumber')
                            && property_exists($r, 'refName') && property_exists($r, 'refCon1')
                            && property_exists($r, 'refCon2') && property_exists($r, 'refCon3')
                            && property_exists($r, 'refCon4') && property_exists($r, 'refCon5')
                            && property_exists($r, 'refCon6') && property_exists($r, 'refCon7')
                        ) {
                            $__reflectionLog = [
                                'userId' => $r->userId,
                                'refNumber' => $r->refNumber,
                                'refName' => $r->refName,
                                'refCon1' => $r->refCon1,
                                'refCon2' => $r->refCon2,
                                'refCon3' => $r->refCon3,
                                'refCon4' => $r->refCon4,
                                'refCon5' => $r->refCon5,
                                'refCon6' => $r->refCon6,
                                'refCon7' => $r->refCon7,
                            ];

                            $validator = Validator::make($__reflectionLog, [
                                'userId' => ['required', 'integer'],
                                'refNumber' => ['required', 'string'],
                                'refName' => ['required', 'string'],
                                'refCon1' => ['required', 'string'],
                                'refCon2' => ['required', 'string'],
                                'refCon3' => ['required', 'string'],
                                'refCon4' => ['required', 'string'],
                                'refCon5' => ['required', 'string'],
                                'refCon6' => ['required', 'string'],
                                'refCon7' => ['required', 'string'],
                            ]);

                            if (!$validator->fails()) {
                                $_reflectionLog[] = $__reflectionLog;
                            }
                            else {
                                return 'reflectionLog';
                            }
                        }
                    }
                }
            }
            $request['reflectionLog'] = $_reflectionLog;

            $create['reflectionLog'] = $request['reflectionLog'];
        }

//        return dd($create);

        $item = Item::create($create);
        if (!$card->itemsNum) {
            $card->itemsNum = [$item->id];
        }
        else {
            $itemsNum = $card->itemsNum;
            array_push($itemsNum, $item->id);
            $card->itemsNum = $itemsNum;
        }

        $item = Item::find($item->id);

        if ($request->hasfile('images')) {
            $imageNames = [];
            foreach ($request->file('images') as $image) {
                $imageName = uniqid();
//        $request->image->move(public_path('uploads').'/'.$myPage->id, $imageName);
                Storage::disk('upload')->putFileAs('', $image, $imageName);
                $imageFile = ItemFileList::create([
                    'itemId' => $item->id,
                    'type' => 'image',
                    'fileName' => $image->getClientOriginalName(),
                    'pathName' => $imageName,
                    'imgUrl' => asset('storage/uploads/'.$imageName)
                ]);
                $itemImages = $item->images;
                if (!$itemImages) {
                    $itemImages = [];
                }
                $itemImages[] = $imageFile->id;
                $item->images = $itemImages;
            }

            $item->save();
        }

        $myPage = $card->myPage();
        $myPage->version = $myPage->version + 1;
        $myPage->save();
        $card->save();

//        if ($item->images) {
//            $imageInfos = [];
//            foreach ($item['images'] as $image) {
//                $imageInfo = [];
//
//                $imageInfo['fileName'] = $image->fileName;
//                $imageInfo['pathName'] = $image->pathName;
//
//                $path = asset('storage/uploads/'.$myPage->id.'/'.$image->pathName);
//                $imageInfo['imgUrl'] = $path;
//                $imageInfo['imgFile'] = [];
//
//                $imageInfos[] = $imageInfo;
//            }
//
//            $item->images = $imageInfos;
//        }

        $item->imagesNum = $item->images;
        $item->images = $item->itemFileList();

        return json_encode($item);
    }

    public function removeItem (Request $request) {
        $validatedData = $request->validate([
            'id' => ['required'],
        ]);

        $item = Item::find($request['id']);
        if (!$item) {
            return false;
        }
        if ($item->card()->myPage()->user()->id != Auth::id()) {
            return false;
        }

        $card = $item->card();

        $itemsNum = $card->itemsNum;
        $key = array_search($request['id'], $itemsNum);
        array_splice($itemsNum, $key, 1);
        $card->itemsNum = $itemsNum;
        $myPage = $card->myPage();
        $myPage->version = $myPage->version + 1;

        $files = [];
        foreach ($item->itemFileList() as $image) {
            $files[] = $image->pathName;
        }
        Storage::disk('upload')->delete($files);

        $myPage->save();

        $card->save();

        $item->delete();

        return true;
    }

    public function updateItem (Request $request) {
//        return dd($request->all());

        //move
        if ($request->has('destinationPosition') && $request->has('cardId')) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
                'destinationPosition' => ['required', 'integer'],
                'cardId' => ['required', 'integer'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                if ($errors->has('id')) {
                    return 'id';
                }
                if ($errors->has('destinationPosition')) {
                    return 'destinationPosition';
                }
                if ($errors->has('cardId')) {
                    return 'cardId';
                }
            }

            $card = Card::find($request['cardId']);
            if (!$card) {
                return false;
            }
            if ($card->myPage()->user()->id != Auth::id()) {
                return false;
            }
            $item = Item::find($request['id']);
            if (!$item) {
                return false;
            }

            $sourceCard = $item->card();
            if ($sourceCard->myPage()->id != $card->myPage()->id) {
                return false;
            }
            if ($sourceCard->id == $request['cardId']) {
                $sourceCard = $card;
            }
            else {
                $item->cardId = $card->id;
            }

            $itemsNum = $sourceCard->itemsNum;
            $key = array_search($request['id'], $itemsNum);
            array_splice($itemsNum, $key, 1);
            $sourceCard->itemsNum = $itemsNum;

            $destinationPosition = $request['destinationPosition'];
            $itemsNum = $card->itemsNum;
            if ($itemsNum != null) {
                if (count($itemsNum) < $destinationPosition) {
                    $destinationPosition = count($itemsNum);
                }
                else if ($destinationPosition < 0) {
                    $destinationPosition = 0;
                }

                array_splice($itemsNum, $destinationPosition, 0, $item->id);
            }
            else {
                $itemsNum = [$item->id];
            }

            $card->itemsNum = $itemsNum;

            $myPage = $card->myPage();
            $myPage->version = $myPage->version + 1;
            $myPage->save();
            if ($sourceCard->id != $request['cardId']) {
                $sourceCard->save();
            }
            $item->save();
            $card->save();

            return true;
        }

//        return dd($request->all());

        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'content' => ['string', 'nullable'],
            'images' => ['array'],
            'images.*' => ['image'],
            'imagesNum' => ['array'],
            'imagesNum.*' => ['integer'],
//            'comments' => ['array', 'nullable'],
//            'comments.*' => ['object'],
//            'comments.*.name' => ['required', 'string'],
            'deadLine' => ['date', 'nullable'],
//            'party' => ['array'],
//            'party.*' => ['object'],
//            'party.*.name' => ['required', 'string'],
            'label' => ['integer', 'min:0', 'max:8'],
            'checks' => ['json'],
//            'checks.*' => ['object'],
//            'checks.*.content' => ['required', 'string'],
//            'checks.*.checked' => ['required', 'boolean'],
            'activationDeadline' => ['in:true,false'],
            'activationImage' => ['in:true,false'],
//            'activationParty' => ['in:true,false'],
            'activationLabel' => ['in:true,false'],
            'activationCheck' => ['in:true,false'],
            //이 밑은 잘 모르겠음
            'brain' => ['json'],
            'problemAnalysis' => ['json'],
            'reflectionLog' => ['json'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('id')) {
                return 'id';
            }
            if ($errors->has('cardId')) {
                return 'cardId';
            }
            if ($errors->has('title')) {
                return 'title';
            }
            if ($errors->has('content')) {
                return 'content';
            }
            if ($errors->has('images')) {
                return 'images';
            }
            if ($errors->has('images.*')) {
                return 'images.*';
            }
            if ($errors->has('activationImage')) {
                return 'activationImage';
            }
            if ($errors->has('imagesNum')) {
                return 'imagesNum';
            }
            if ($errors->has('imagesNum.*')) {
                return 'imagesNum.*';
            }
            if ($errors->has('deadLine')) {
                return 'deadLine';
            }
            if ($errors->has('activationDeadline')) {
                return 'activationDeadline';
            }
            if ($errors->has('label')) {
                return 'label';
            }
            if ($errors->has('activationLabel')) {
                return 'activationLabel';
            }
            if ($errors->has('checks')) {
                return 'checks';
            }
            if ($errors->has('activationCheck')) {
                return 'activationCheck';
            }
            if ($errors->has('brain')) {
                return 'brain';
            }
            if ($errors->has('problemAnalysis')) {
                return 'problemAnalysis';
            }
            if ($errors->has('reflectionLog')) {
                return 'reflectionLog';
            }
        }

        $item = Item::find($request['id']);
        if (!$item) {
            return false;
        }

        if ($request->has('title')) {
            $item->title = $request['title'];
        }
        if ($request->has('content')) {
            $item->content = $request['content'];
        }
        if ($request->has('deadLine')) {
            $item->deadLine = $request['deadLine'];
        }
        if ($request->has('activationImage')) {
            $item->activationImage = $request['activationImage'] == 'true' ? true : false;
        }
        if ($request->has('activationDeadline')) {
            $item->activationDeadline = $request['activationDeadline'] == 'true' ? true : false;
        }
        if ($request->has('label')) {
            $item->label = $request['label'];
        }
        if ($request->has('activationLabel')) {
            $item->activationLabel = $request['activationLabel'] == 'true' ? true : false;
        }


        $imageNames = $item->images;
        $_imageNames = [];
        $newNum = 0;
        $imagesNum = $request['imagesNum'];
        if (!$imagesNum) {
            $imagesNum = [];
        }
        foreach ($imagesNum as $imageNum) {
            if ($imageNum > 0) {
                $key = array_search($imageNum, $imageNames);
                if (!$key && gettype($key) == 'boolean') {
                    continue;
                }
                $_imageNames[] = $imageNames[$key];

                array_splice($imageNames, $key, 1);
            }
            else {
                if ($request->hasfile('images')) {
                    $file = $request->file('images')[$newNum++];
                    if (!$file) {
                        continue;
                    }

                    $imageName = uniqid();
                    Storage::disk('upload')->putFileAs('', $file, $imageName);
                    $imageFile = ItemFileList::create([
                        'itemId' => $item->id,
                        'type' => 'image',
                        'fileName' => $file->getClientOriginalName(),
                        'pathName' => $imageName,
                        'imgUrl' => asset('storage/uploads/'.$imageName)
                    ]);
                    $_imageNames[] = $imageFile->id;
                }
            }
        }

        $item->images = $_imageNames;

        if ($imageNames) {
            foreach ($imageNames as $imageName) {
                $itemFileList = ItemFileList::find($imageName);
                if ($itemFileList) {
                    Storage::disk('upload')->delete($itemFileList->pathName);

                    $itemFileList->delete();
                }
            }
        }

        if ($request->has('checks')) {
            $checks = json_decode($request['checks']);
            $_check = [];
            if ($checks) {
                foreach ($checks as $check) {
                    if (gettype($check) == 'object') {
                        if (property_exists($check, 'content') && property_exists($check, 'checked')) {
                            $__check = ['content' => $check->content, 'checked' => $check->checked];
                            $validator = Validator::make($__check, [
                                'content' => ['required', 'string'],
                                'checked' => ['required', 'boolean'],
                            ]);

                            if (!$validator->fails()) {
                                $_check[] = $__check;
                            }
                            else {
                                return 'checks';
                            }
                        }
                    }
                }
            }
            $request['checks'] = $_check;

            $item->checks = $request['checks'];
        }
        if ($request->has('activationCheck')) {
            $item->activationCheck = $request['activationCheck'] == 'true' ? true : false;
        }
        if ($request->has('brain')) {
            $brain = json_decode($request['brain']);
            $_brain = [];
            if ($brain) {
                foreach ($brain as $b) {
                    if (gettype($b) == 'object') {
                        if (
                            property_exists($b, 'userId') && property_exists($b, 'name')
                            && property_exists($b, 'content') && property_exists($b, 'date')
                            && property_exists($b, 'color')
                        ) {
                            $__brain = [
                                'userId' => $b->userId,
                                'name' => $b->name,
                                'content' => $b->content,
                                'date' => $b->date,
                                'color' => $b->color,
                            ];

                            $validator = Validator::make($__brain, [
                                'userId' => ['required', 'integer'],
                                'name' => ['required', 'string'],
                                'content' => ['required', 'string'],
                                'date' => ['required', 'date'],
                                'color' => ['required', 'integer', 'min:1', 'max:8'],
                            ]);

                            if (!$validator->fails()) {
                                $_brain[] = $__brain;
                            }
                            else {
                                return 'brain';
                            }
                        }
                    }
                }
            }
            $request['brain'] = $_brain;

            $item->brain = $request['brain'];
        }
        if ($request->has('problemAnalysis')) {
            $problemAnalysis = json_decode($request['problemAnalysis']);
//            return dd($problemAnalysis);
            $_problemAnalysis = [];
            if ($problemAnalysis) {
                foreach ($problemAnalysis as $p) {
                    if (gettype($p) == 'object') {
                        if (
                            property_exists($p, 'userId') && property_exists($p, 'paNumber')
                            && property_exists($p, 'paName') && property_exists($p, 'paCon1')
                            && property_exists($p, 'paCon2')&& property_exists($p, 'paCon3')
                            && property_exists($p, 'paCon4')
                        ) {
                            $__problemAnalysis = [
                                'userId' => $p->userId,
                                'paNumber' => $p->paNumber,
                                'paName' => $p->paName,
                                'paCon1' => $p->paCon1,
                                'paCon2' => $p->paCon2,
                                'paCon3' => $p->paCon3,
                                'paCon4' => $p->paCon4,
                            ];

                            $validator = Validator::make($__problemAnalysis, [
                                'userId' => ['required', 'integer'],
                                'paNumber' => ['required', 'string'],
                                'paName' => ['required', 'string'],
                                'paCon1' => ['required', 'string'],
                                'paCon2' => ['required', 'string'],
                                'paCon3' => ['required', 'string'],
                                'paCon4' => ['required', 'string'],
                            ]);

                            if (!$validator->fails()) {
                                $_problemAnalysis[] = $__problemAnalysis;
                            }
                            else {
                                return 'problemAnalysis';
                            }
                        }
                    }
                }
            }
            $request['problemAnalysis'] = $_problemAnalysis;

            $item->problemAnalysis = $request['problemAnalysis'];
        }
        if ($request->has('reflectionLog')) {
            $reflectionLog = json_decode($request['reflectionLog']);
//            return dd($problemAnalysis);
            $_reflectionLog = [];
            if ($reflectionLog) {
                foreach ($reflectionLog as $r) {
                    if (gettype($r) == 'object') {
                        if (
                            property_exists($r, 'userId') && property_exists($r, 'refNumber')
                            && property_exists($r, 'refName') && property_exists($r, 'refCon1')
                            && property_exists($r, 'refCon2') && property_exists($r, 'refCon3')
                            && property_exists($r, 'refCon4') && property_exists($r, 'refCon5')
                            && property_exists($r, 'refCon6') && property_exists($r, 'refCon7')
                        ) {
                            $__reflectionLog = [
                                'userId' => $r->userId,
                                'refNumber' => $r->refNumber,
                                'refName' => $r->refName,
                                'refCon1' => $r->refCon1,
                                'refCon2' => $r->refCon2,
                                'refCon3' => $r->refCon3,
                                'refCon4' => $r->refCon4,
                                'refCon5' => $r->refCon5,
                                'refCon6' => $r->refCon6,
                                'refCon7' => $r->refCon7,
                            ];

                            $validator = Validator::make($__reflectionLog, [
                                'userId' => ['required', 'integer'],
                                'refNumber' => ['required', 'string'],
                                'refName' => ['required', 'string'],
                                'refCon1' => ['required', 'string'],
                                'refCon2' => ['required', 'string'],
                                'refCon3' => ['required', 'string'],
                                'refCon4' => ['required', 'string'],
                                'refCon5' => ['required', 'string'],
                                'refCon6' => ['required', 'string'],
                                'refCon7' => ['required', 'string'],
                            ]);

                            if (!$validator->fails()) {
                                $_reflectionLog[] = $__reflectionLog;
                            }
                            else {
                                return 'reflectionLog';
                            }
                        }
                    }
                }
            }
            $request['reflectionLog'] = $_reflectionLog;

            $item->reflectionLog = $request['reflectionLog'];
        }

        $item->save();

        $item = Item::find($item->id);
        if (!$item) {
            return false;
        }

        $item->imagesNum = $item->images;
        $item->images = $item->itemFileList();

        return json_encode($item);
    }

    public function fetchItem (Request $request, $cardId) {
//        $request->validate([
//            'cardId' => ['required']
//        ]);

        $card = Card::find($cardId);
        if (!$card) {
            return false;
        }
        if ($card->myPage()->userId != Auth::id()) {
            return false;
        }

        $items = $card->items();
        if ($items->count() <= 0) {
            return false;
        }

        foreach ($items as $item) {
            $item->imagesNum = $item->images;
            $item->images = $item->itemFileList();
//            $item->checks = json_decode($item->checks);
        }

        return json_encode(['itemsNum' => $card->itemsNum, 'items' => $items]);
    }

    public function downloadFile (Request $request, $myPageId, $fileName, $fileRealName) {
        $storagePath = Storage::disk('upload')->getDriver()->getAdapter()->getPathPrefix();
        $path = $storagePath.$fileName;

        return response()->download($path, $fileRealName);
    }

    //api v2
    public function fetch (Request $request) {
        $item = Item::find($request['id']);
        if (!$item) {
            return ['fail' => 'invalid id'];
        }

        if (!$item->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $request['subId'] : null)) {
            return ['fail' => 'permission denied'];
        }

//        $item->with_images = $item->withImages()->get();
        $item->load('withImages');
        $item->item_file_list = $item->itemFileList();
        $item->load('withFiles');
        $item->with_comments = $item->withComments()->get();

        switch($item->type) {
            case 1:
                $item->with_problem_analysis = $item->withProblemAnalysis()->first();
                break;
            case 2:
                $item->with_reflection_log = $item->withReflectionLog()->first();
                break;
            case 3:
                $item->with_brains = $item->withBrains()->get();
                break;
            case 4:
                $item->with_class_apply = $item->withClassApply()->first();
                break;
            case 5:
                $item->with_team_activity = $item->withTeamActivity()->first();
                break;
            case 8:
                $item->with_operation_result = $item->withOperationResult()->first();
                break;
            default:
                break;
        }

        return [$item];
    }

    public function createProblemAnalysis (Card $card, $problemAnalysis) {
        try {
            $problemAnalysis = (array)json_decode($problemAnalysis);

            $validator = Validator::make($problemAnalysis, [
                'studentId' => ['required', 'string'],
                'name' => ['required', 'string'],
                'content1' => ['required', 'string'],
                'content2' => ['required', 'string'],
                'content3' => ['required', 'string'],
                'content4' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            return ['fail' => false, 'sub' => true, new ProblemAnalysis([
                'studentId' => $problemAnalysis['studentId'],
                'name' => Auth::user()->name,
                'content1' => $problemAnalysis['content1'],
                'content2' => $problemAnalysis['content2'],
                'content3' => $problemAnalysis['content3'],
                'content4' => $problemAnalysis['content4'],
            ])];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function createReflectionLog (Card $card, $reflectionLog) {
        try {
            $reflectionLog = (array)json_decode($reflectionLog);

            $validator = Validator::make($reflectionLog, [
                'studentId' => ['required', 'string'],
                'name' => ['required', 'string'],
                'content1' => ['required', 'string'],
                'content2' => ['required', 'string'],
                'content3' => ['required', 'string'],
                'content4' => ['required', 'string'],
                'content5' => ['required', 'string'],
                'content6' => ['required', 'string'],
                'content7' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            return ['fail' => false, 'sub' => true, new ReflectionLog([
                'studentId' => $reflectionLog['studentId'],
                'name' => Auth::user()->name,
                'content1' => $reflectionLog['content1'],
                'content2' => $reflectionLog['content2'],
                'content3' => $reflectionLog['content3'],
                'content4' => $reflectionLog['content4'],
                'content5' => $reflectionLog['content5'],
                'content6' => $reflectionLog['content6'],
                'content7' => $reflectionLog['content7'],
            ])];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function createTeamActivity (Card $card, $teamActivity) {
        try {
            $teamActivity = (array)json_decode($teamActivity);

            $validator = Validator::make($teamActivity, [
                'dateTime' => ['required', 'string'],
                'problemSolvingProcess' => ['required', 'string'],
                'attendees' => ['required', 'string'],
                'mainActivities' => ['required', 'string'],
                'task1' => ['required', 'string'],
                'task2' => ['required', 'string'],
                'discuss1' => ['required', 'string'],
                'schedule1' => ['required', 'string'],
                'schedule2' => ['required', 'string'],
                'schedule3' => ['required', 'string'],
                'schedule4' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            return ['fail' => false, 'sub' => true, new TeamActivity([
                'dateTime' => $teamActivity['dateTime'],
                'problemSolvingProcess' => $teamActivity['problemSolvingProcess'],
                'attendees' => $teamActivity['attendees'],
                'mainActivities' => $teamActivity['mainActivities'],
                'task1' => $teamActivity['task1'],
                'task2' => $teamActivity['task2'],
                'discuss1' => $teamActivity['discuss1'],
                'schedule1' => $teamActivity['schedule1'],
                'schedule2' => $teamActivity['schedule2'],
                'schedule3' => $teamActivity['schedule3'],
                'schedule4' => $teamActivity['schedule4'],
            ])];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function createBrains (Card $card, $brains) {
        try {
            $brains = (array)json_decode($brains);

            $validator = Validator::make($brains, [
                '*->content' => ['required', 'string'],
                '*->color' => ['required', 'integer', 'min:1', 'max:8'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $brainsData = [];
            foreach ($brains as $b) {
                $brainsData[] = new Brain([
                    'userId' => Auth::id(),
                    'content' => $b->content,
                    'color' => $b->color,
                ]);
            }

            return ['fail' => false, 'sub' => true, $brainsData];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function createClassApply (Card $card, $classApply, $title) {
        try {

            $validator = Validator::make(['classApply' => $classApply], [
                'classApply' => ['json'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $classApply = (array)json_decode($classApply);

            $classApply['title'] = $title;

            $rv = new RequiredValidator();
            $result = $rv->validate($classApply, [
                'type',
                'grade',
                'size1',
                'proSize1',
                'department',
//                'major',
                'korName',
                'engName',
                'gradesPoint',
                'lecturePoint',
                'exercisePoint',
                'description',
                'meca',
                'role1',
                'role3',
                'role4',
                'role5',
                'expected1',
                'expected2',
                'expected3',
                'expected4',
                'expected5',
                'expected6',
                'expected7',
//                'aplName',
//                'aplOrg',
//                'aplTel',
//                'aplPhone',
//                'aplEmail',
                'agree1',
                'basic1',
                'basic2',
                'basic3',
                'basic4',
                'basic5',
                'basic6',
                'basicPlan',
                'sceContent',
                'sceGoal',
                'sceTitle',
                'sceRole',
                'sceDetail',
                'planDetail',
            ], 'classApply');

            if ($result) {
                return ['fail' => true, 'sub' => true, $result];
            }

            $validator = Validator::make($classApply, [
                'mode' => ['required', 'integer', 'min:1', 'max:2'], //1:기존 과목, 2: 이전 과목
                'type' => ['required', 'integer', 'min:1', 'max:4'],
                'grade' => ['required', 'integer', 'min:1', 'max:4'],
                'size1' => ['required', 'integer', 'min:1', 'max:3'],
                'size2' => ['required_if:size1,3', 'exclude_unless:size1,3', 'integer', 'min:31'], ////////////
                'proSize1' => ['required', 'integer', 'min:1', 'max:3'],
                'proSize2' => ['required_if:proSize1,2', 'exclude_unless:proSize1,2', 'integer', 'min:1'], /////////////
                'proSize3' => ['required_if:proSize1,3', 'exclude_unless:proSize1,3', 'integer', 'min:1'], /////////////
                'daehak' => ['required', 'string'],
                'department' => ['required', 'string'],
//                'major' => ['required', 'string'],
                'special' => ['required', 'boolean'],
                'special2' => ['required', 'boolean'],
                'special3' => ['required', 'boolean'],
                'special4' => ['required', 'boolean'],
                'korName' => ['required', 'string', 'same:title'],
                'engName' => ['required', 'string'],
                'gradesPoint' => ['required', 'integer', 'min:0'],
                'lecturePoint' => ['required', 'integer', 'min:0'],
                'exercisePoint' => ['required', 'integer', 'min:0'],
                'description' => ['required', 'string'],
                'meca' => ['required', 'integer', 'min:1', 'max:4'],
                'agency' => ['string', 'nullable'],
                'expert' => ['string', 'nullable'],
                'role1' => ['required', 'boolean'],
                'role3' => ['required', 'boolean'],
                'role4' => ['required', 'boolean'],
                'role5' => ['required', 'boolean'],
                'role2' => ['required_if:role1,4', 'exclude_unless:role1,4', 'string'], //////////////////////
                'expected1' => ['required', 'boolean'],
                'expected2' => ['required', 'boolean'],
                'expected3' => ['required', 'boolean'],
                'expected4' => ['required', 'boolean'],
                'expected5' => ['required', 'boolean'],
                'expected6' => ['required', 'boolean'],
                'expected7' => ['required', 'boolean'],
                'expected8' => ['required_if:expected7,1', 'exclude_unless:expected7,1', 'string'], /////////////////
//                'aplName' => ['required', 'string'],
//                'aplSign' => [],
//                'aplOrg' => ['required', 'string'],
//                'aplTel' => ['required', 'string'],
//                'aplPhone' => ['required', 'string'],
//                'aplEmail' => ['required', 'string'],//'email'],
                'applicant' => ['required', 'array'],
                'agree1' => ['required', 'boolean', 'in:1,true'],
                'duration' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
                'duration2' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conName' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conPer' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string', 'in:100'],
                'contribute' => ['required_if:mode,1', 'exclude_unless:mode,1', 'array'],
                'conDescription' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
                'agree2' => ['required_if:mode,1', 'exclude_unless:mode,1', 'boolean', 'in:1,true'],
                'agree3' => ['required_if:mode,1', 'exclude_unless:mode,1', 'boolean', 'in:1,true'],
                'basic1' => ['required', 'string'],
                'basic2' => ['required', 'string'],
                'basic3' => ['required', 'string'],
                'basic4' => ['required', 'string'],
                'basic5' => ['required', 'string'],
                'basic6' => ['required', 'string'],
                'basicPlan' => ['required', 'string'],
                'sceContent' => ['required', 'string'],
                'sceGoal' => ['required', 'string'],
                'sceTitle' => ['required', 'string'],
                'sceRole' => ['required', 'string'],
                'sceDetail' => ['required', 'string'],
                'planDetail' => ['required', 'array'],
//                'planDetail.*' => [],
//                'planDetail.*.content' => ['required', 'nullable', 'string'],
//                'planDetail.*.level' => ['required', 'nullable', 'string'],
//                'planDetail.*.stuContent' => ['required', 'nullable', 'string'],
//                'planDetail.*.subject' => ['required', 'nullable', 'string'],
//                'planDetail.*.method1' => ['required', 'boolean'],
//                'planDetail.*.method2' => ['required', 'boolean'],
//                'planDetail.*.method3' => ['required', 'boolean'],
//                'planDetail.*.test' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                return ['fail' => true, 'sub' => true, $errors];
            }

            $daehak = Daehak::where('name', $classApply['daehak'])->where('department', $classApply['department'])->first();//->where('major', $classApply['major'])->first();
            if (!$daehak) {
                return ['fail' => true, 'sub' => true, ['department' => ['올바른 학과,전공 정보를 입력해주세요.']]];
            }

            if ($classApply['mode'] == "1") {
                $contributes = [];
                foreach ($classApply['contribute'] as $key=>$c) {
                    $contributes[] = (array)$c;
                }

                $validator = Validator::make($contributes, [
                    '*.name' => ['required', 'string'], // required
                    '*.per' => ['required', 'integer'], // required
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return ['fail' => true, 'sub' => true, 'contribute' => true, $errors];
                }

                $contributes = collect($contributes);
                if ($contributes->sum('per') != 100) {
                    return ['fail' => true, 'sub' => true, 'contribute' => true, ['contribute' => ['기여도의 합이 100이 아닙니다.']]];
                }
            }

            $applicants = [];
            foreach ($classApply['applicant'] as $key=>$a) {
                $applicants[] = (array)$a;
            }

            $validator = Validator::make($applicants, [
                '*.name' => ['required', 'string'], // required
                '*.org' => ['required', 'string'], // required
                '*.tel' => ['required', 'string'], // required
                '*.phone' => ['required', 'string'], // required
                '*.email' => ['required', 'email'], // required
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, 'applicant' => true, $errors];
            }

            $applicants = collect($applicants);
            if ($applicants->count() <= 0) {
                return ['fail' => true, 'sub' => true, 'applicant' => true, ['applicant' => ['신청자 정보를 입력해주세요.']]];
            }

            $planDetail = [];
            foreach ($classApply['planDetail'] as $p) {
                $planDetail[] = (array)$p;
            }

            $validator = Validator::make($planDetail, [
                '*.content' => ['string', 'nullable'], // required
                '*.level' => ['string', 'nullable'], // required
                '*.stuContent' => ['string', 'nullable'], // required
                '*.subject' => ['string', 'nullable'], // required
                '*.method1' => ['required', 'boolean'],
                '*.method2' => ['required', 'boolean'],
                '*.method3' => ['required', 'boolean'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            if (!(
                (int)$classApply['expected1']
                + (int)$classApply['expected2']
                + (int)$classApply['expected3']
                + (int)$classApply['expected4']
                + (int)$classApply['expected5']
                + (int)$classApply['expected6']
                + (int)$classApply['expected7']
            )) {
                return ['fail' => true, 'sub' => true, ['expected' => '예상수업결과물은 필수입력 항목입니다.']];
            }

            return ['fail' => false, 'sub' => true, new ClassApply([
                'state' => 'wait',
                'mode' => $classApply['mode'],
                'type' => $classApply['type'],
                'grade' => $classApply['grade'],
                'size1' => $classApply['size1'],
                'size2' => (int)$classApply['size1'] == 3 ? $classApply['size2'] : null,
                'proSize1' => $classApply['proSize1'],
                'proSize2' => (int)$classApply['proSize1'] == 2 ? $classApply['proSize2'] : null,
                'proSize3' => (int)$classApply['proSize1'] == 3 ? $classApply['proSize3'] : null,
                'daehak' => $classApply['daehak'],
                'department' => $classApply['department'],
                'major' => $classApply['major'],
                'special' => $classApply['special'],
                'special2' => $classApply['special2'],
                'special3' => $classApply['special3'],
                'special4' => $classApply['special4'],
                'korName' => $classApply['korName'],
                'engName' => $classApply['engName'],
                'gradesPoint' => $classApply['gradesPoint'],
                'lecturePoint' => $classApply['lecturePoint'],
                'exercisePoint' => $classApply['exercisePoint'],
                'description' => $classApply['description'],
                'meca' => $classApply['meca'],
                'agency' => $classApply['agency'],
                'expert' => $classApply['expert'],
                'role1' => $classApply['role1'],
                'role3' => $classApply['role3'],
                'role4' => $classApply['role4'],
                'role5' => $classApply['role5'],
                'role2' => (int)$classApply['role1'] == 4 ? $classApply['role2'] : null,
                'expected1' => $classApply['expected1'],
                'expected2' => $classApply['expected2'],
                'expected3' => $classApply['expected3'],
                'expected4' => $classApply['expected4'],
                'expected5' => $classApply['expected5'],
                'expected6' => $classApply['expected6'],
                'expected7' => $classApply['expected7'],
                'expected8' => (int)$classApply['expected7'] > 0 ? $classApply['expected8'] : null,
//                'aplName' => $classApply['aplName'],
//                'aplSign' => $classApply['aplSign'],
//                'aplOrg' => $classApply['aplOrg'],
//                'aplTel' => $classApply['aplTel'],
//                'aplPhone' => $classApply['aplPhone'],
//                'aplEmail' => $classApply['aplEmail'],
                'applicant' => $classApply['applicant'],
                'agree1' => $classApply['agree1'],
                'duration' => (int)$classApply['mode'] == 1 ? $classApply['duration'] : null,
                'duration2' => (int)$classApply['mode'] == 1 ? $classApply['duration2'] : null,
//                'conName' => (int)$classApply['mode'] == 1 ? $classApply['conName'] : null,
//                'conPer' => (int)$classApply['mode'] == 1 ? $classApply['conPer'] : null,
                'contribute' => $classApply['contribute'],
                'conDescription' => (int)$classApply['mode'] == 1 ? $classApply['conDescription'] : null,
                'agree2' => (int)$classApply['mode'] == 1 ? $classApply['agree2'] : null,
                'agree3' => (int)$classApply['mode'] == 1 ? $classApply['agree3'] : null,
                'basic1' => $classApply['basic1'],
                'basic2' => $classApply['basic2'],
                'basic3' => $classApply['basic3'],
                'basic4' => $classApply['basic4'],
                'basic5' => $classApply['basic5'],
                'basic6' => $classApply['basic6'],
                'basicPlan' => $classApply['basicPlan'],
                'sceContent' => $classApply['sceContent'],
                'sceGoal' => $classApply['sceGoal'],
                'sceTitle' => $classApply['sceTitle'],
                'sceRole' => $classApply['sceRole'],
                'sceDetail' => $classApply['sceDetail'],
                'planDetail' => $classApply['planDetail'],
            ])];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, ['exception' => ['exception']]];
        }
    }
    public function createOperationResult (Card $card, $operationResult) {
        try {
            $operationResult = (array)json_decode($operationResult);

            $rv = new RequiredValidator();
            $result = $rv->validate($operationResult, [
                'semester',
                'college',
                'lectureName',
                'grade',
                'division',
                'grades',
                'professor',
                'size',
                'icpblType',
                'summary',
                'classGoal',
                'method',
                'basicPlan',
                'title',
                'role',
                'scenario',
                'process',
                'outputType1',
                'outputType2',
                'outputType3',
                'outputType4',
                'outputType5',
                'outputType6',
                'outputType7',
                'finalOutput',
                'mainStudent',
                'sTitle',
                'sName',
                'sRole1',
                'sLink',
                'sFeedback',
                'sOpinion',
                'pr1',
                'pr2',
                'pr3',
                'pr4',
                'pr5',
                'pr6',
            ], 'operationResult');

            if ($result) {
                return ['fail' => true, 'sub' => true, $result];
            }

            $validator = Validator::make($operationResult, [
                'semester' => ['required', 'string'],
                'college' => ['required', 'string'],
                'lectureName' => ['required', 'string'],
                'grade' => ['required', 'string'],
                'division' => ['required', 'string'],
                'grades' => ['required', 'string'],
                'professor' => ['required', 'string'],
                'size' => ['required', 'string'],
                'icpblType' => ['required', 'string'],
                'summary' => ['required', 'string'],
                'classGoal' => ['required', 'string'],
                'method' => ['required', 'string'],
                'basicPlan' => ['required', 'string'],
                'title' => ['required', 'string'],
                'role' => ['required', 'string'],
                'scenario' => ['required', 'string'],
                'process' => ['required', 'array'],
                'outputType1' => ['required', 'string'],
                'outputType2' => ['required', 'string'],
                'outputType3' => ['required', 'string'],
                'outputType4' => ['required', 'string'],
                'outputType5' => ['required', 'string'],
                'outputType6' => ['required', 'string'],
                'outputType7' => ['required', 'string'],
                'outputType8' => ['required_if:outputType7,1', 'exclude_unless:outputType7,1', 'string'],
                'finalOutput' => ['required', 'string'],
                'mainStudent' => ['required', 'string'],
                'sTitle' => ['required', 'string'],
                'sName' => ['required', 'string'],
                'sRole1' => ['required', 'string'],
                'sRole2' => ['required_if:sRole,5', 'exclude_unless:sRole1,5', 'string'],
                'sLink' => ['required', 'string'],
                'sFeedback' => ['required', 'string'],
                'sOpinion' => ['required', 'string'],
                'pr1' => ['required', 'string'],
                'pr2' => ['required', 'string'],
                'pr3' => ['required', 'string'],
                'pr4' => ['required', 'string'],
                'pr5' => ['required', 'string'],
                'pr6' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            return ['fail' => false, 'sub' => true, new operation([
                'semester' => $operationResult['semester'],
                'college' => $operationResult['college'],
                'lectureName' => $operationResult['lectureName'],
                'grade' => $operationResult['grade'],
                'division' => $operationResult['division'],
                'grades' => $operationResult['grades'],
                'professor' => $operationResult['professor'],
                'size' => $operationResult['size'],
                'icpblType' => $operationResult['icpblType'],
                'summary' => $operationResult['summary'],
                'classGoal' => $operationResult['classGoal'],
                'method' => $operationResult['method'],
                'basicPlan' => $operationResult['basicPlan'],
                'title' => $operationResult['title'],
                'role' => $operationResult['role'],
                'scenario' => $operationResult['scenario'],
                'process' => $operationResult['process'],
                'outputType1' => $operationResult['outputType1'],
                'outputType2' => $operationResult['outputType2'],
                'outputType3' => $operationResult['outputType3'],
                'outputType4' => $operationResult['outputType4'],
                'outputType5' => $operationResult['outputType5'],
                'outputType6' => $operationResult['outputType6'],
                'outputType7' => $operationResult['outputType7'],
                'outputType8' => $operationResult['outputType8'],
                'finalOutput' => $operationResult['finalOutput'],
                'mainStudent' => $operationResult['mainStudent'],
                'sTitle' => $operationResult['sTitle'],
                'sName' => $operationResult['sName'],
                'sRole1' => $operationResult['sRole1'],
                'sRole2' => $operationResult['sRole2'],
                'sLink' => $operationResult['sLink'],
                'sFeedback' => $operationResult['sFeedback'],
                'sOpinion' => $operationResult['sOpinion'],
                'pr1' => $operationResult['pr1'],
                'pr2' => $operationResult['pr2'],
                'pr3' => $operationResult['pr3'],
                'pr4' => $operationResult['pr4'],
                'pr5' => $operationResult['pr5'],
                'pr6' => $operationResult['pr6'],
            ])];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    //수정 예정
    public function broadcast(Card $card, Item $item, $type) {
        $cardType = [
            'problemAnalysis' => 10,
            'reflectionLog' => 7,
            'teamActivity' => 9,
        ];
        $itemType = [
            'problemAnalysis' => 1,
            'reflectionLog' => 2,
            'teamActivity' => 5,
        ];
        if (!is_null($card->classObjectId) && is_null($card->teamId)) {
            if ($card->type != $cardType[$type]) {
//                $_myClass = $card->myClass();
//                $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', $cardType[$type])->first();

//                if ($_myClass->onTeamAccess) {
//                    $_cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->with(['withItems' => function ($query) use($itemType, $type) {
//                            $query->where('type', $itemType[$type]);
//                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//                }
//                else {
//                    $_cards = Card::where('classObjectId', $card->classObjectId)
//                        ->where('teamId', null)->with(['withItems' => function ($query) use($itemType, $type) {
//                            $query->where('type', $itemType[$type]);
//                        }])->orderBy('id', 'asc')->get();
//                }
//
//                $_itemsNum = [];
//                foreach ($_cards as $c) {
//                    if (!is_null($c->itemsNum)) {
//                        foreach ($c->itemsNum as $itemNum) {
//                            $_item = $c->withItems->where('id', $itemNum)->first();
//                            if ($_item) {
//                                $_itemsNum[] = $_item->id;
//                            }
//                        }
//                    }
//                }
//
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum, $card->classObjectId, 'create', 'MYCLASS')
//                    );
//                }

                broadcast(
                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->classObjectId, 'MYCLASS', 'add')
                )->toOthers();
            }
        }
        else if (!is_null($card->classObjectId) && !is_null($card->teamId)) {
            $_myClass = $card->myClass();
            $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', $cardType[$type])->first();
            $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', $cardType[$type])->get();

            $_cards = Card::where('classObjectId', $card['classObjectId'])->where('type', $cardType[$type])->whereNotNull('teamId')
                ->with(['withItems' => function ($query) use($itemType, $type) {
                    $query->where('type', $itemType[$type]);
                }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();

//            if ($_myClass->onTeamAccess) {
//                $_cards = Card::where('classObjectId', $card['classObjectId'])->where('type', $cardType[$type])->whereNotNull('teamId')
//                    ->with(['withItems' => function ($query) use($itemType, $type) {
//                        $query->where('type', $itemType[$type]);
//                    }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//            }
//            else {
//                $_cards = Card::where('classObjectId', $card->classObjectId)
//                    ->where('teamId', null)->with(['withItems' => function ($query) use($itemType, $type) {
//                        $query->where('type', $itemType[$type]);
//                    }])->orderBy('id', 'asc')->get();
//            }

            $_itemsNum1 = [];
            foreach ($_cards as $c) {
                if (!is_null($c->itemsNum)) {
                    foreach ($c->itemsNum as $itemNum) {
                        $_item = $c->withItems->where('id', $itemNum)->first();
                        if ($_item) {
                            $_itemsNum1[] = $_item->id;
                        }
                    }
                }
            }

//            $_cards = Card::where('teamId', $card['teamId'])->where('type', $cardType[$type])->with(['withItems' => function ($query) use($itemType, $type) {
//                $query->where('type', $itemType[$type]);
//            }])->get();
//
//            $_itemsNum2 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum2[] = $_item->id;
//                        }
//                    }
//                }
//            }

            $item->load('withCard');
            if ($card->type == $cardType[$type] && $item->type == $itemType[$type]) {
                if ($_card) {
                    if ($_myClass->onTeamAccess) {
                        broadcast(
                            new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
                        )->toOthers();

                        broadcast(
                            new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
                        )->toOthers();
                    }
                    else {
                        broadcast(
                            new SuperEvent(
                                $item->toArray(),
                                $card->classObjectId,
                                $_card->id,
                                'add')
                        )->toOthers();

                        broadcast(
                            new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
                        )->toOthers();
                    }
                }
            }
            else {
                broadcast(
                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
                )->toOthers();
            }
        }
    }
    public function create (Request $request) {
        $card = Card::find($request['id']);
        if (!$card) {
            return ['fail' => true, 'sub' => false, ['id' => ['card not found']]];
        }
        if (!$card->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $request['subId'] : null)) {
            return ['fail' => true, 'sub' => false, ['user' => ['permission denied']]];
        }

        $validator = Validator::make($request->all(), [
            'type' => ['required', 'integer', 'min:0'],//max:3
            'problemAnalysis' => ['required_if:type,1', 'json'],
            'reflectionLog' => ['required_if:type,2', 'json'],
            'brains' => ['required_if:type,3', 'json'],
            'classApply' => ['required_if:type,4', 'json'],
            'teamActivity' => ['required_if:type,5', 'json'],
            'operationResult' => ['required_if:type,8', 'json'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return ['fail' => true, 'sub' => false, $errors];
        }

        if ($request['type'] == 4 && $card->type != 0) { //수업 개설은 수업개실신청 카드에서만 만들 수 있음
            return ['fail' => true, 'sub' => false, ['type' => ['invalid type']]];
        }
        if ($request['type'] == 4 && is_null($card->myPageId)) { //수업 개설은 수업 마이페이지에서만 만들 수 있음
            return ['fail' => true, 'sub' => false, ['type' => ['invalid type']]];
        }
        if ($request['type'] == 4 && Auth::user()->authority != 2) {
            return ['fail' => true, 'sub' => false, ['type' => ['permission denied']]];
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['string', 'nullable'],
            'deadLine' => ['date', 'nullable'],
            'activationDeadline' => ['boolean'],
            'label' => ['integer', 'min:0', 'max:8'],
            'activationLabel' => ['boolean'],
            'checks' => ['json', 'nullable'],
            'images' => ['array'],
            'images.*' => ['image'],
            'files' => ['array'],
            'files.*' => ['file'],
            'activationFile' => ['boolean'],
            'activationCheck' => ['boolean'],
            'activationImage' => ['boolean'],
            'party' => ['json', 'nullable'],
            'activationParty' => ['boolean'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return ['fail' => true, 'sub' => false, $errors];
        }

        if ($request['activationCheck'] == '1') {
            try {
                $checks = (array)json_decode($request['checks']);

                foreach ($checks as $key=>$check) {
                    $checks[$key] = (array)$check;
                }

                $validator = Validator::make($checks, [
                    '*.checked' => ['required', 'boolean'],
                    '*.content' => ['required', 'string']
                ]);

                if ($validator->fails()) {
                    return ['fail' => true, 'sub' => false, ['checks' => ['체크리스트 내용을 전부 입력해 주세요.']]];
                }
            }
            catch(Exception $e) {
                return ['fail' => true, 'sub' => false, ['checks' => ['체크리스트 형식이 맞지 않습니다.']]];
            }
        }

        $itemData = [];
        $itemData['title'] = $request['title'];
        $itemData['userId'] = Auth::id();
        foreach (['content', 'deadLine', 'activationDeadline', 'label', 'activationLabel', 'activationCheck', 'activationImage', 'activationParty', 'type', 'checks', 'party', 'activationFile'] as $column) {
            if ($request->has($column)) {
                $itemData[$column] = $request[$column];
            }
        }

        $subItem = ['fail' => false, 'sub' => true, null];
        switch($request['type']) {
            case 1:
                $subItem = $this->createProblemAnalysis($card, $request['problemAnalysis']);
                break;
            case 2:
                $subItem = $this->createReflectionLog($card, $request['reflectionLog']);
                break;
            case 3:
                $subItem = $this->createBrains($card, $request['brains']);
                break;
            case 4:
                $subItem = $this->createClassApply($card, $request['classApply'], $request['title']);
                break;
            case 5:
                $subItem = $this->createTeamActivity($card, $request['teamActivity']);
                break;
            case 8:
                if ($card->type != '13') {
                    return ['fail' => true, 'sub' => false, ['type' => ['invalid card type']]];
                }
                $subItem = $this->createOperationResult($card, $request['operationResult']);
                break;
            default:
                break;
        }

        if ($subItem['fail']) {
            return $subItem;
        }

        $files = [];
        if ($request['activationFile']) {
            if ($request->hasfile('files')) {
                try {
                    foreach ($request->file('files') as $file) {
                        $fileName = uniqid();
                        Storage::disk('local')->putFileAs('/files', $file, $fileName);
                        $files[] = new ItemFileList([
                            'type' => 'file',
                            'fileName' => $file->getClientOriginalName(),
                            'pathName' => $fileName,
                            'imgUrl' => env('APP_URL').'/files/'.$fileName
                        ]);
                    }
                }
                catch (Exception $e) {
                    return ['fail' => true, 'sub' => false, 'storage' => ['exception']];
                }
            }
        }

        $imageFiles = [];
        if ($request['activationImage']) {
            if ($request->hasfile('images')) {
                try {
                    foreach ($request->file('images') as $image) {
                        $imageName = uniqid();
                        Storage::disk('local')->putFileAs('/images', $image, $imageName);
                        $imageFiles[] = new ItemFileList([
                            'type' => 'image',
                            'fileName' => $image->getClientOriginalName(),
                            'pathName' => $imageName,
                            'imgUrl' => env('APP_URL').'/images/'.$imageName
                        ]);
                    }
                }
                catch (Exception $e) {
                    return ['fail' => true, 'sub' => false, 'storage' => ['exception']];
                }
            }
        }

        $item = new Item($itemData);
        $card->createItem()->save($item);
        $card->itemsNum = is_array($card->itemsNum) ? array_merge([$item->id], $card->itemsNum) : [$item->id];
        $card->save();

        $item->withImages()->saveMany($files);
        $_files = [];
        foreach ($files as $file) {
            $_files[] = $file->id;
        }
        $item->files = $_files;

        $item->withImages()->saveMany($imageFiles);
        $images = [];
        foreach ($imageFiles as $imageFile) {
            $images[] = $imageFile->id;
        }
        $item->images = $images;
        $item->save();
        switch($request['type']) {
            case 1:
                $item->withProblemAnalysis()->save($subItem[0]);
                $item->with_problem_analysis = $subItem[0];
                break;
            case 2:
                $item->withReflectionLog()->save($subItem[0]);
                $item->with_reflection_log = $subItem[0];
                break;
            case 3:
                $item->createBrains()->saveMany($subItem[0]);
                $brain = [];
                foreach ($subItem[0] as $brains) {
                    $brain[] = $brains->id;
                }
                $item->brain = $brain;
                $item->save();
                $item->with_brains = $subItem[0];
                break;
            case 4:
                $item->withClassApply()->save($subItem[0]);
                $item->with_class_apply = $subItem[0];
                $adminLog = AdminLog::create([
                    'userId' => Auth::id(),
                    'userName' => Auth::user()->name,
                    'authority' => Auth::user()->authority,
                    'eventTitle' => $subItem[0]->korName,
                    'eventType' => '40'
                ]);

                broadcast(
                    new AdminLogEvent($adminLog->toArray())
                );
                break;
            case 5:
                $item->withTeamActivity()->save($subItem[0]);
                $item->with_team_activity = $subItem[0];
                break;
            case 8:
                $item->withOperationResult()->save($subItem[0]);
                $item->with_operation_result = $subItem[0];
                break;
            default:
                break;
        }

        $item->with_images = $imageFiles;
        $item->with_files = $files;

        if (is_null($card->myPageId)) {
            switch($request['type']) {
                case 1:
                    $this->broadcast($card, $item, 'problemAnalysis');
                    break;
                case 2:
                    $this->broadcast($card, $item, 'reflectionLog');
                    break;
                case 4:
                    break;
                case 5:
                    $this->broadcast($card, $item, 'teamActivity');
                    break;
                case 0:case 3:
                    broadcast(
                        new ItemEvent(['item' => $item, 'cardId' => $card->id], $card->teamId ?? $card->classObjectId,
                            $card->teamId ? 'MYTEAM' : 'MYCLASS',
                            'add')
                    )->toOthers();
                    break;
                case 8:default:
                    break;
            }
        }

        if (!is_null($card->classObjectId) && in_array($item->type, [1, 2, 5])) {
            $user = Auth::user();
            $classObject = ClassObject::find($card->classObjectId);
            $myClass = MyClass::where('classObjectId', $classObject->id)->first();
            $classObject->load('class_apply');

            $team = Team::find($card->teamId);
            $actLog = ActLog::create([
                'userId' => $user->id,
                'userName' => $user->name,
                'classObjectId' => $classObject->id,
                'classObjectName' => $classObject->class_apply ? $classObject->class_apply->korName : $classObject->gwamokNm,
                'teamId' => $team ? $team->id : null,
                'teamName' => $team ? $team->name : null,
                'authority' => $user->authority,
                'eventType' => $item->type * 10,
                'eventState' => 10,
                'eventTitle' => $item->title,
            ]);

            broadcast(
                new ActLogEvent($actLog->toArray(), $myClass->userId)
            );

            if (!is_null($card->teamId) && in_array($item->type, [1, 2, 5])) {
                $teamMembers = $team->teamMembers();

                foreach ($teamMembers as $member) {
                    if (is_null($member->userId)) {
                        continue;
                    }

                    broadcast(
                        new ActLogEvent($actLog->toArray(), $member->userId)
                    );
                }
            }
            else {
                $classMembers = $myClass->members();

                foreach ($classMembers as $member) {
                    if (is_null($member->userId)) {
                        continue;
                    }

                    broadcast(
                        new ActLogEvent($actLog->toArray(), $member->userId)
                    );
                }
            }
        }

        return [$item];
        ///////////////////////////////////////////////////////////
//        $card = Card::find($request['id']);
//        if (!$card) {
//            return ['fail' => 'card'];
//        }
//        if (!$card->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $request['subId'] : null)) {
//            return ['fail' => 'permission denied'];
//        }
//
//        $validator = Validator::make($request->all(), [
//            'problemAnalysis' => ['required', 'json'],
//            'reflectionLog' => ['required', 'json'],
//            'brains' => ['required', 'json'],
//            'classApply' => ['required', 'json'],
//            'teamActivity' => ['required', 'json'],
//            'operationResult' => ['required', 'json'],
//            'type' => ['required', 'integer', 'min:0'],//max:3
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('type')) {
//                return ['fail' => 'invalid parameter: type'];
//            }
//            else if ($request['type'] == 1) {
//                if ($errors->has('problemAnalysis')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//            }
//            else if ($request['type'] == 2) {
//                if ($errors->has('reflectionLog')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//            }
//            else if ($request['type'] == 3) {
//                if ($errors->has('brains')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//            }
//            else if ($request['type'] == 4) {
//                if ($errors->has('classApply')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//            }
//            else if ($request['type'] == 5) {
//                if ($errors->has('teamActivity')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//            }
//            else if ($request['type'] == 8) {
//                if ($errors->has('operationResult')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//            }
//        }
//
//        if ($request['type'] == 4 && $card->type != 0) {
//            return ['fail' => 'invalid parameter'];
//        }
//
//        $validator = Validator::make($request->all(), [
//            'title' => ['required', 'string'],
//            'content' => ['string', 'nullable'],
//            'deadLine' => ['date', 'nullable'],
//            'activationDeadline' => ['boolean'],
//            'label' => ['integer', 'min:0', 'max:8'],
//            'activationLabel' => ['boolean'],
//            'checks' => ['json', 'nullable'],
//            'activationCheck' => ['boolean'],
//            'activationImage' => ['boolean'],
//            'party' => ['json', 'nullable'],
//            'activationParty' => ['boolean'],
//            'type' => ['required', 'integer', 'min:0'],//max:3
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('title')) {
//                return ['fail' => 'title'];
//            }
//            else if ($errors->has('content')) {
//                return ['fail' => 'validate error: content'];
//            }
//            else if ($errors->has('deadLine')) {
//                return ['fail' => 'deadLine'];
//            }
//            else if ($errors->has('activationDeadline')) {
//                return ['fail' => 'validate error: activationDeadline'];
//            }
//            else if ($errors->has('label')) {
//                return ['fail' => 'validate error: label'];
//            }
//            else if ($errors->has('activationLabel')) {
//                return ['fail' => 'validate error: activationLabel'];
//            }
//            else if ($errors->has('checks')) {
//                return ['fail' => 'validate error: checks'];
//            }
//            else if ($errors->has('activationCheck')) {
//                return ['fail' => 'validate error: activationCheck'];
//            }
//            else if ($errors->has('activationImage')) {
//                return ['fail' => 'validate error: activationImage'];
//            }
//            else if ($errors->has('party')) {
//                return ['fail' => 'validate error: party'];
//            }
//            else if ($errors->has('activationParty')) {
//                return ['fail' => 'validate error: activationParty'];
//            }
//            else if ($errors->has('type')) {
//                return ['fail' => 'validate error: type'];
//            }
//        }
//
//        //수업 개설은 마이페이지 에서만 만들수 있음
//        if ($request['type'] == 4 && is_null($card->myPageId)) {
//            return ['fail' => 'invalid access'];
//        }
//
//        $itemData = [];
//        $itemData['cardId'] = $card->id;
//        $itemData['title'] = $request['title'];
//        $itemData['userId'] = Auth::id();
//        if ($request->has('content')) {
//            $itemData['content'] = $request['content'];
//        }
//        if ($request->has('deadLine')) {
//            $itemData['deadLine'] = $request['deadLine'];
//        }
//        if ($request->has('activationDeadline')) {
//            $itemData['activationDeadline'] = $request['activationDeadline'];
//        }
//        if ($request->has('label')) {
//            $itemData['label'] = $request['label'];
//        }
//        if ($request->has('activationLabel')) {
//            $itemData['activationLabel'] = $request['activationLabel'];
//        }
//        if ($request->has('activationCheck')) {
//            $itemData['activationCheck'] = $request['activationCheck'];
//        }
//        if ($request->has('activationImage')) {
//            $itemData['activationImage'] = $request['activationImage'];
//        }
//        if ($request->has('activationParty')) {
//            $itemData['activationParty'] = $request['activationParty'];
//        }
//        if ($request->has('type')) {
//            $itemData['type'] = $request['type'];
//        }
//        if ($request->has('checks')) {
//            $itemData['checks'] = $request['checks'];
//        }
//        if ($request->has('party')) {
//            $itemData['party'] = $request['party'];
//        }
//
//        $item = Item::create($itemData);
//        if (!$item) {
//            return ['fail' => 'create error'];
//        }
//
//        if ($request->hasfile('images')) {
//            $imageNames = [];
//            foreach ($request->file('images') as $image) {
//                $imageName = uniqid();
////        $request->image->move(public_path('uploads').'/'.$myPage->id, $imageName);
//                Storage::disk('local')->putFileAs('/images', $image, $imageName);
//                $imageFile = ItemFileList::create([
//                    'itemId' => $item->id,
//                    'type' => 'image',
//                    'fileName' => $image->getClientOriginalName(),
//                    'pathName' => $imageName,
////                    'imgUrl' => env('APP_URL').':'.env('PORT').'/images/'.$imageName
//                    'imgUrl' => env('APP_URL').'/images/'.$imageName
//                ]);
//                $itemImages = $item->images;
//                if (!$itemImages) {
//                    $itemImages = [];
//                }
//                $itemImages[] = $imageFile->id;
//                $item->images = $itemImages;
//            }
//
//            $item->save();
//        }
//
//        if (is_null($card->itemsNum)) {
//            $card->itemsNum = [];
//        }
//        $itemsNum = $card->itemsNum;
//        array_unshift($itemsNum, $item->id);
//        $card->itemsNum = $itemsNum;
//        $card->save();
//
//        $item->with_images = $item->itemFileList();
//
//        if ($request['type'] == 1) {
//            return $this->createWithProblem($request, $card, $item);
//        }
//        else if ($request['type'] == 2) {
//            return $this->createWithReflection($request, $card, $item);
//        }
//        else if ($request['type'] == 3) {
//            return $this->createWithBrains($request, $card, $item);
//        }
//        else if ($request['type'] == 4) {
//            return $this->createWithClassApply($request, $card, $item);
//        }
//        else if ($request['type'] == 5) {
//            return $this->createWithTeamActivity($request, $card, $item);
//        }
//        else if ($request['type'] == 8) {
//            return $this->createWithOperationResult($request, $card, $item);
//        }
//        else if ($request['type'] == 0 && is_null($item->card()->myPageId)) {
//            broadcast(
//                new ItemEvent(['item' => $item, 'cardId' => $item->card()->id], $item->card()->teamId ?? $item->card()->classObjectId,
//                    $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
//                    'add')
//            )->toOthers();
//        }
//
//        return [$item];
    }
//    private function createWithProblem (Request $request, $card, $item) {
//        try {
//            $problemAnalysis = json_decode($request['problemAnalysis']);
//
//            ProblemAnalysis::create([
//                'itemId' => $item->id,
//                'studentId' => $problemAnalysis->studentId,
//                'name' => Auth::user()->name,
//                'content1' => $problemAnalysis->content1,
//                'content2' => $problemAnalysis->content2,
//                'content3' => $problemAnalysis->content3,
//                'content4' => $problemAnalysis->content4,
//            ]);
//        }
//        catch (Exception $e) {
//            $item->delete();
//
//            return ['fail' => 'exception'];
//        }
//
//        if (!is_null($card->classObjectId) && is_null($card->teamId)) {
//            if ($card->type != 10) {
//                $_myClass = $card->myClass();
//                $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 10)->first();
//
//                if ($_myClass->onTeamAccess) {
//                    $_cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->with(['withItems' => function ($query) {
//                            $query->where('type', 1);
//                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//                }
//                else {
//                    $_cards = Card::where('classObjectId', $card->classObjectId)
//                        ->where('teamId', null)->with(['withItems' => function ($query) {
//                            $query->where('type', 1);
//                        }])->orderBy('id', 'asc')->get();
//                }
//
//                $_itemsNum = [];
//                foreach ($_cards as $c) {
//                    if (!is_null($c->itemsNum)) {
//                        foreach ($c->itemsNum as $itemNum) {
//                            $_item = $c->withItems->where('id', $itemNum)->first();
//                            if ($_item) {
//                                $_itemsNum[] = $_item->id;
//                            }
//                        }
//                    }
//                }
//
////                $data['cards'][$key]['itemsNum'] = $_itemsNum;
////                $data['cards'][$key]['with_items'] = $_withItems;
//
//                //this.team -> this.team
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum, $card->classObjectId, 'create', 'MYCLASS')
//                    );
//                }
//
//                broadcast(
//                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->classObjectId, 'MYCLASS', 'add')
//                )->toOthers();
//            }
//        }
//        else if (!is_null($card->classObjectId) && !is_null($card->teamId)) {
//            $_myClass = $card->myClass();
//            $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 10)->first();
//            $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', 10)->get();
//
//            if ($_myClass->onTeamAccess) {
//                $_cards = Card::where('classObjectId', $card['classObjectId'])
//                    ->with(['withItems' => function ($query) {
//                        $query->where('type', 1);
//                    }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//            }
//            else {
//                $_cards = Card::where('classObjectId', $card->classObjectId)
//                    ->where('teamId', null)->with(['withItems' => function ($query) {
//                        $query->where('type', 1);
//                    }])->orderBy('id', 'asc')->get();
//            }
//
//            $_itemsNum1 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum1[] = $_item->id;
//                        }
//                    }
//                }
//            }
//
//            $_cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
//                    $query->where('type', 1);
//                }])->get();
//
//            $_itemsNum2 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum2[] = $_item->id;
//                        }
//                    }
//                }
//            }
//
//
////            $_items = DB::table('cards')
////                ->where('classObjectId', '=', $_myClass->classObjectId)
////                ->where('teamId','!=', null)
////                ->join('items', 'cards.id', '=', 'items.cardId')
////                ->where('items.type','=', 1)
////                ->select('cards.teamId', 'items.id', 'items.type', 'items.title')
////                ->orderBy('cards.teamId', 'asc')
////                ->orderBy('items.id', 'desc')
////                ->get();
////
////            $_itemsNum = [];
////            $_brodItemsNum = [];
////            foreach($_items as $_c) {
////                if($card->teamId === $_c->teamId){
////                    $_itemsNum[] = $_c->id;
////                }
////
////                $_brodItemsNum[] = $_c->id;
////            }
////            $_itemsNum = [];
////            foreach($_cards as $_c) {
////                if (!is_null($_c->itemsNum)) {
////                    foreach ($_c->itemsNum as $itemNum) {
////                        $_itemsNum[] = $itemNum;
////                    }
////                }
////            }
//
//            if ($card->type != 10) {
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
//                    )->toOthers();
//
//                        //this.team -> this.team
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
//                    );
//                }
//                //this.team -> others.myclass
//
//
//                broadcast(
//                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
//                )->toOthers();
//            }
//            else {
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
//                    )->toOthers();
//
//                    //this.team -> this.team
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
//                    );
//                }
//            }
//        }
//
//        $item->with_problem_analysis = $item->withProblemAnalysis()->first();
//
//        return [$item];
//    }
//    private function createWithReflection (Request $request, $card, $item) {
//        try {
//            $reflectionLog = json_decode($request['reflectionLog']);
//
//            ReflectionLog::create([
//                'itemId' => $item->id,
//                'studentId' => $reflectionLog->studentId,
//                'name' => Auth::user()->name,
//                'content1' => $reflectionLog->content1,
//                'content2' => $reflectionLog->content2,
//                'content3' => $reflectionLog->content3,
//                'content4' => $reflectionLog->content4,
//                'content5' => $reflectionLog->content5,
//                'content6' => $reflectionLog->content6,
//                'content7' => $reflectionLog->content7,
//            ]);
//        }
//        catch (Exception $e) {
//            $item->delete();
//
//            return ['fail' => 'exception'];
//        }
//
//        if (!is_null($card->classObjectId) && is_null($card->teamId)) {
//            if ($card->type != 7) {
//                $_myClass = $card->myClass();
//                $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 7)->first();
//
//                if ($_myClass->onTeamAccess) {
//                    $_cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->with(['withItems' => function ($query) {
//                            $query->where('type', 2);
//                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//                }
//                else {
//                    $_cards = Card::where('classObjectId', $card->classObjectId)
//                        ->where('teamId', null)->with(['withItems' => function ($query) {
//                            $query->where('type', 2);
//                        }])->orderBy('id', 'asc')->get();
//                }
//
//                $_itemsNum = [];
//                foreach ($_cards as $c) {
//                    if (!is_null($c->itemsNum)) {
//                        foreach ($c->itemsNum as $itemNum) {
//                            $_item = $c->withItems->where('id', $itemNum)->first();
//                            if ($_item) {
//                                $_itemsNum[] = $_item->id;
//                            }
//                        }
//                    }
//                }
//
//                //this.team -> this.team
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum, $card->classObjectId, 'create', 'MYCLASS')
//                    );
//                }
//
//                broadcast(
//                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->classObjectId, 'MYCLASS', 'add')
//                )->toOthers();
//            }
//        }
//        else if (!is_null($card->classObjectId) && !is_null($card->teamId)) {
//            $_myClass = $card->myClass();
//            $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 7)->first();
//            $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', 7)->get();
//
//            if ($_myClass->onTeamAccess) {
//                $_cards = Card::where('classObjectId', $card['classObjectId'])
//                    ->with(['withItems' => function ($query) {
//                        $query->where('type', 2);
//                    }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//            }
//            else {
//                $_cards = Card::where('classObjectId', $card->classObjectId)
//                    ->where('teamId', null)->with(['withItems' => function ($query) {
//                        $query->where('type', 2);
//                    }])->orderBy('id', 'asc')->get();
//            }
//
//            $_itemsNum1 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum1[] = $_item->id;
//                        }
//                    }
//                }
//            }
//
//            $_cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
//                $query->where('type', 2);
//            }])->get();
//
//            $_itemsNum2 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum2[] = $_item->id;
//                        }
//                    }
//                }
//            }
//
//            if ($card->type != 7) {
//                //this.team -> others.myclass
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
//                    )->toOthers();
//
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
//                    );
//                }
//
//                broadcast(
//                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
//                )->toOthers();
//            }
//            else {
//                //this.team -> others.myclass
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
//                    )->toOthers();
//
//                    //this.team -> this.team
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
//                    );
//                }
//            }
//        }
//
//        $item->with_reflection_log = $item->withReflectionLog()->first();
//
//        return [$item];
//    }
//    private function createWithBrains (Request $request, $card, $item) {
//        try {
//            $item->refresh();
//
//            $brains = (array)json_decode($request['brains']);
//
//            $validator = Validator::make($brains, [
//                '*->content' => ['required', 'string'],
//                '*->color' => ['required', 'integer', 'min:1', 'max:8'],
//            ]);
//
//            if ($validator->fails()) {
//                return ['fail' => $validator->errors()];//'invalid parameter'];
//            }
//
//            foreach ($brains as $b) {
//                $brain = Brain::create([
//                    'userId' => Auth::id(),
//                    'itemId' => $item->id,
//                    'content' => $b->content,
//                    'color' => $b->color,
//                ]);
//
//                $brainsNum = $item->brain ?? [];
//                array_push($brainsNum, $brain->id);
//                $item->brain = $brainsNum;
//
//                $item->save();
//            }
//        }
//        catch (Exception $e) {
//            $item->remove();
//
//            return ['fail' => 'exception'];
//        }
//
//        $item->refresh();
//
//        $item->with_brains = $item->withBrains()->first();
//        $item->with_images = $item->itemFileList();
//
//        broadcast(
//            new ItemEvent(['item' => $item, 'cardId' => $card->id], $card->teamId ?? $card->classObjectId,
//                $card->teamId ? 'MYTEAM' : 'MYCLASS',
//                'add')
//        )->toOthers();
//
//        return [$item];
//    }
//    private function createWithClassApply (Request $request, $card, $item) {
//        if ($card->type != 0) {
//            $item->remove();
//            return ['fail' => 'invalid parameter'];
//        }
//        if (Auth::user()->authority != 2) {
//            $item->remove();
//            return ['fail' => 'permission denied'];
//        }
//
//        try {
//            $classApply = (array)json_decode($request['classApply']);
//
//            $validator = Validator::make($classApply, [
//                'mode' => ['required', 'integer', 'min:1', 'max:2'], //1:기존 과목, 2: 이전 과목
//                'type' => ['required', 'integer', 'min:1', 'max:4'],
//                'grade' => ['required', 'integer', 'min:1', 'max:4'],
//                'size1' => ['required', 'integer', 'min:1', 'max:3'],
//                'size2' => ['required_if:size1,3', 'exclude_unless:size1,3', 'integer', 'min:31'], ////////////
//                'proSize1' => ['required', 'integer', 'min:1', 'max:3'],
//                'proSize2' => ['required_if:proSize1,2', 'exclude_unless:proSize1,2', 'integer', 'min:1'], /////////////
//                'proSize3' => ['required_if:proSize1,3', 'exclude_unless:proSize1,3', 'integer', 'min:1'], /////////////
//                'department' => ['required', 'string'],
//                'special' => ['required', 'integer', 'min:1', 'max:4'],
//                'korName' => ['required', 'string'],
//                'engName' => ['required', 'string'],
//                'gradesPoint' => ['required', 'integer', 'min:0'],
//                'lecturePoint' => ['required', 'integer', 'min:0'],
//                'exercisePoint' => ['required', 'integer', 'min:0'],
//                'description' => ['required', 'string'],
//                'meca' => ['required', 'integer', 'min:1', 'max:4'],
//                'agency' => ['required', 'string'],
//                'expert' => ['required', 'string'],
//                'role1' => ['required', 'integer', 'min:1', 'max:4'],
//                'role2' => ['required_if:role1,4', 'exclude_unless:role1,4', 'string'], //////////////////////
//                'expected1' => ['required', 'boolean'],
//                'expected2' => ['required', 'boolean'],
//                'expected3' => ['required', 'boolean'],
//                'expected4' => ['required', 'boolean'],
//                'expected5' => ['required', 'boolean'],
//                'expected6' => ['required', 'boolean'],
//                'expected7' => ['required', 'boolean'],
//                'expected8' => ['required_if:expected7,1', 'exclude_unless:expected7,1', 'string'], /////////////////
//                'aplName' => ['required', 'string'],
//                'aplSign' => [],
//                'aplOrg' => ['required', 'string'],
//                'aplTel' => ['required', 'string'],
//                'aplPhone' => ['required', 'string'],
//                'aplEmail' => ['required', 'email'],
//                'agree1' => ['required', 'boolean', 'in:1,true'],
//                'duration' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conName' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conPer' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conDescription' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'agree2' => ['required_if:mode,1', 'exclude_unless:mode,1', 'boolean', 'in:1,true'],
//                'agree3' => ['required_if:mode,1', 'exclude_unless:mode,1', 'boolean', 'in:1,true'],
//                'basic1' => ['required', 'string'],
//                'basic2' => ['required', 'string'],
//                'basic3' => ['required', 'string'],
//                'basic4' => ['required', 'string'],
//                'basic5' => ['required', 'string'],
//                'basic6' => ['required', 'string'],
//                'basicPlan' => ['required', 'string'],
//                'sceContent' => ['required', 'string'],
//                'sceGoal' => ['required', 'string'],
//                'sceTitle' => ['required', 'string'],
//                'sceRole' => ['required', 'string'],
//                'sceDetail' => ['required', 'string'],
//                'planDetail' => ['required', 'array'],
//                'planDetail[].*.content' => ['nullable', 'string'],
//                'planDetail[].*.level' => ['nullable', 'string'],
//                'planDetail[].*.stuContent' => ['nullable', 'string'],
//                'planDetail[].*.subject' => ['nullable', 'string'],
//                'planDetail[].*.method' => ['integer', 'min:1', 'max:3'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                $item->remove();
//
//                return $errors;
//            }
//
//            $_classApply = ClassApply::create([
//                'itemId' => $item->id,
////                'state' => 'wait', //default: wait
//                'mode' => $classApply['mode'],
//                'type' => $classApply['type'],
//                'grade' => $classApply['grade'],
//                'size1' => $classApply['size1'],
//                'size2' => (int)$classApply['size1'] == 3 ? $classApply['size2'] : null,
//                'proSize1' => $classApply['proSize1'],
//                'proSize2' => (int)$classApply['proSize1'] == 2 ? $classApply['proSize2'] : null,
//                'proSize3' => (int)$classApply['proSize1'] == 3 ? $classApply['proSize3'] : null,
//                'department' => $classApply['department'],
//                'special' => $classApply['special'],
//                'korName' => $classApply['korName'],
//                'engName' => $classApply['engName'],
//                'gradesPoint' => $classApply['gradesPoint'],
//                'lecturePoint' => $classApply['lecturePoint'],
//                'exercisePoint' => $classApply['exercisePoint'],
//                'description' => $classApply['description'],
//                'meca' => $classApply['meca'],
//                'agency' => $classApply['agency'],
//                'expert' => $classApply['expert'],
//                'role1' => $classApply['role1'],
//                'role2' => (int)$classApply['role1'] == 4 ? $classApply['role2'] : null,
//                'expected1' => $classApply['expected1'],
//                'expected2' => $classApply['expected2'],
//                'expected3' => $classApply['expected3'],
//                'expected4' => $classApply['expected4'],
//                'expected5' => $classApply['expected5'],
//                'expected6' => $classApply['expected6'],
//                'expected7' => $classApply['expected7'],
//                'expected8' => (int)$classApply['expected7'] > 0 ? $classApply['expected8'] : null,
//                'aplName' => $classApply['aplName'],
//                'aplSign' => $classApply['aplSign'],
//                'aplOrg' => $classApply['aplOrg'],
//                'aplTel' => $classApply['aplTel'],
//                'aplPhone' => $classApply['aplPhone'],
//                'aplEmail' => $classApply['aplEmail'],
//                'agree1' => $classApply['agree1'],
//                'duration' => (int)$classApply['mode'] == 1 ? $classApply['duration'] : null,
//                'conName' => (int)$classApply['mode'] == 1 ? $classApply['conName'] : null,
//                'conPer' => (int)$classApply['mode'] == 1 ? $classApply['conPer'] : null,
//                'conDescription' => (int)$classApply['mode'] == 1 ? $classApply['conDescription'] : null,
//                'agree2' => (int)$classApply['mode'] == 1 ? $classApply['agree2'] : null,
//                'agree3' => (int)$classApply['mode'] == 1 ? $classApply['agree3'] : null,
//                'basic1' => $classApply['basic1'],
//                'basic2' => $classApply['basic2'],
//                'basic3' => $classApply['basic3'],
//                'basic4' => $classApply['basic4'],
//                'basic5' => $classApply['basic5'],
//                'basic6' => $classApply['basic6'],
//                'basicPlan' => $classApply['basicPlan'],
//                'sceContent' => $classApply['sceContent'],
//                'sceGoal' => $classApply['sceGoal'],
//                'sceTitle' => $classApply['sceTitle'],
//                'sceRole' => $classApply['sceRole'],
//                'sceDetail' => $classApply['sceDetail'],
//                'planDetail' => $classApply['planDetail'],
//            ]);
//        }
//        catch (Exception $e) {
//            $item->remove();
//
//            return ['fail' => 'exception'];
//        }
//
//        $item->with_class_apply = $item->withClassApply()->first();
//
//        return [$item];
//    }
//    private function createWithTeamActivity (Request $request, $card, $item) {
//        $validator = Validator::make($request->all(), [
//            'teamActivity' => ['required', 'json'],
//        ]);
//
//        if ($validator->fails()) {
//            return ['fail' => 'invalid parameter'];
//        }
//
//        $teamActivity = (array)json_decode($request['teamActivity']);
//
//        $validator = Validator::make($teamActivity, [
//            'dateTime' => ['required', 'string'],
//            'problemSolvingProcess' => ['required', 'string'],
//            'attendees' => ['required', 'string'],
//            'mainActivities' => ['required', 'string'],
//            'task1' => ['required', 'string'],
//            'task2' => ['required', 'string'],
//            'discuss1' => ['required', 'string'],
//            'schedule1' => ['required', 'string'],
//            'schedule2' => ['required', 'string'],
//            'schedule3' => ['required', 'string'],
//            'schedule4' => ['required', 'string'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            $item->delete();
//
//            return $errors;
//        }
//
//        $_teamActivity = TeamActivity::create([
//            'itemId' => $item->id,
//            'dateTime' => $teamActivity['dateTime'],
//            'problemSolvingProcess' => $teamActivity['problemSolvingProcess'],
//            'attendees' => $teamActivity['attendees'],
//            'mainActivities' => $teamActivity['mainActivities'],
//            'task1' => $teamActivity['task1'],
//            'task2' => $teamActivity['task2'],
//            'discuss1' => $teamActivity['discuss1'],
//            'schedule1' => $teamActivity['schedule1'],
//            'schedule2' => $teamActivity['schedule2'],
//            'schedule3' => $teamActivity['schedule3'],
//            'schedule4' => $teamActivity['schedule4'],
//        ]);
//
//        if (!is_null($card->classObjectId) && is_null($card->teamId)) {
//            if ($card->type != 9) {
//                $_myClass = $card->myClass();
//                $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 9)->first();
//
//                if ($_myClass->onTeamAccess) {
//                    $_cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->with(['withItems' => function ($query) {
//                            $query->where('type', 5);
//                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//                }
//                else {
//                    $_cards = Card::where('classObjectId', $card->classObjectId)
//                        ->where('teamId', null)->with(['withItems' => function ($query) {
//                            $query->where('type', 5);
//                        }])->orderBy('id', 'asc')->get();
//                }
//
//                $_itemsNum = [];
//                foreach ($_cards as $c) {
//                    if (!is_null($c->itemsNum)) {
//                        foreach ($c->itemsNum as $itemNum) {
//                            $_item = $c->withItems->where('id', $itemNum)->first();
//                            if ($_item) {
//                                $_itemsNum[] = $_item->id;
//                            }
//                        }
//                    }
//                }
//
//                //this.team -> this.team
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum, $card->classObjectId, 'create', 'MYCLASS')
//                    );
//                }
//
//                broadcast(
//                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->classObjectId, 'MYCLASS', 'add')
//                )->toOthers();
//            }
//        }
//        else if (!is_null($card->classObjectId) && !is_null($card->teamId)) {
//            $_myClass = $card->myClass();
//            $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 9)->first();
//            $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', 9)->get();
//
//            if ($_myClass->onTeamAccess) {
//                $_cards = Card::where('classObjectId', $card['classObjectId'])
//                    ->with(['withItems' => function ($query) {
//                        $query->where('type', 5);
//                    }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
//            }
//            else {
//                $_cards = Card::where('classObjectId', $card->classObjectId)
//                    ->where('teamId', null)->with(['withItems' => function ($query) {
//                        $query->where('type', 5);
//                    }])->orderBy('id', 'asc')->get();
//            }
//
//            $_itemsNum1 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum1[] = $_item->id;
//                        }
//                    }
//                }
//            }
//
//            $_cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
//                $query->where('type', 5);
//            }])->get();
//
//            $_itemsNum2 = [];
//            foreach ($_cards as $c) {
//                if (!is_null($c->itemsNum)) {
//                    foreach ($c->itemsNum as $itemNum) {
//                        $_item = $c->withItems->where('id', $itemNum)->first();
//                        if ($_item) {
//                            $_itemsNum2[] = $_item->id;
//                        }
//                    }
//                }
//            }
//
//            if ($card->type != 9) {
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
//                    )->toOthers();
//
//                    //this.team -> this.team
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
//                    );
//                }
//                //this.team -> others.myclass
//
//
//                broadcast(
//                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
//                )->toOthers();
//            }
//            else {
//                if ($_card) {
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
//                    )->toOthers();
//
//                    //this.team -> this.team
//                    broadcast(
//                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
//                    );
//                }
//            }
//        }
//
//        $item->with_team_activity = $item->withTeamActivity()->first();
//
//        return [$item];
//    }
//    private function createWithOperationResult (Request $request, $card, $item) {
//        try {
//            $operationResult = (array)json_decode($request['operationResult']);
//
//            $validator = Validator::make($operationResult, [
//                'semester' => ['required', 'string'],
//                'college' => ['required', 'string'],
//                'lectureName' => ['required', 'string'],
//                'grade' => ['required', 'string'],
//                'division' => ['required', 'string'],
//                'grades' => ['required', 'string'],
//                'professor' => ['required', 'string'],
//                'size' => ['required', 'string'],
//                'icpblType' => ['required', 'string'],
//                'summary' => ['required', 'string'],
//                'classGoal' => ['required', 'string'],
//                'method' => ['required', 'string'],
//                'basicPlan' => ['required', 'string'],
//                'title' => ['required', 'string'],
//                'role' => ['required', 'string'],
//                'scenario' => ['required', 'string'],
//                'process' => ['required', 'array'],
//                'outputType1' => ['required', 'string'],
//                'outputType2' => ['required', 'string'],
//                'outputType3' => ['required', 'string'],
//                'outputType4' => ['required', 'string'],
//                'outputType5' => ['required', 'string'],
//                'outputType6' => ['required', 'string'],
//                'outputType7' => ['required', 'string'],
//                'outputType8' => ['required_if:outputType7,1', 'exclude_unless:outputType7,1', 'string'],
//                'finalOutput' => ['required', 'string'],
//                'mainStudent' => ['required', 'string'],
//                'sTitle' => ['required', 'string'],
//                'sName' => ['required', 'string'],
//                'sRole1' => ['required', 'string'],
//                'sRole2' => ['required_if:sRole,5', 'exclude_unless:sRole1,5', 'string'],
//                'sLink' => ['required', 'string'],
//                'sFeedback' => ['required', 'string'],
//                'sOpinion' => ['required', 'string'],
//                'pr1' => ['required', 'string'],
//                'pr2' => ['required', 'string'],
//                'pr3' => ['required', 'string'],
//                'pr4' => ['required', 'string'],
//                'pr5' => ['required', 'string'],
//                'pr6' => ['required', 'string'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                $item->remove();
//
//                return $errors;
//            }
//
//            operation::create([
//                'itemId' => $item->id,
//                'semester' => $operationResult['semester'],
//                'college' => $operationResult['college'],
//                'lectureName' => $operationResult['lectureName'],
//                'grade' => $operationResult['grade'],
//                'division' => $operationResult['division'],
//                'grades' => $operationResult['grades'],
//                'professor' => $operationResult['professor'],
//                'size' => $operationResult['size'],
//                'icpblType' => $operationResult['icpblType'],
//                'summary' => $operationResult['summary'],
//                'classGoal' => $operationResult['classGoal'],
//                'method' => $operationResult['method'],
//                'basicPlan' => $operationResult['basicPlan'],
//                'title' => $operationResult['title'],
//                'role' => $operationResult['role'],
//                'scenario' => $operationResult['scenario'],
//                'process' => $operationResult['process'],
//                'outputType1' => $operationResult['outputType1'],
//                'outputType2' => $operationResult['outputType2'],
//                'outputType3' => $operationResult['outputType3'],
//                'outputType4' => $operationResult['outputType4'],
//                'outputType5' => $operationResult['outputType5'],
//                'outputType6' => $operationResult['outputType6'],
//                'outputType7' => $operationResult['outputType7'],
//                'outputType8' => $operationResult['outputType8'],
//                'finalOutput' => $operationResult['finalOutput'],
//                'mainStudent' => $operationResult['mainStudent'],
//                'sTitle' => $operationResult['sTitle'],
//                'sName' => $operationResult['sName'],
//                'sRole1' => $operationResult['sRole1'],
//                'sRole2' => $operationResult['sRole2'],
//                'sLink' => $operationResult['sLink'],
//                'sFeedback' => $operationResult['sFeedback'],
//                'sOpinion' => $operationResult['sOpinion'],
//                'pr1' => $operationResult['pr1'],
//                'pr2' => $operationResult['pr2'],
//                'pr3' => $operationResult['pr3'],
//                'pr4' => $operationResult['pr4'],
//                'pr5' => $operationResult['pr5'],
//                'pr6' => $operationResult['pr6'],
//            ]);
//        }
//        catch (Exception $e) {
//            $item->remove();
//
//            return ['fail' => 'exception'];
//        }
//
////        if (!is_null($card->classObjectId) && is_null($card->teamId)) {
////            if ($card->type != 10) {
////                $_myClass = $card->myClass();
////                $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 10)->first();
////
////                if ($_myClass->onTeamAccess) {
////                    $_cards = Card::where('classObjectId', $card['classObjectId'])
////                        ->with(['withItems' => function ($query) {
////                            $query->where('type', 1);
////                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
////                }
////                else {
////                    $_cards = Card::where('classObjectId', $card->classObjectId)
////                        ->where('teamId', null)->with(['withItems' => function ($query) {
////                            $query->where('type', 1);
////                        }])->orderBy('id', 'asc')->get();
////                }
////
////                $_itemsNum = [];
////                foreach ($_cards as $c) {
////                    if (!is_null($c->itemsNum)) {
////                        foreach ($c->itemsNum as $itemNum) {
////                            $_item = $c->withItems->where('id', $itemNum)->first();
////                            if ($_item) {
////                                $_itemsNum[] = $_item->id;
////                            }
////                        }
////                    }
////                }
////
//////                $data['cards'][$key]['itemsNum'] = $_itemsNum;
//////                $data['cards'][$key]['with_items'] = $_withItems;
////
////                //this.team -> this.team
////                if ($_card) {
////                    broadcast(
////                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum, $card->classObjectId, 'create', 'MYCLASS')
////                    );
////                }
////
////                broadcast(
////                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->classObjectId, 'MYCLASS', 'add')
////                )->toOthers();
////            }
////        }
////        else if (!is_null($card->classObjectId) && !is_null($card->teamId)) {
////            $_myClass = $card->myClass();
////            $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 10)->first();
////            $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', 10)->get();
////
////            if ($_myClass->onTeamAccess) {
////                $_cards = Card::where('classObjectId', $card['classObjectId'])
////                    ->with(['withItems' => function ($query) {
////                        $query->where('type', 1);
////                    }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
////            }
////            else {
////                $_cards = Card::where('classObjectId', $card->classObjectId)
////                    ->where('teamId', null)->with(['withItems' => function ($query) {
////                        $query->where('type', 1);
////                    }])->orderBy('id', 'asc')->get();
////            }
////
////            $_itemsNum1 = [];
////            foreach ($_cards as $c) {
////                if (!is_null($c->itemsNum)) {
////                    foreach ($c->itemsNum as $itemNum) {
////                        $_item = $c->withItems->where('id', $itemNum)->first();
////                        if ($_item) {
////                            $_itemsNum1[] = $_item->id;
////                        }
////                    }
////                }
////            }
////
////            $_cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
////                $query->where('type', 1);
////            }])->get();
////
////            $_itemsNum2 = [];
////            foreach ($_cards as $c) {
////                if (!is_null($c->itemsNum)) {
////                    foreach ($c->itemsNum as $itemNum) {
////                        $_item = $c->withItems->where('id', $itemNum)->first();
////                        if ($_item) {
////                            $_itemsNum2[] = $_item->id;
////                        }
////                    }
////                }
////            }
////
////
//////            $_items = DB::table('cards')
//////                ->where('classObjectId', '=', $_myClass->classObjectId)
//////                ->where('teamId','!=', null)
//////                ->join('items', 'cards.id', '=', 'items.cardId')
//////                ->where('items.type','=', 1)
//////                ->select('cards.teamId', 'items.id', 'items.type', 'items.title')
//////                ->orderBy('cards.teamId', 'asc')
//////                ->orderBy('items.id', 'desc')
//////                ->get();
//////
//////            $_itemsNum = [];
//////            $_brodItemsNum = [];
//////            foreach($_items as $_c) {
//////                if($card->teamId === $_c->teamId){
//////                    $_itemsNum[] = $_c->id;
//////                }
//////
//////                $_brodItemsNum[] = $_c->id;
//////            }
//////            $_itemsNum = [];
//////            foreach($_cards as $_c) {
//////                if (!is_null($_c->itemsNum)) {
//////                    foreach ($_c->itemsNum as $itemNum) {
//////                        $_itemsNum[] = $itemNum;
//////                    }
//////                }
//////            }
////
////            if ($card->type != 10) {
////                if ($_card) {
////                    broadcast(
////                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
////                    )->toOthers();
////
////                    //this.team -> this.team
////                    broadcast(
////                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
////                    );
////                }
////                //this.team -> others.myclass
////
////
////                broadcast(
////                    new ItemEvent(['item' => $item->toArray(), 'cardId' => $card->id], $card->teamId, 'MYTEAM', 'add')
////                )->toOthers();
////            }
////            else {
////                if ($_card) {
////                    broadcast(
////                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum1, $card->classObjectId, 'create', 'MYCLASS')
////                    )->toOthers();
////
////                    //this.team -> this.team
////                    broadcast(
////                        new FixedCardItemEvent($item->toArray(), $_card->id, $_itemsNum2, $card->teamId, 'create', 'MYTEAM')
////                    );
////                }
////            }
////        }
//
//        $item->with_operation_result = $item->withOperationResult()->first();
//
//        return [$item];
//    }

    public function delete (Request $request) {
        //id: item->id
        //types: 'item'
        $item = Item::find($request['id']);
        if (!$item) {
            return ['fail' => 'id'];
        }
        if ($item->type == 4) {
            $classApply = $item->classApply()->first();
            if (!$classApply) {
                return ['fail' => 'id'];
            }
            if ($classApply->state != 'wait') {
                return ['fail' => 'permission denied'];
            }
        }
        if ($item->type > 1000) {
            return ['fail' => 'permission denied'];
        }
        if ($item->type == 4) {
            return ['fail' => 'permission denied'];
        }

        $card = $item->card();
        if (!$card) {
            return ['fail' => 'card'];
        }

        if ($item->userId == Auth::id()) {
            $item_ = [
                'id' => $item->id,
                'cardId' => $item->cardId,
                'type' => $item->type,
                'teamId' => $item->card()->teamId,
                'classObjectId' => $item->card()->classObjectId,
                'myPageId' => $item->card()->myPageId,
            ];


            $files = [];
            foreach ($item->getComments() as $comment) {
                $files[] = 'comment/'.$comment->filePathName;
            }
            foreach ($item->itemFileList() as $image) {
                $files[] = 'images/'.$image->pathName;
            }
            foreach ($item->itemFiles() as $file) {
                $files[] = 'files/'.$file->pathName;
            }
            $classApply = $item->classApply()->first();
            if ($classApply) {
                if (!is_null($classApply->progressPlan)) {
                    foreach($classApply->progressPlan as $file) {
                        $files[] = 'classApplyFile/'.$file['filePathName'];
                    }
                }
            }
            Storage::disk('local')->delete($files);
            $itemsNum = $card->itemsNum;
            $key = array_search($item->id, $itemsNum);
            array_splice($itemsNum, $key, 1);
            $card->itemsNum = $itemsNum;
            $card->save();
            $item->delete();
//
//            if ($item->type == 1) {
////                $item->with_problem_analysis = $item->withProblemAnalysis()->first();
//            }
//            if ($item->type == 2) {
////                $item->with_reflection_log = $item->withReflectionLog()->first();
//            }
            if (is_null($item_['myPageId'])) {
                broadcast(
                    new ItemEvent(
                        $item_,
                        $item_['classObjectId'],
                        'MYCLASS',
                        'del')
                )->toOthers();

                if (!is_null($item_['teamId'])) {
                    broadcast(
                        new ItemEvent(
                            $item_,
                            $item_['teamId'],
                            'MYTEAM',
                            'del')
                    )->toOthers();
                }
            }

            return ['success' => true];
        }
        else {
            return ['fail' => 'permission denied'];
        }
    }

    //아이템 업데이트 수정 해야함
    //validation, refactoring
//    public function update (Request $request) {
//        $validator = Validator::make($request->all(), [
//            'subTypes' => ['required', 'string', 'in:move,edit,comment'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//            return ['fail' => true, 'sub' => false, $errors];
//        }
//
//        if ($request['subTypes'] == 'move') {
//            $validator = Validator::make($request->all(), [
//                'subId' => ['required', 'integer', 'min:1'],
//                '_subId' => ['integer', 'min:1'],
//                'destinationPosition' => ['required', 'integer', 'min:0'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('subId')) {
//                    return ['fail' => 'subId'];
//                }
//                else if ($errors->has('destinationPosition')) {
//                    return ['fail' => 'destinationPosition'];
//                }
//                else {
//                    return ['fail' => 'validate error'];
//                }
//            }
//
//            $item = Item::find($request['id']);
//            if (!$item) {
//                return ['fail' => 'item'];
//            }
//            $card = Card::find($request['subId']);
//            if (!$card) {
//                return ['fail' => 'card'];
//            }
//
//            if (!(in_array($item->card()->type, [4, 6, 11]) || in_array($card->type, [4, 6, 11]))) {
//                return ['fail' => 'invalid move'];
//            }
//
//            if (!$card->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $request['_subId'] : null)) {
//                return ['fail' => 'permission denied'];
//            }
//
//            if (($item->card()->id != $card->id) && (!(in_array($item->card()->type, [4, 6, 11]) || in_array($card->type, [4, 6, 11])))) {
//                return ['fail' => 'invalid move'];
//            }
//
//            if ($item->card()->myPageId != $card->myPageId || $item->card()->classObjectId != $card->classObjectId || $item->card()->teamId != $card->teamId) {
//                return ['fail' => 'not matchable card group'];
//            }
//
//            $sourceCard = $item->card();
//            $sourceItemsNum = $sourceCard->itemsNum;
//            $sourceKey = array_search($item->id, $sourceItemsNum);
//            $key = $request['destinationPosition'];
//            array_splice($sourceItemsNum, $sourceKey, 1);
//            $sourceCard->itemsNum = $sourceItemsNum;
//
//            if ($card->id == $sourceCard->id) {
//                $card = $sourceCard;
//            }
//
//            if ($key < 0) {
//                $key = 0;
//            }
//            else if (gettype($card->itemsNum) != 'array') {
//
//            }
//            else if ($key > count($card->itemsNum)) {
//                $key = count($card->itemsNum);
//            }
//
//            if (!$card->itemsNum) {
//                $card->itemsNum = [$item->id];
//            }
//            else {
//                $itemsNum = $card->itemsNum;
//                array_splice($itemsNum, $key, 0, $item->id);
//                $card->itemsNum = $itemsNum;
//            }
//
////                return dd($card->itemsNum);
//
//            $item->cardId = $card->id;
//
//            if ($card->id != $sourceCard->id) {
//                $sourceCard->save();
//                $item->save();
//            }
//            $card->save();
//
//            broadcast(
//                new ItemEvent(
//                    ['oldCard' => $sourceCard, 'newCard' => $card],
//                    $card->teamId ?? $card->classObjectId,
//                    $card->teamId ? 'MYTEAM' : 'MYCLASS',
//                    'move')
//            )->toOthers();
//
//            return ['success' => true];
//        }
//        else if ($request['subTypes'] == 'edit') {
//            $validator = Validator::make($request->all(), [
//                'problemAnalysis' => ['required', 'json'],
//                'reflectionLog' => ['required', 'json'],
//                'classApply' => ['required', 'json'],
//                'teamActivity' => ['required', 'json'],
//                'operationResult' => ['required', 'json'],
//                'type' => ['required', 'integer', 'min:0'],//max:3
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('type')) {
//                    return ['fail' => 'invalid parameter'];
//                }
//                else if ($request['type'] == 1) {
//                    if ($errors->has('problemAnalysis')) {
//                        return ['fail' => 'invalid parameter'];
//                    }
//                }
//                else if ($request['type'] == 2) {
//                    if ($errors->has('reflectionLog')) {
//                        return ['fail' => 'invalid parameter'];
//                    }
//                }
////                else if ($request['type'] == 4) {
////                    if ($errors->has('classApply')) {
////                        return ['fail' => 'invalid parameter'];
////                    }
////                }
//                else if ($request['type'] == 5) {
//                    if ($errors->has('teamActivity')) {
//                        return ['fail' => 'invalid parameter'];
//                    }
//                }
//                else if ($request['type'] == 8) {
//                    if ($errors->has('operationResult')) {
//                        return ['fail' => 'invalid parameter'];
//                    }
//                }
//            }
//
//            $validator = Validator::make($request->all(), [
//                'content' => ['string', 'nullable'],
//                'deadLine' => ['date', 'nullable'],
//                'activationDeadline' => ['boolean'],
//                'label' => ['integer', 'min:0', 'max:8'],
//                'activationLabel' => ['boolean'],
//                'checks' => ['json', 'nullable'],
//                'activationCheck' => ['boolean'],
//                'activationImage' => ['boolean'],
//                'party' => ['json', 'nullable'],
//                'activationParty' => ['boolean'],
//                'type' => ['required', 'integer', 'min:0'],//max:3
//                'brain' => ['json', 'nullable'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('id')) {
//                    return ['fail' => 'id'];
//                }
//                if ($errors->has('cardId')) {
//                    return ['fail' => 'cardId'];
//                }
//                if ($errors->has('title')) {
//                    return ['fail' => 'title'];
//                }
//                if ($errors->has('content')) {
//                    return ['fail' => 'content'];
//                }
//                if ($errors->has('images')) {
//                    return ['fail' => 'images'];
//                }
//                if ($errors->has('images.*')) {
//                    return ['fail' => 'images.*'];
//                }
//                if ($errors->has('activationImage')) {
//                    return ['fail' => 'activationImage'];
//                }
//                if ($errors->has('imagesNum')) {
//                    return ['fail' => 'imagesNum'];
//                }
//                if ($errors->has('imagesNum.*')) {
//                    return ['fail' => 'imagesNum.*'];
//                }
//                if ($errors->has('deadLine')) {
//                    return ['fail' => 'deadLine'];
//                }
//                if ($errors->has('activationDeadline')) {
//                    return ['fail' => 'activationDeadline'];
//                }
//                if ($errors->has('label')) {
//                    return ['fail' => 'label'];
//                }
//                if ($errors->has('activationLabel')) {
//                    return ['fail' => 'activationLabel'];
//                }
//                if ($errors->has('checks')) {
//                    return ['fail' => 'checks'];
//                }
//                if ($errors->has('activationCheck')) {
//                    return ['fail' => 'activationCheck'];
//                }
//                if ($errors->has('brain')) {
//                    return ['fail' => 'brain'];
//                }
//                if ($errors->has('party')) {
//                    return ['fail' => 'party'];
//                }
//                if ($errors->has('activationParty')) {
//                    return ['fail' => 'activationParty'];
//                }
//                if ($errors->has('type')) {
//                    return ['fail' => 'type'];
//                }
//            }
//
//            $item = Item::find($request['id']);
//            if (!$item) {
//                return false;
//            }
//
//
//            if (Auth::user()->authority == 4) {
//                if ($item->isPermitted(Auth::id(), $request['subId'])) {
//                    if ($item->userId != Auth::id()) {
//                        return ['fail' => 'permission denied'];
//                    }
//                }
//                else {
//                    return ['fail' => 'permission denied'];
//                }
//            }
//            else if ($item->userId != Auth::id()) {
//                return ['fail' => 'permission denied'];
//            }
//
//            if ($request->has('title')) {
//                $item->title = $request['title'];
//            }
//            if ($request->has('content')) {
//                $item->content = $request['content'];
//            }
//            if ($request->has('deadLine')) {
//                $item->deadLine = $request['deadLine'];
//            }
//            if ($request->has('activationImage')) {
//                $item->activationImage = $request['activationImage'];
//            }
//            if ($request->has('activationDeadline')) {
//                $item->activationDeadline = $request['activationDeadline'];
//            }
//            if ($request->has('label')) {
//                $item->label = $request['label'];
//            }
//            if ($request->has('activationLabel')) {
//                $item->activationLabel = $request['activationLabel'];
//            }
//            if ($request->has('checks')) {
//                $item->checks = $request['checks'];
//            }
//            if ($request->has('party')) {
//                $item->party = $request['party'];
//            }
//
//
//            $imageNames = $item->images;
//            $_imageNames = [];
//            $newNum = 0;
//            $imagesNum = $request['imagesNum'];
//            if (!$imagesNum) {
//                $imagesNum = [];
//            }
//            foreach ($imagesNum as $imageNum) {
//                if ($imageNum > 0) {
//                    $key = array_search($imageNum, $imageNames);
//                    if (!$key && gettype($key) == 'boolean') {
//                        continue;
//                    }
//                    $_imageNames[] = $imageNames[$key];
//
//                    array_splice($imageNames, $key, 1);
//                }
//                else {
//                    if ($request->hasfile('images')) {
//                        $file = $request->file('images')[$newNum++];
//                        if (!$file) {
//                            continue;
//                        }
//
//                        $imageName = uniqid();
//                        Storage::disk('local')->putFileAs('/images', $file, $imageName);
//                        $imageFile = ItemFileList::create([
//                            'itemId' => $item->id,
//                            'type' => 'image',
//                            'fileName' => $file->getClientOriginalName(),
//                            'pathName' => $imageName,
////                            'imgUrl' => env('APP_URL').':'.env('PORT').'/images/'.$imageName
//                            'imgUrl' => env('APP_URL').'/images/'.$imageName
//                        ]);
//                        $_imageNames[] = $imageFile->id;
//                    }
//                }
//            }
//
//            $item->images = $_imageNames;
//
//            if ($imageNames) {
//                foreach ($imageNames as $imageName) {
//                    $itemFileList = ItemFileList::find($imageName);
//                    if ($itemFileList) {
//                        Storage::disk('local')->delete('images/'.$itemFileList->pathName);
//
//                        $itemFileList->delete();
//                    }
//                }
//            }
//
//            if ($request->has('checks')) {
////                    $checks = json_decode($request['checks']);
////                    $_check = [];
////                    if ($checks) {
////                        foreach ($checks as $check) {
////                            if (gettype($check) == 'object') {
////                                if (property_exists($check, 'content') && property_exists($check, 'checked')) {
////                                    $__check = ['content' => $check->content, 'checked' => $check->checked];
////                                    $validator = Validator::make($__check, [
////                                        'content' => ['required', 'string'],
////                                        'checked' => ['required', 'boolean'],
////                                    ]);
////
////                                    if (!$validator->fails()) {
////                                        $_check[] = $__check;
////                                    }
////                                    else {
////                                        return 'checks';
////                                    }
////                                }
////                            }
////                        }
////                    }
////                    $request['checks'] = $_check;
////
////                    $item->checks = $request['checks'];
//
//                $item->checks = $request['checks'];
//            }
//            if ($request->has('activationCheck')) {
//                $item->activationCheck = $request['activationCheck'];
//            }
//            if ($request->has('brain')) {
//                //
//            }
//            if ($request->has('party')) {
//                $item->party = $request['party'];
//            }
//            if ($request->has('activationParty')) {
//                $item->activationParty = $request['activationParty'];
//            }
//
////                return dd($request->all());
//
//            if ($request->has('type')) {
//                if ($request['type'] == 1) {
//                    $problemAnalysis = (array)json_decode($request['problemAnalysis']);
//
//                    $_problemAnalysis = ProblemAnalysis::where('itemId', $item->id)->first();
//                    if ($_problemAnalysis) {
//                        $_problemAnalysis->studentId = $problemAnalysis['studentId'];
//                        $_problemAnalysis->content1 = $problemAnalysis['content1'];
//                        $_problemAnalysis->content2 = $problemAnalysis['content2'];
//                        $_problemAnalysis->content3 = $problemAnalysis['content3'];
//                        $_problemAnalysis->content4 = $problemAnalysis['content4'];
//
//                        $_problemAnalysis->save();
//                    }
//                }
//                else if ($request['type'] == 2) {
//                    $reflectionLog = (array)json_decode($request['reflectionLog']);
//
//                    $_reflectionLog = ReflectionLog::where('itemId', $item->id)->first();
//                    if ($_reflectionLog) {
//                        $_reflectionLog->studentId = $reflectionLog['studentId'];
//                        $_reflectionLog->content1 = $reflectionLog['content1'];
//                        $_reflectionLog->content2 = $reflectionLog['content2'];
//                        $_reflectionLog->content3 = $reflectionLog['content3'];
//                        $_reflectionLog->content4 = $reflectionLog['content4'];
//
//                        $_reflectionLog->save();
//                    }
//                }
//                else if ($request['type'] == 5) {
//                    $teamActivity = (array)json_decode($request['teamActivity']);
//
//                    $validator = Validator::make($teamActivity, [
//                        'dateTime' => ['required', 'string'],
//                        'problemSolvingProcess' => ['required', 'string'],
//                        'attendees' => ['required', 'string'],
//                        'mainActivities' => ['required', 'string'],
//                        'task1' => ['required', 'string'],
//                        'task2' => ['required', 'string'],
//                        'discuss1' => ['required', 'string'],
//                        'schedule1' => ['required', 'string'],
//                        'schedule2' => ['required', 'string'],
//                        'schedule3' => ['required', 'string'],
//                        'schedule4' => ['required', 'string'],
//                    ]);
//
//                    if ($validator->fails()) {
//                        return ['fail' => 'invalid parameter'];
//                    }
//
//                    $_teamActivity = TeamActivity::where('itemId', $item->id)->first();
//
//                    if ($_teamActivity) {
//                        $_teamActivity->dateTime = $teamActivity['dateTime'];
//                        $_teamActivity->problemSolvingProcess = $teamActivity['problemSolvingProcess'];
//                        $_teamActivity->attendees = $teamActivity['attendees'];
//                        $_teamActivity->mainActivities = $teamActivity['mainActivities'];
//                        $_teamActivity->task1 = $teamActivity['task1'];
//                        $_teamActivity->task2 = $teamActivity['task2'];
//                        $_teamActivity->discuss1 = $teamActivity['discuss1'];
//                        $_teamActivity->schedule1 = $teamActivity['schedule1'];
//                        $_teamActivity->schedule2 = $teamActivity['schedule2'];
//                        $_teamActivity->schedule3 = $teamActivity['schedule3'];
//                        $_teamActivity->schedule4 = $teamActivity['schedule4'];
//
//                        $_teamActivity->save();
//                    }
//                }
//                else if ($request['type'] == 8) {
//                    try {
//                        $operationResult = (array)json_decode($request['operationResult']);
//
//                        $validator = Validator::make($operationResult, [
//                            'semester' => ['required', 'string'],
//                            'college' => ['required', 'string'],
//                            'lectureName' => ['required', 'string'],
//                            'grade' => ['required', 'string'],
//                            'division' => ['required', 'string'],
//                            'grades' => ['required', 'string'],
//                            'professor' => ['required', 'string'],
//                            'size' => ['required', 'string'],
//                            'icpblType' => ['required', 'string'],
//                            'summary' => ['required', 'string'],
//                            'classGoal' => ['required', 'string'],
//                            'method' => ['required', 'string'],
//                            'basicPlan' => ['required', 'string'],
//                            'title' => ['required', 'string'],
//                            'role' => ['required', 'string'],
//                            'scenario' => ['required', 'string'],
//                            'process' => ['required', 'array'],
//                            'outputType1' => ['required', 'string'],
//                            'outputType2' => ['required', 'string'],
//                            'outputType3' => ['required', 'string'],
//                            'outputType4' => ['required', 'string'],
//                            'outputType5' => ['required', 'string'],
//                            'outputType6' => ['required', 'string'],
//                            'outputType7' => ['required', 'string'],
//                            'outputType8' => ['required_if:outputType7,1', 'exclude_unless:outputType7,1', 'string'],
//                            'finalOutput' => ['required', 'string'],
//                            'mainStudent' => ['required', 'string'],
//                            'sTitle' => ['required', 'string'],
//                            'sName' => ['required', 'string'],
//                            'sRole1' => ['required', 'string'],
//                            'sRole2' => ['required_if:sRole,5', 'exclude_unless:sRole1,5', 'string'],
//                            'sLink' => ['required', 'string'],
//                            'sFeedback' => ['required', 'string'],
//                            'sOpinion' => ['required', 'string'],
//                            'pr1' => ['required', 'string'],
//                            'pr2' => ['required', 'string'],
//                            'pr3' => ['required', 'string'],
//                            'pr4' => ['required', 'string'],
//                            'pr5' => ['required', 'string'],
//                            'pr6' => ['required', 'string'],
//                        ]);
//
//                        if ($validator->fails()) {
//                            return ['fail' => 'invalid parameter'];
//                        }
//
//                        $_operationResult = operation::where('itemId', $item->id)->first();
//                        $_operationResult->semester = $operationResult['semester'];
//                        $_operationResult->college = $operationResult['college'];
//                        $_operationResult->lectureName = $operationResult['lectureName'];
//                        $_operationResult->grade = $operationResult['grade'];
//                        $_operationResult->division = $operationResult['division'];
//                        $_operationResult->grades = $operationResult['grades'];
//                        $_operationResult->professor = $operationResult['professor'];
//                        $_operationResult->size = $operationResult['size'];
//                        $_operationResult->icpblType = $operationResult['icpblType'];
//                        $_operationResult->summary = $operationResult['summary'];
//                        $_operationResult->classGoal = $operationResult['classGoal'];
//                        $_operationResult->method = $operationResult['method'];
//                        $_operationResult->basicPlan = $operationResult['basicPlan'];
//                        $_operationResult->title = $operationResult['title'];
//                        $_operationResult->role = $operationResult['role'];
//                        $_operationResult->scenario = $operationResult['scenario'];
//                        $_operationResult->process = $operationResult['process'];
//                        $_operationResult->outputType1 = $operationResult['outputType1'];
//                        $_operationResult->outputType2 = $operationResult['outputType2'];
//                        $_operationResult->outputType3 = $operationResult['outputType3'];
//                        $_operationResult->outputType4 = $operationResult['outputType4'];
//                        $_operationResult->outputType5 = $operationResult['outputType5'];
//                        $_operationResult->outputType6 = $operationResult['outputType6'];
//                        $_operationResult->outputType7 = $operationResult['outputType7'];
//                        $_operationResult->outputType8 = $operationResult['outputType8'];
//                        $_operationResult->finalOutput = $operationResult['finalOutput'];
//                        $_operationResult->mainStudent = $operationResult['mainStudent'];
//                        $_operationResult->sTitle = $operationResult['sTitle'];
//                        $_operationResult->sName = $operationResult['sName'];
//                        $_operationResult->sRole1 = $operationResult['sRole1'];
//                        $_operationResult->sRole2 = $operationResult['sRole2'];
//                        $_operationResult->sLink = $operationResult['sLink'];
//                        $_operationResult->sFeedback = $operationResult['sFeedback'];
//                        $_operationResult->sOpinion = $operationResult['sOpinion'];
//                        $_operationResult->pr1 = $operationResult['pr1'];
//                        $_operationResult->pr2 = $operationResult['pr2'];
//                        $_operationResult->pr3 = $operationResult['pr3'];
//                        $_operationResult->pr4 = $operationResult['pr4'];
//                        $_operationResult->pr5 = $operationResult['pr5'];
//                        $_operationResult->pr6 = $operationResult['pr6'];
//
//                        $_operationResult->save();
//                    }
//                    catch (Exception $e) {
//                        $item->remove();
//
//                        return ['fail' => 'exception'];
//                    }
//                }
//            }
//
//            $item->save();
//
//            $item->refresh();
//
//            $item->imagesNum = $item->images;
//            $item->images = $item->itemFileList();
//            if ($item->type == 1) {
//                $item->with_problem_analysis = $item->withProblemAnalysis()->first();
//            }
//            else if ($item->type == 2) {
//                $item->with_reflection_log = $item->withReflectionLog()->first();
//            }
//            else if ($item->type == 3) {
//                $item->with_brains = $item->withBrains()->get();
//            }
//            else if ($item->type == 4) {
//                $item->with_class_apply = $item->withClassApply()->first();
//            }
//            else if ($item->type == 5) {
//                $item->with_team_activity = $item->withTeamActivity()->first();
//            }
//            else if ($item->type == 9) {
//                $item->with_operation_result = $item->withOperationResult()->first();
//            }
//
//            if (is_null($item->card()->myPageId)) {
//                $item->load('withCard');
//                if($item->type == 0 ){
//                    broadcast(
//                        new ItemEvent(
//                            $item->toArray(),
//                            $item->card()->teamId ?? $item->card()->classObjectId,
//                            $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
//                            'update')
//                    )->toOthers();
//                }
//                else{
//                    broadcast(
//                        new ItemEvent(
//                            $item->toArray(),
//                            $item->card()->teamId,
//                            'MYTEAM',
//                            'update')
//                    )->toOthers();
//
//                    broadcast(
//                        new ItemEvent(
//                            $item->toArray(),
//                            $item->card()->classObjectId,
//                            'MYCLASS',
//                            'update')
//                    )->toOthers();
//                }
//            }
//
//            return [$item];
//        }
//        else if ($request['subTypes'] == 'comment') {
//            //id: item->id
//            //types: 'item'
//            //subTypes: 'comment'
//            //content: '댓글 내용'
//            //file: 파일
//            $validator = Validator::make($request->all(), [
//                'content' => ['required', 'string'],
//                'file' => ['file', 'nullable'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('content')) {
//                    return ['fail' => 'content'];
//                }
//                else if ($errors->has('file')) {
//                    return ['fail' => 'file'];
//                }
//            }
//
//            $item = Item::find($request['id']);
//            if (!$item) {
//                return ['fail' => 'id'];
//            }
//
////                $card = $item->card();
////                if (!$card) {
////                    return ['fail' => 'card'];
////                }
//
//            if (!($item->isPermitted(Auth::id()))) {
//                return ['fail' => 'permission denied'];
//            }
//
//            $comment = null;
//            if ($request->hasFile('file')) {
//                $file = $request->file('file');
//                $filePathName = uniqid();
//                Storage::disk('local')->putFileAs('/comment', $file, $filePathName);
//                $comment = Comment::create([
//                    'userId' => Auth::id(),
//                    'itemId' => $item->id,
//                    'content' => $request['content'],
//                    'fileName' => $file->getClientOriginalName(),
//                    'filePathName' => $filePathName,
//                ]);
//            }
//            else {
//                $comment = Comment::create([
//                    'userId' => Auth::id(),
//                    'itemId' => $item->id,
//                    'content' => $request['content'],
//                ]);
//
//                $comment->refresh();
//            }
//
//            if (!$comment) {
//                return ['fail' => 'comment'];
//            }
//            else {
//                $comment->with_user = $comment->withUser()->first();
//            }
//
//            broadcast(
//                new CommentEvent(['itemId' => $item->id, 'comment' => $comment], $item->card()->teamId ?? $item->card()->classObjectId,
//                    $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
//                    'add')
//            )->toOthers();
//
//            return [$comment];
//        }
//    }
    public function updateProblemAnalysis ($problemAnalysis) {
        try {
            $validator = Validator::make(['problem' => $problemAnalysis], [
                'problem' => ['json'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $problemAnalysis = (array)json_decode($problemAnalysis);

            $validator = Validator::make($problemAnalysis, [
                'studentId' => ['required', 'string'],
                'name' => ['required', 'string'],
                'content1' => ['required', 'string'],
                'content2' => ['required', 'string'],
                'content3' => ['required', 'string'],
                'content4' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $subData = [];
            $subData['studentId'] = $problemAnalysis['studentId'];
            $subData['content1'] = $problemAnalysis['content1'];
            $subData['content2'] = $problemAnalysis['content2'];
            $subData['content3'] = $problemAnalysis['content3'];
            $subData['content4'] = $problemAnalysis['content4'];

            return ['fail' => false, 'sub' => true, $subData];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function updateReflectionLog ($reflectionLog) {
        try {
            $validator = Validator::make(['reflection' => $reflectionLog], [
                'reflection' => ['json'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $reflectionLog = (array)json_decode($reflectionLog);

            $validator = Validator::make($reflectionLog, [
                'studentId' => ['required', 'string'],
                'content1' => ['required', 'string'],
                'content2' => ['required', 'string'],
                'content3' => ['required', 'string'],
                'content4' => ['required', 'string'],
                'content5' => ['required', 'string'],
                'content6' => ['required', 'string'],
                'content7' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $subData = [];
            $subData['studentId'] = $reflectionLog['studentId'];
            $subData['content1'] = $reflectionLog['content1'];
            $subData['content2'] = $reflectionLog['content2'];
            $subData['content3'] = $reflectionLog['content3'];
            $subData['content4'] = $reflectionLog['content4'];
            $subData['content5'] = $reflectionLog['content5'];
            $subData['content6'] = $reflectionLog['content6'];
            $subData['content7'] = $reflectionLog['content7'];

            return ['fail' => false, 'sub' => true, $subData];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function updateTeamActivity ($teamActivity) {
        try {
            $validator = Validator::make(['team' => $teamActivity], [
                'team' => ['json'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $teamActivity = (array)json_decode($teamActivity);

            $validator = Validator::make($teamActivity, [
                'dateTime' => ['required', 'string'],
                'problemSolvingProcess' => ['required', 'string'],
                'attendees' => ['required', 'string'],
                'mainActivities' => ['required', 'string'],
                'task1' => ['required', 'string'],
                'task2' => ['required', 'string'],
                'discuss1' => ['required', 'string'],
                'schedule1' => ['required', 'string'],
                'schedule2' => ['required', 'string'],
                'schedule3' => ['required', 'string'],
                'schedule4' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $subData = [];
            $subData['dateTime'] = $teamActivity['dateTime'];
            $subData['problemSolvingProcess'] = $teamActivity['problemSolvingProcess'];
            $subData['attendees'] = $teamActivity['attendees'];
            $subData['mainActivities'] = $teamActivity['mainActivities'];
            $subData['task1'] = $teamActivity['task1'];
            $subData['task2'] = $teamActivity['task2'];
            $subData['discuss1'] = $teamActivity['discuss1'];
            $subData['schedule1'] = $teamActivity['schedule1'];
            $subData['schedule2'] = $teamActivity['schedule2'];
            $subData['schedule3'] = $teamActivity['schedule3'];
            $subData['schedule4'] = $teamActivity['schedule4'];

            return ['fail' => false, 'sub' => true, $subData];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function updateOperationResult ($operationResult) {
        try {
            $validator = Validator::make(['operation' => $operationResult], [
                'operation' => ['json'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $operationResult = (array)json_decode($operationResult);

            $validator = Validator::make($operationResult, [
                'semester' => ['required', 'string'],
                'college' => ['required', 'string'],
                'lectureName' => ['required', 'string'],
                'grade' => ['required', 'string'],
                'division' => ['required', 'string'],
                'grades' => ['required', 'string'],
                'professor' => ['required', 'string'],
                'size' => ['required', 'string'],
                'icpblType' => ['required', 'string'],
                'summary' => ['required', 'string'],
                'classGoal' => ['required', 'string'],
                'method' => ['required', 'string'],
                'basicPlan' => ['required', 'string'],
                'title' => ['required', 'string'],
                'role' => ['required', 'string'],
                'scenario' => ['required', 'string'],
                'process' => ['required', 'array'],
                'outputType1' => ['required', 'string'],
                'outputType2' => ['required', 'string'],
                'outputType3' => ['required', 'string'],
                'outputType4' => ['required', 'string'],
                'outputType5' => ['required', 'string'],
                'outputType6' => ['required', 'string'],
                'outputType7' => ['required', 'string'],
                'outputType8' => ['required_if:outputType7,1', 'exclude_unless:outputType7,1', 'string'],
                'finalOutput' => ['required', 'string'],
                'mainStudent' => ['required', 'string'],
                'sTitle' => ['required', 'string'],
                'sName' => ['required', 'string'],
                'sRole1' => ['required', 'string'],
                'sRole2' => ['required_if:sRole,5', 'exclude_unless:sRole1,5', 'string'],
                'sLink' => ['required', 'string'],
                'sFeedback' => ['required', 'string'],
                'sOpinion' => ['required', 'string'],
                'pr1' => ['required', 'string'],
                'pr2' => ['required', 'string'],
                'pr3' => ['required', 'string'],
                'pr4' => ['required', 'string'],
                'pr5' => ['required', 'string'],
                'pr6' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $subData = [];
            $subData['semester'] = $operationResult['semester'];
            $subData['college'] = $operationResult['college'];
            $subData['lectureName'] = $operationResult['lectureName'];
            $subData['grade'] = $operationResult['grade'];
            $subData['division'] = $operationResult['division'];
            $subData['grades'] = $operationResult['grades'];
            $subData['professor'] = $operationResult['professor'];
            $subData['size'] = $operationResult['size'];
            $subData['icpblType'] = $operationResult['icpblType'];
            $subData['summary'] = $operationResult['summary'];
            $subData['classGoal'] = $operationResult['classGoal'];
            $subData['method'] = $operationResult['method'];
            $subData['basicPlan'] = $operationResult['basicPlan'];
            $subData['title'] = $operationResult['title'];
            $subData['role'] = $operationResult['role'];
            $subData['scenario'] = $operationResult['scenario'];
            $subData['process'] = $operationResult['process'];
            $subData['outputType1'] = $operationResult['outputType1'];
            $subData['outputType2'] = $operationResult['outputType2'];
            $subData['outputType3'] = $operationResult['outputType3'];
            $subData['outputType4'] = $operationResult['outputType4'];
            $subData['outputType5'] = $operationResult['outputType5'];
            $subData['outputType6'] = $operationResult['outputType6'];
            $subData['outputType7'] = $operationResult['outputType7'];
            $subData['outputType8'] = $operationResult['outputType8'];
            $subData['finalOutput'] = $operationResult['finalOutput'];
            $subData['mainStudent'] = $operationResult['mainStudent'];
            $subData['sTitle'] = $operationResult['sTitle'];
            $subData['sName'] = $operationResult['sName'];
            $subData['sRole1'] = $operationResult['sRole1'];
            $subData['sRole2'] = $operationResult['sRole2'];
            $subData['sLink'] = $operationResult['sLink'];
            $subData['sFeedback'] = $operationResult['sFeedback'];
            $subData['sOpinion'] = $operationResult['sOpinion'];
            $subData['pr1'] = $operationResult['pr1'];
            $subData['pr2'] = $operationResult['pr2'];
            $subData['pr3'] = $operationResult['pr3'];
            $subData['pr4'] = $operationResult['pr4'];
            $subData['pr5'] = $operationResult['pr5'];
            $subData['pr6'] = $operationResult['pr6'];

            return ['fail' => false, 'sub' => true, $subData];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function updateClassApply ($classApply, $title) {
        try {
            $validator = Validator::make(['classApply' => $classApply], [
                'classApply' => ['json'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $classApply = (array)json_decode($classApply);

            $classApply['title'] = $title;

            $rv = new RequiredValidator();
            $result = $rv->validate($classApply, [
                'type',
                'grade',
                'size1',
                'proSize1',
                'department',
//                'major',
                'korName',
                'engName',
                'gradesPoint',
                'lecturePoint',
                'exercisePoint',
                'description',
                'meca',
                'role1',
                'role3',
                'role4',
                'role5',
                'expected1',
                'expected2',
                'expected3',
                'expected4',
                'expected5',
                'expected6',
                'expected7',
//                'aplName',
//                'aplOrg',
//                'aplTel',
//                'aplPhone',
//                'aplEmail',
                'agree1',
                'basic1',
                'basic2',
                'basic3',
                'basic4',
                'basic5',
                'basic6',
                'basicPlan',
                'sceContent',
                'sceGoal',
                'sceTitle',
                'sceRole',
                'sceDetail',
                'planDetail',
            ], 'classApply');

            if ($result) {
                return ['fail' => true, 'sub' => true, $result];
            }

            $validator = Validator::make($classApply, [
                'mode' => ['required', 'integer', 'min:1', 'max:2'], //1:기존 과목, 2: 이전 과목
                'type' => ['required', 'integer', 'min:1', 'max:4'],
                'grade' => ['required', 'integer', 'min:1', 'max:4'],
                'size1' => ['required', 'integer', 'min:1', 'max:3'],
                'size2' => ['required_if:size1,3', 'exclude_unless:size1,3', 'integer', 'min:31'], ////////////
                'proSize1' => ['required', 'integer', 'min:1', 'max:3'],
                'proSize2' => ['required_if:proSize1,2', 'exclude_unless:proSize1,2', 'integer', 'min:1'], /////////////
                'proSize3' => ['required_if:proSize1,3', 'exclude_unless:proSize1,3', 'integer', 'min:1'], /////////////
                'daehak' => ['required', 'string'],
                'department' => ['required', 'string'],
//                'major' => ['required', 'string'],
                'special' => ['required', 'boolean'],
                'special2' => ['required', 'boolean'],
                'special3' => ['required', 'boolean'],
                'special4' => ['required', 'boolean'],
                'korName' => ['required', 'string', 'same:title'],
                'engName' => ['required', 'string'],
                'gradesPoint' => ['required', 'integer', 'min:0'],
                'lecturePoint' => ['required', 'integer', 'min:0'],
                'exercisePoint' => ['required', 'integer', 'min:0'],
                'description' => ['required', 'string'],
                'meca' => ['required', 'integer', 'min:1', 'max:4'],
                'agency' => ['string', 'nullable'],
                'expert' => ['string', 'nullable'],
                'role1' => ['required', 'boolean'],
                'role3' => ['required', 'boolean'],
                'role4' => ['required', 'boolean'],
                'role5' => ['required', 'boolean'],
                'role2' => ['required_if:role1,4', 'exclude_unless:role1,4', 'string'], //////////////////////
                'expected1' => ['required', 'boolean'],
                'expected2' => ['required', 'boolean'],
                'expected3' => ['required', 'boolean'],
                'expected4' => ['required', 'boolean'],
                'expected5' => ['required', 'boolean'],
                'expected6' => ['required', 'boolean'],
                'expected7' => ['required', 'boolean'],
                'expected8' => ['required_if:expected7,1', 'exclude_unless:expected7,1', 'string'], /////////////////
//                'aplName' => ['required', 'string'],
//                'aplSign' => [],
//                'aplOrg' => ['required', 'string'],
//                'aplTel' => ['required', 'string'],
//                'aplPhone' => ['required', 'string'],
//                'aplEmail' => ['required', 'string'],//, 'email'],
                'applicant' => ['required', 'array'],
                'agree1' => ['required', 'boolean', 'in:1,true'],
                'duration' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conName' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
//                'conPer' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
                'contribute' => ['required_if:mode,1', 'exclude_unless:mode,1', 'array'],
                'conDescription' => ['required_if:mode,1', 'exclude_unless:mode,1', 'string'],
                'agree2' => ['required_if:mode,1', 'exclude_unless:mode,1', 'boolean', 'in:1,true'],
                'agree3' => ['required_if:mode,1', 'exclude_unless:mode,1', 'boolean', 'in:1,true'],
                'basic1' => ['required', 'string'],
                'basic2' => ['required', 'string'],
                'basic3' => ['required', 'string'],
                'basic4' => ['required', 'string'],
                'basic5' => ['required', 'string'],
                'basic6' => ['required', 'string'],
                'basicPlan' => ['required', 'string'],
                'sceContent' => ['required', 'string'],
                'sceGoal' => ['required', 'string'],
                'sceTitle' => ['required', 'string'],
                'sceRole' => ['required', 'string'],
                'sceDetail' => ['required', 'string'],
                'planDetail' => ['required', 'array'],
//                'planDetail[].*.content' => ['nullable', 'string'],
//                'planDetail[].*.level' => ['nullable', 'string'],
//                'planDetail[].*.stuContent' => ['nullable', 'string'],
//                'planDetail[].*.subject' => ['nullable', 'string'],
//                'planDetail[].*.method1' => ['required', 'boolean'],
//                'planDetail[].*.method2' => ['required', 'boolean'],
//                'planDetail[].*.method3' => ['required', 'boolean'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }

            $daehak = Daehak::where('name', $classApply['daehak'])->where('department', $classApply['department'])->first();//->where('major', $classApply['major'])->first();
            if (!$daehak) {
                return ['fail' => true, 'sub' => true, ['department' => ['올바른 학과,전공 정보를 입력해주세요.']]];
            }

            if ($classApply['mode'] == "1") {
                $contributes = [];
                foreach ($classApply['contribute'] as $key=>$c) {
                    $contributes[] = (array)$c;
                }

                $validator = Validator::make($contributes, [
                    '*.name' => ['required', 'string'], // required
                    '*.per' => ['required', 'integer'], // required
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return ['fail' => true, 'sub' => true, 'contribute' => true, $errors];
                }

                $contributes = collect($contributes);
                if ($contributes->sum('per') != 100) {
                    return ['fail' => true, 'sub' => true, 'contribute' => true, ['contribute' => ['기여도의 합이 100이 아닙니다.']]];
                }
            }

            $applicants = [];
            foreach ($classApply['applicant'] as $key=>$a) {
                $applicants[] = (array)$a;
            }

            $validator = Validator::make($applicants, [
                '*.name' => ['required', 'string'], // required
                '*.org' => ['required', 'string'], // required
                '*.tel' => ['required', 'string'], // required
                '*.phone' => ['required', 'string'], // required
                '*.email' => ['required', 'email'], // required
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, 'applicant' => true, $errors];
            }

            $applicants = collect($applicants);
            if ($applicants->count() <= 0) {
                return ['fail' => true, 'sub' => true, 'applicant' => true, ['applicant' => ['신청자 정보를 입력해주세요.']]];
            }

            if (!(
                (int)$classApply['expected1']
                + (int)$classApply['expected2']
                + (int)$classApply['expected3']
                + (int)$classApply['expected4']
                + (int)$classApply['expected5']
                + (int)$classApply['expected6']
                + (int)$classApply['expected7']
            )) {
                return ['fail' => true, 'sub' => true, ['expected' => '예상수업결과물은 필수입력 항목입니다.']];
            }

            $planDetail = [];
            foreach ($classApply['planDetail'] as $p) {
                $planDetail[] = (array)$p;
            }

            $validator = Validator::make($planDetail, [
                '*.content' => ['string', 'nullable'], // required
                '*.level' => ['string', 'nullable'], // required
                '*.stuContent' => ['string', 'nullable'], // required
                '*.subject' => ['string', 'nullable'], // required
                '*.method1' => ['required', 'boolean'],
                '*.method2' => ['required', 'boolean'],
                '*.method3' => ['required', 'boolean'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return ['fail' => true, 'sub' => true, $errors];
            }


            $subData = [];
            $subData['mode'] = $classApply['mode'];
            $subData['type'] = $classApply['type'];
            $subData['grade'] = $classApply['grade'];
            $subData['size1'] = $classApply['size1'];
            $subData['size2'] = $classApply['size2'];
            $subData['proSize1'] = $classApply['proSize1'];
            $subData['proSize2'] = $classApply['proSize2'];
            $subData['proSize3'] = $classApply['proSize3'];
            $subData['daehak'] = $classApply['daehak'];
            $subData['department'] = $classApply['department'];
            $subData['major'] = $classApply['major'];
            $subData['special'] = $classApply['special'];
            $subData['special2'] = $classApply['special2'];
            $subData['special3'] = $classApply['special3'];
            $subData['special4'] = $classApply['special4'];
            $subData['korName'] = $classApply['korName'];
            $subData['engName'] = $classApply['engName'];
            $subData['gradesPoint'] = $classApply['gradesPoint'];
            $subData['lecturePoint'] = $classApply['lecturePoint'];
            $subData['exercisePoint'] = $classApply['exercisePoint'];
            $subData['description'] = $classApply['description'];
            $subData['meca'] = $classApply['meca'];
            $subData['agency'] = $classApply['agency'];
            $subData['expert'] = $classApply['expert'];
            $subData['role1'] = $classApply['role1'];
            $subData['role3'] = $classApply['role3'];
            $subData['role4'] = $classApply['role4'];
            $subData['role5'] = $classApply['role5'];
            $subData['role2'] = $classApply['role2'];
            $subData['expected1'] = $classApply['expected1'];
            $subData['expected2'] = $classApply['expected2'];
            $subData['expected3'] = $classApply['expected3'];
            $subData['expected4'] = $classApply['expected4'];
            $subData['expected5'] = $classApply['expected5'];
            $subData['expected6'] = $classApply['expected6'];
            $subData['expected7'] = $classApply['expected7'];
            $subData['expected8'] = $classApply['expected8'];
//            $subData['aplName'] = $classApply['aplName'];
//            $subData['aplSign'] = $classApply['aplSign'];
//            $subData['aplOrg'] = $classApply['aplOrg'];
//            $subData['aplTel'] = $classApply['aplTel'];
//            $subData['aplPhone'] = $classApply['aplPhone'];
//            $subData['aplEmail'] = $classApply['aplEmail'];
            $subData['applicant'] = $classApply['applicant'];
            $subData['agree1'] = $classApply['agree1'];
            $subData['duration'] = $classApply['duration'];
//            $subData['conName'] = $classApply['conName'];
//            $subData['conPer'] = $classApply['conPer'];
            $subData['contribute'] = $classApply['contribute'];
            $subData['conDescription'] = $classApply['conDescription'];
            $subData['agree2'] = $classApply['agree2'];
            $subData['agree3'] = $classApply['agree3'];
            $subData['basic1'] = $classApply['basic1'];
            $subData['basic2'] = $classApply['basic2'];
            $subData['basic3'] = $classApply['basic3'];
            $subData['basic4'] = $classApply['basic4'];
            $subData['basic5'] = $classApply['basic5'];
            $subData['basic6'] = $classApply['basic6'];
            $subData['basicPlan'] = $classApply['basicPlan'];
            $subData['sceContent'] = $classApply['sceContent'];
            $subData['sceGoal'] = $classApply['sceGoal'];
            $subData['sceTitle'] = $classApply['sceTitle'];
            $subData['sceRole'] = $classApply['sceRole'];
            $subData['sceDetail'] = $classApply['sceDetail'];
            $subData['planDetail'] = $classApply['planDetail'];

            return ['fail' => false, 'sub' => true, $subData];
        }
        catch (Exception $e) {
            return ['fail' => true, 'sub' => true, 'exception' => ['exception']];
        }
    }
    public function update (Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'content' => ['string', 'nullable'],
            'deadLine' => ['date', 'nullable'],
            'activationDeadline' => ['boolean'],
            'activationFile' => ['boolean'],
            'label' => ['integer', 'min:0', 'max:8'],
            'activationLabel' => ['boolean'],
            'checks' => ['json', 'nullable'],
            'activationCheck' => ['boolean'],
            'activationImage' => ['boolean'],
            'party' => ['json', 'nullable'],
            'activationParty' => ['boolean'],
            'type' => ['required', 'integer', 'min:0'],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return ['fail' => true, 'sub' => false, $errors];
        }


        if ($request['activationCheck'] == '1') {
            try {
                $checks = (array)json_decode($request['checks']);

                foreach ($checks as $key=>$check) {
                    $checks[$key] = (array)$check;
                }

                $validator = Validator::make($checks, [
                    '*.checked' => ['required', 'boolean'],
                    '*.content' => ['required', 'string']
                ]);

                if ($validator->fails()) {
                    return ['fail' => true, 'sub' => false, ['checks' => ['체크리스트 내용을 전부 입력해 주세요.']]];
                }
            }
            catch(Exception $e) {
                return ['fail' => true, 'sub' => false, ['checks' => ['체크리스트 형식이 맞지 않습니다.']]];
            }
        }

        $item = Item::find($request['id']);
        if (!$item) {
            return ['fail' => true, 'sub' => false, ['id' => ['item not found']]];
        }
        if ($item->type == 4) {
            $classApply = $item->withClassApply()->first();
            if (!$classApply || $classApply->state != 'wait') {
                return ['fail' => true, 'sub' => false, ['id' => ['승인 대기 상태인 신청만 수정이 가능합니다.']]];
            }
        }
        if ($item->type == 1001) {
            return ['fail' => true, 'sub' => false, ['id' => ['permission denied']]];
        }


        $subData = ['fail' => false, 'sub' => false, ['initialize' => ['initialize']]];
        switch ($request['type']) {
            case 1: //problem
                $subData = $this->updateProblemAnalysis($request['problemAnalysis']);
                break;
            case 2: //reflection
                $subData = $this->updateReflectionLog($request['reflectionLog']);
                break;
            case 4: //classApply
                $classApply = $item->withClassApply()->first();
                if (!$classApply || $classApply->state != 'wait') {
//                    return ['fail' => true, 'sub' => false, ['id' => ['permission denied']]];
                }
                else {
                    $subData = $this->updateClassApply($request['classApply'], $request['title']);
                }
                break;
            case 5: //team activity
                $subData = $this->updateTeamActivity($request['teamActivity']);
                break;
            case 8: //operation
                $subData = $this->updateOperationResult($request['operationResult']);
                break;
        }

        if ($subData['fail']) {
            return $subData;
        }


        if (Auth::user()->authority == 4) {
            if ($item->isPermitted(Auth::id(), $request['subId'])) {
                if ($item->userId != Auth::id()) {
                    return ['fail' => true, 'sub' => false, ['subId' => ['permission denied']]];
                }
            }
            else {
                return ['fail' => true, 'sub' => false, ['subId' => ['permission denied']]];
            }
        }
        else if ($item->userId != Auth::id()) {
            return ['fail' => true, 'sub' => false, ['id' => ['permission denied']]];
        }

        $updateData = [];
        $updateData['title'] = $request['title'];
        if ($request->has('content')) {
            $updateData['content'] = $request['content'];
        }
        if ($request->has('deadLine')) {
            $updateData['deadLine'] = $request['deadLine'];
        }
        if ($request->has('activationImage')) {
            $updateData['activationImage'] = $request['activationImage'];
        }
        if ($request->has('activationDeadline')) {
            $updateData['activationDeadline'] = $request['activationDeadline'];
        }
        if ($request->has('label')) {
            $updateData['label'] = $request['label'];
        }
        if ($request->has('activationLabel')) {
            $updateData['activationLabel'] = $request['activationLabel'];
        }
        if ($request->has('checks')) {
            $updateData['checks'] = $request['checks'];
        }
        if ($request->has('party')) {
            $updateData['party'] = $request['party'];
        }
        if ($request->has('checks')) {
            $updateData['checks'] = $request['checks'];
        }
        if ($request->has('activationCheck')) {
            $updateData['activationCheck'] = $request['activationCheck'];
        }
        if ($request->has('activationParty')) {
            $updateData['activationParty'] = $request['activationParty'];
        }

        ///////////////////////////////////////////////////////////////////////////
        /// //activationFile
        $fileNames = $item->files;
        $_fileNames = [];
        $files = [];
        $newNum = 0;
        $filesNum = $request['filesNum'];
        if (!$filesNum) {
            $filesNum = [];
        }
        foreach ($filesNum as $fileNum) {
            if ($fileNum > 0) {
                $key = array_search($fileNum, $fileNames);
                if (!$key && gettype($key) == 'boolean') {
                    continue;
                }
                $_fileNames[] = $fileNames[$key];

                array_splice($fileNames, $key, 1);
            }
            else {
                if ($request->hasfile('files')) {
                    $file = $request->file('files')[$newNum++];
                    if (!$file) {
                        continue;
                    }

                    $fileName = uniqid();
                    Storage::disk('local')->putFileAs('/files', $file, $fileName);
                    $files[] = new ItemFileList([
                        'type' => 'file',
                        'fileName' => $file->getClientOriginalName(),
                        'pathName' => $fileName,
                        'imgUrl' => env('APP_URL').'/files/'.$fileName
                    ]);
                }
            }
        }

//        $item->images = $_imageNames;

        if ($fileNames) {
            foreach ($fileNames as $fileName) {
                $itemFileList = ItemFileList::find($fileName);
                if ($itemFileList) {
                    Storage::disk('local')->delete('files/'.$itemFileList->pathName);

                    $itemFileList->delete();
                }
            }
        }
        if (count($files) > 0) {
            $item->withImages()->saveMany($files);
            foreach ($files as $file) {
                $_fileNames[] = $file->id;
            }
        }
        $updateData['files'] = $_fileNames;
        Item::where('id', $item->id)->update($updateData);
        $item->refresh();
        ///////////////////////////////////////////////////////////////////////////

        $imageNames = $item->images;
        $_imageNames = [];
        $imageFile = [];
        $newNum = 0;
        $imagesNum = $request['imagesNum'];
        if (!$imagesNum) {
            $imagesNum = [];
        }
        foreach ($imagesNum as $imageNum) {
            if ($imageNum > 0) {
                $key = array_search($imageNum, $imageNames);
                if (!$key && gettype($key) == 'boolean') {
                    continue;
                }
                $_imageNames[] = $imageNames[$key];

                array_splice($imageNames, $key, 1);
            }
            else {
                if ($request->hasfile('images')) {
                    $file = $request->file('images')[$newNum++];
                    if (!$file) {
                        continue;
                    }

                    $imageName = uniqid();
                    Storage::disk('local')->putFileAs('/images', $file, $imageName);
                    $imageFile[] = new ItemFileList([
                        'type' => 'image',
                        'fileName' => $file->getClientOriginalName(),
                        'pathName' => $imageName,
                        'imgUrl' => env('APP_URL').'/images/'.$imageName
                    ]);
                }
            }
        }

//        $item->images = $_imageNames;

        if ($imageNames) {
            foreach ($imageNames as $imageName) {
                $itemFileList = ItemFileList::find($imageName);
                if ($itemFileList) {
                    Storage::disk('local')->delete('images/'.$itemFileList->pathName);

                    $itemFileList->delete();
                }
            }
        }
        if (count($imageFile) > 0) {
            $item->withImages()->saveMany($imageFile);
            foreach ($imageFile as $image) {
                $_imageNames[] = $image->id;
            }
        }
        $updateData['images'] = $_imageNames;
        Item::where('id', $item->id)->update($updateData);
        $item->refresh();
        $item->imagesNum = $item->images;
        $item->images = $item->itemFileList();
//        $item->load('withImages');
        //
        $item->filesNum = $item->files;
        $item->files = $item->itemFiles();
        //

        switch ($request['type']) {
            case 1: //problem
                $item->withProblemAnalysis()->update($subData[0]);
                $item->load('withProblemAnalysis');
                break;
            case 2: //reflection
                $item->withReflectionLog()->update($subData[0]);
                $item->load('withReflectionLog');
                break;
            case 4: //classApply
                $classApply = $item->withClassApply()->first();
                if (!$classApply || $classApply->state != 'wait') {
//                    return ['fail' => true, 'sub' => false, ['id' => ['permission denied']]];
                }
                else {
                    $classApply->update($subData[0]);
                    $item->load('withClassApply');
                }
                break;
            case 5: //team activity
                $item->withTeamActivity()->update($subData[0]);
                $item->load('withTeamActivity');
                break;
            case 8: //operation
                $item->withOperationResult()->update($subData[0]);
                $item->load('withOperationResult');
                break;
        }

        $card = $item->card();
        $item->load('withCard');
        if (is_null($card->myPageId)) {
            if (!in_array($card->type, [7, 9, 10])) {
                broadcast(
                    new ItemEvent(
                        $item->toArray(),
                        $card->teamId ?? $card->classObjectId,
                        $card->teamId ? 'MYTEAM' : 'MYCLASS',
                        'update')
                )->toOthers();
            }
            else {
                if (in_array($item->type, [1, 2, 5])) {
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





//            if (in_array($item->type, [1, 2, 5])) {
//                $item->load('withCard');
//            }
//
//            broadcast(
//                new ItemEvent(
//                    $item->toArray(),
//                    $card->teamId ?? $card->classObjectId,
//                    $card->teamId ? 'MYTEAM' : 'MYCLASS',
//                    'update')
//            )->toOthers();
//
//            if (!is_null($item->card()->teamId)) {
//                if ($item->card()->myClass()->onTeamAccess) {
//                    broadcast(
//                        new ItemEvent(
//                            $item->toArray(),
//                            $card->classObjectId,
//                            'MYCLASS',
//                            'update')
//                    )->toOthers();
//                }
//                else {
//                    broadcast(
//                        new SuperEvent(
//                            $item->toArray(),
//                            $card->classObjectId,
//                            'update')
//                    )->toOthers();
//                }
//            }

//            if (!is_null($card->classObjectId) && in_array($item->type, [1, 2, 5])) {
//                $user = Auth::user();
//                $classObject = ClassObject::find($card->classObjectId);
//                $team = Team::find($card->teamId);
//                $myClass = MyClass::where('classObjectId', $classObject->id)->first();
//                $classObject->load('class_apply');
//
//                $actLog = ActLog::create([
//                    'userId' => $user->id,
//                    'userName' => $user->name,
//                    'classObjectId' => $classObject->id,
//                    'classObjectName' => $classObject->class_apply ? $classObject->class_apply->korName : $classObject->gwamokNm,
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

        return [$item];
    }

    public function move (Request $request) {
        $validator = Validator::make($request->all(), [
            //id: 옮겨질 아이템 이이디
            'subId' => ['required', 'integer', 'min:1'], //도착지 카드 id
            '_subId' => ['integer', 'min:1'],
            'destinationPosition' => ['required', 'integer', 'min:0'], //도착지에서의 위치
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return ['fail' => true, 'sub' => false, $errors];
        }

        $item = Item::find($request['id']);
        if (!$item) {
            return ['fail' => true, 'sub' => false, ['id' => ['item not found']]];
        }
        $card = Card::find($request['subId']);
        if (!$card) {
            return ['fail' => true, 'sub' => false, ['subId' => ['card not found']]];
        }
        $_card = $item->card();

        $movableTypes = [
            'common' => 4,
            'orientation' => 6,
            'teamOrientation' => 11,
        ];

        if (!in_array($_card->type, $movableTypes)) {
            return ['fail' => true, 'sub' => false, ['id' => ['invalid card type']]];
        }

        if (!in_array($card->type, $movableTypes)) {
            return ['fail' => true, 'sub' => false, ['subId' => ['invalid card type']]];
        }

//        if (!(in_array($item->card()->type, [4, 6, 11]) || in_array($card->type, [4, 6, 11]))) {
//            return ['fail' => true, 'sub' => false, ['subId' => ['invalid card type']]];
//        }

        if (!$card->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $request['_subId'] : null)) {
            return ['fail' => true, 'sub' => false, ['subId' => ['permission denied']]];
        }

//        if (($item->card()->id != $card->id) && (!(in_array($item->card()->type, [4, 6, 11]) || in_array($card->type, [4, 6, 11])))) {
//            return ['fail' => 'invalid move'];
//        }

        if ($_card->myPageId != $card->myPageId || $_card->classObjectId != $card->classObjectId || $_card->teamId != $card->teamId) {
            return ['fail' => true, 'sub' => false, ['id' => ['invalid move: card group'], 'subId' => ['invalid move: card group']]];
        }

        $sourceCard = $_card;
        $sourceItemsNum = $sourceCard->itemsNum;
        $sourceKey = array_search($item->id, $sourceItemsNum);
        $key = $request['destinationPosition'];
        array_splice($sourceItemsNum, $sourceKey, 1);
        $sourceCard->itemsNum = $sourceItemsNum;

        if ($card->id == $sourceCard->id) {
            $card = $sourceCard;
        }

        if ($key < 0) {
            $key = 0;
        }
        else if (gettype($card->itemsNum) != 'array') {

        }
        else if ($key > count($card->itemsNum)) {
            $key = count($card->itemsNum);
        }

        if (!$card->itemsNum) {
            $card->itemsNum = [$item->id];
        }
        else {
            $itemsNum = $card->itemsNum;
            array_splice($itemsNum, $key, 0, $item->id);
            $card->itemsNum = $itemsNum;
        }

//                return dd($card->itemsNum);

        $item->cardId = $card->id;

        if ($card->id != $sourceCard->id) {
            $sourceCard->save();
            $item->save();
        }
        $card->save();

        broadcast(
            new ItemEvent(
                ['oldCard' => $sourceCard, 'newCard' => $card],
                $card->teamId ?? $card->classObjectId,
                $card->teamId ? 'MYTEAM' : 'MYCLASS',
                'move')
        )->toOthers();

        return ['success' => true];
    }
    public function comment (Request $request) {
        //id: item->id
        //types: 'item'
        //subTypes: 'comment'
        //content: '댓글 내용'
        //file: 파일
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string'],
            'file' => ['file', 'nullable'],
            'mentions' => ['required', 'json'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return ['fail' => true, 'sub' => false, $errors];
        }

        $item = Item::find($request['id']);
        if (!$item) {
            return ['fail' => true, 'sub' => false, ['id' => ['item not found']]];
        }

        if (!($item->isPermitted(Auth::id()))) {
            return ['fail' => true, 'sub' => false, ['id' => ['permission denied']]];
        }

        $mentions = (array)json_decode($request['mentions']);
        $mentions = array_unique($mentions);
        $users = User::whereIn('id', $mentions)->get();

        foreach ($mentions as $mention) {
            if (!$item->isPermitted2($mention)) {
                return ['fail' => true, 'sub' => false, ['id' => ['mention error']]];
            }
        }

        $comment = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePathName = uniqid();
            Storage::disk('local')->putFileAs('/comment', $file, $filePathName);
            $comment = Comment::create([
                'userId' => Auth::id(),
                'itemId' => $item->id,
                'content' => $request['content'],
                'fileName' => $file->getClientOriginalName(),
                'filePathName' => $filePathName,
            ]);
        }
        else {
            $comment = Comment::create([
                'userId' => Auth::id(),
                'itemId' => $item->id,
                'content' => $request['content'],
            ]);
        }

        $comment->with_user = $comment->withUser()->first();

        broadcast(
            new CommentEvent(['itemId' => $item->id, 'comment' => $comment], $item->card()->teamId ?? $item->card()->classObjectId,
                $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                'add')
        )->toOthers();

        $card = $item->card();
        if (!is_null($card->teamId) && in_array($item->type, [1, 2, 5]) && in_array($card->type, [7, 9, 10])) {
            $_card = Card::where('classObjectId', $card->classObjectId)->where('teamId', null)->where('type', $card->type)->first();
            if ($_card) {
                if ($card->myClass()->onTeamAccess) {
                    broadcast(
                        new CommentEvent(['itemId' => $item->id, 'comment' => $comment], $item->card()->classObjectId,
                            'MYCLASS',
                            'add')
                    )->toOthers();
                } else {
                    $item->comment = $comment;
                    broadcast(
                        new SuperEvent(
                            $item->toArray(),
                            $card->classObjectId,
                            $_card->id,
                            'comment.add')
                    )->toOthers();
                }
            }
        }

        $user = Auth::user();

        $classApply = ClassApply::where('classObjectId', $card->classObjectId)->first();
        foreach ($users as $u) {
            $proLog = ProLog::create([
                'userId' => $user->id,
                'targetId' => $u->id,
                'userName' => $user->name.($user->authority == 1 ? ' 학생' : ''),
                'authority' => $user->authority,
                'eventType' => $item->type * 10,
                'eventState' => 30,
                'eventTitle' => $item->title,
                'title' => $classApply ? $classApply->korName : null,
            ]);

            broadcast(
                new ProLogEvent($proLog->toArray(), $u->id)
            );
        }

        if ($item->userId != $user->id) {
            $proLog = ProLog::create([
                'userId' => $user->id,
                'targetId' => $item->userId,
                'userName' => $user->name.($user->authority == 1 ? ' 학생' : ''),
                'authority' => $user->authority,
                'eventType' => $item->type * 10,
                'eventState' => 40,
                'eventTitle' => $comment->content,
                'title' => $classApply ? $classApply->korName : null,
            ]);

            broadcast(
                new ProLogEvent($proLog->toArray(), $item->userId)
            );
        }

        return [$comment];
    }

    public function brainMove (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'destPosition' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return ['fail' => 'invalid parameter'];
        }

        $brain = Brain::find($request['id']);
        if (!$brain) {
            return ['fail' => 'id'];
        }

        $item = $brain->item();
        if (!$item) {
            return ['fail' => 'item'];
        }

        if (!$item->isPermitted(Auth::id())) {
            return ['fail' => 'permission denied'];
        }

        if (is_null($item->brain)) {
            $brain->delete();
            return ['fail' => 'brain list'];
        }
        else {
            $brainsNum = $item->brain;
            $len = count($item->brain);
            $dest = 0;
            if ((int)$request['destPosition'] >= $len) {
                $dest = $len - 1;
            }
            else if ((int)$request['destPosition'] < 0) {
                $dest = 0;
            }
            else {
                $dest = $request['destPosition'];
            }

            $key = array_search($brain->id, $brainsNum);
            array_splice($brainsNum, $key, 1);
            array_splice($brainsNum, $dest, 0, $brain->id);
            $item->brain = $brainsNum;

            $item->save();

            broadcast(
                new BrainEvent(['itemId' => $item->id, 'brain' => $item->brain], $item->card()->teamId ?? $item->card()->classObjectId,
                    $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                    'move')
            )->toOthers();

            return ['success' => true];
        }
    }

    public function brainUpdate (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],//id
            'type' => ['required', 'string', 'in:item,brain,delete'],//type
            'content' => ['required', 'string'],//content
            'color' => ['required', 'integer', 'min:1', 'max:8'],//color
        ]);

        if ($validator->fails()) {
            return ['fail' => 'invalid parameter'];
        }


        if ($request['type'] == 'item') {
            //추가
            $item = Item::find($request['id']);
            if (!$item) {
                return ['fail' => 'invalid id'];
            }

            if (!$item->isPermitted(Auth::id())) {
                return ['fail' => 'permission denied'];
            }

            $brain = Brain::create([
                'userId' => Auth::id(),
                'itemId' => $item->id,
                'content' => $request['content'],
                'color' => $request['color'],
            ]);

            $brainsNum = $item->brain ?? [];
            array_push($brainsNum, $brain->id);
            $item->brain = $brainsNum;

            $item->save();

            $brain->with_user = $brain->withUser()->first();

            broadcast(
                new BrainEvent(['itemId' => $item->id, 'brains' => $brain], $item->card()->teamId ?? $item->card()->classObjectId,
                    $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                    'add')
            )->toOthers();

            return ['brainId' => $brain->id];
        }
        else if ($request['type'] == 'brain') {
            //수정
            $brain = Brain::find($request['id']);
            if (!$brain) {
                return ['fail' => 'invalid id'];
            }

            if ($brain->userId != Auth::id()) {
                return ['fail' => 'permission denied'];
            }

            $item = $brain->item();
            if (!$item) {
                $brain->delete();
                return ['fail' => 'invalid item relation'];
            }

            $brain->content = $request['content'];
            $brain->color = $request['color'];

            $item->save();
            $brain->save();

            $brain->with_user = $brain->withUser()->first();

            broadcast(
                new BrainEvent(['itemId' => $item->id, 'brains' => $brain], $item->card()->teamId ?? $item->card()->classObjectId,
                    $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                    'update')
            )->toOthers();

            return ['success' => true];
        }
        else {
            //삭제
            $brain = Brain::find($request['id']);
            if (!$brain) {
                return ['fail' => 'invalid id'];
            }

            $item = $brain->item();
            if ($brain->userId != Auth::id()) {
                return ['fail' => 'permission denied'];
            }
            $brainsNum = $item->brain;
            $key = array_search($brain->id, $brainsNum);
            if (!is_bool($key)) {
                array_splice($brainsNum, $key, 1);
            }
            $brainId = $brain->id;
            $brain->delete();
            $item->brain = $brainsNum;
            $item->save();

            broadcast(
                new BrainEvent(['itemId' => $item->id, 'brainId' => $brainId], $item->card()->teamId ?? $item->card()->classObjectId,
                    $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                    'del')
            )->toOthers();

            return ['success' => true];
        }
    }
}

