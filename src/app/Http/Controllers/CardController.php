<?php

namespace App\Http\Controllers;

use App\Events\CardEvent;
use http\Params;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MyPage;
use App\Models\Card;
use App\Models\MyClass;
use App\Models\Team;
use App\Models\Consulting;
use App\Models\ConsultingApply;
use App\Models\Expert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    //api v1
    public function createCard (Request $request) {
        $validatedData = $request->validate([
            'myPageId' => ['required'],
            'type' => ['integer', 'min:0', 'max:4'],
            'title' => ['required', 'string'],
            'apply' => ['string'],
            'btnCardTitle' => ['string'],
        ]);

        $myPage = MyPage::find($request->myPageId);
        if (!$myPage) {
            return false;
        }
        if ($myPage->user()->id != Auth::id()) {
            return false;
        }

        $create = [];
        $create['myPageId'] = $request['myPageId'];
        $create['title'] = $request['title'];
        $create['userId'] = Auth::id();
        if ($request->has('type')) {
            $create['type'] = $request['type'];
        }
        if ($request->has('apply')) {
            $create['apply'] = $request['apply'];
        }
        if ($request->has('btnCardTitle')) {
            $create['btnCardTitle'] = $request['btnCardTitle'];
        }

        $card = Card::create($create);

        if (!$myPage->cardsNum) {
            $myPage->cardsNum = [$card->id];
        }
        else {
            $cardsNum = $myPage->cardsNum;
            array_push($cardsNum, $card->id);
            $myPage->cardsNum = $cardsNum;
        }
        $myPage->version = $myPage->version + 1;
        $myPage->save();

        return json_encode($card);
    }

    public function removeCard (Request $request) {
        $validatedData = $request->validate([
            'id' => ['required'],
        ]);

        $card = Card::find($request['id']);
        if (!$card) {
            return false;
        }
        if ($card->myPage()->user()->id != Auth::id()) {
            return false;
        }

        $myPage = $card->myPage();

        $cardsNum = $myPage->cardsNum;
        $key = array_search($request['id'], $cardsNum);
        array_splice($cardsNum, $key, 1);
        $myPage->cardsNum = $cardsNum;
        $myPage->version = $myPage->version + 1;

        foreach ($card->items() as $item) {
            $files = [];
            foreach ($item->itemFileList() as $image) {
                $files[] = $image->pathName;
            }
            Storage::disk('upload')->delete($files);
        }

        $myPage->save();

        $card->delete();

        return true;
    }

    public function updateCard (Request $request) {
//        $validatedData = $request->validate([
//
//        ]);

//        return '['.$request['sourcePosition'].', '.$request['destinationPosition'].']';

        //move
        if ($request->has('destinationPosition') && $request->has('sourcePosition') && $request->has('myPageId')) {
//            $card = Card::find($request['id']);
//            if (!$card) {
//                return false;
//            }

            $myPage = MyPage::find($request['myPageId']);
            if (!$myPage) {
                return false;
            }
            if ($myPage->user()->id != Auth::id()) {
                return false;
            }

            $cardsNum = $myPage->cardsNum;

            $sourcePosition = $request['sourcePosition'];
            if (count($cardsNum) < $sourcePosition) {
                return false;
            }
            else if ($sourcePosition < 0) {
                return false;
            }
            $key = $sourcePosition;//array_search($request['id'], $cardsNum);
            $cardId = $cardsNum[$key];
            array_splice($cardsNum, $key, 1);

            $destinationPosition = $request['destinationPosition'];
            if (count($cardsNum) < $destinationPosition) {
                return false;
            }
            else if ($destinationPosition < 0) {
                return false;
            }
            array_splice($cardsNum, $destinationPosition, 0, $cardId);
            $myPage->cardsNum = $cardsNum;

            $myPage->version = $myPage->version + 1;
            $myPage->save();

            return true;
        }
        else if ($request->has('id') && $request->has('title')) {
            $card = Card::find($request['id']);
            if (!$card) {
                return false;
            }
            if ($card->myPage()->userId != Auth::id()) {
                return false;
            }

            $card->title = $request['title'];
            $card->save();

            return true;
        }

        return false;
    }

    public function fetchCard (Request $request) {
        $myPage = MyPage::where('userId', Auth::id())->first();
        if (!$myPage) {
            return false;
        }

        $cards = $myPage->cards();
        if ($cards->count() <= 0) {
            return false;
        }

        return json_encode(['cardsNum' => $myPage->cardsNum, 'cards' => $cards]);
    }


    //api v2
    public function fetch (Request $request) {
        $validator = Validator::make($request->all(), [
            'subTypes' => ['required', 'string', 'in:id,myPage,classObject,team'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('subTypes')) {
                return ['fail' => 'validate error: subTypes'];
            }
        }

        switch ($request['subTypes']) {
            case 'id':
                return $this->fetchById($request);
            case 'myPage':
                if (in_array(Auth::user()->authority, [4])) {
                    return $this->fetchByConsultantMyPage($request);
                }
                else {
                    return $this->fetchByMyPage($request);
                }
            case 'classObject':
                return $this->fetchByClassObject($request);
            case 'team':
                return $this->fetchByTeam($request);
        }
    }
    private function fetchById (Request $request) {
        $card = Card::find($request['id']);
        if (!$card) {
            return ['fail' => 'cardId'];
        }

        if (!$card->isPermitted(Auth::id())) {
            return ['fail' => 'permission denied'];
        }

        return $card;
    }
    private function fetchByMyPage (Request $request) {
        $myPage = MyPage::find($request['id']);
        if (!$myPage) {
            return ['fail' => 'myPageId'];
        }

        if ($myPage->userId != Auth::id()) {
            return ['fail' => 'permission denied'];
        }

        $data = [];
        $data['cardsNum'] = $myPage->cardsNum;
        $data['cards'] = $myPage->cards();

        $cards = $data['cards']->toArray();

//        $applyCard = $data['cards']->where('type', 0)->first();

//        foreach ($cards as $key=>$card) {
//            if ($card['type'] == 3) {
//                $itemsNum = [];
//
//                foreach ($applyCard->withItems as $item) {
//                    //
//                    $classApply = $item->classApply()->first();
//                    if ($classApply && $item->type == 4 && $classApply->state != 'wait') {
//                        $withItems[] = $item;
//                        $itemsNum[] = $item->id;
//                    }
//                }
//
//                $itemsNum = collect($itemsNum);
//                $itemsNum = $itemsNum->sortDesc();
//                $itemsNum = $itemsNum->values()->all();
//
//                $cards[$key]['itemsNum'] = $itemsNum;
//            }
//        }

        $data['cards'] = $cards;


        return $data;
    }
    private function fetchByClassObject (Request $request) {
        $myClass = MyClass::where('classObjectId', $request['id'])->first();
        if (!$myClass) {
            return ['fail' => 'classObjectId'];
        }

        if (!$myClass->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $data = [];
        $data['cardsNum'] = $myClass->cardsNum;
        $data['cards'] = $myClass->exclusiveCards()->toArray();

//        $problem = $data['cards']->where('type', 10)->first();

        $expert = null;
        if (Auth::user()->authority == 5) {
            $expert = Expert::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first();
            if (!$expert) {
                return ['fail' => 'permission denied'];
            }
        }
        foreach ($data['cards'] as $key=>$card) {
            $cards = [];
            if ($card['type'] == 7/*성찰*/) {
                $cards = collect([]);

                if ($myClass->onTeamAccess || Auth::user()->authority == 2 || Auth::user()->authority == 5) {
                    $cards = Card::where('classObjectId', $card['classObjectId'])->where('type', 7)->whereNotNull('teamId')->with(['withItems' => function ($query) {
                        $query->where('type', 2)->with('withCard');
                    }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
                }
                else if (Auth::user()->authority == 1) {
                    $_cards = Card::where('classObjectId', $card['classObjectId'])->where('type', 7)->whereNotNull('teamId')
                        ->with(['withItems' => function ($query) {
                            $query->where('userId', Auth::id())->where('type', 2)->with('withCard');
                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();

                    $cards = $cards->merge($_cards);
                }
//                else {
//                    $cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->where('teamId', null)->with(['withItems' => function ($query) {
//                            $query->where('type', 2);
//                        }])->orderBy('id', 'asc')->get();
//                }
            }
            else if ($card['type'] == 10/*문제분석*/) {
                $cards = Card::where('classObjectId', $card['classObjectId'])->whereNull('teamId')
                    ->with(['withItems' => function ($query) {
                        $query->where('type', 1)->with('withCard');
                    }])->orderBy('id', 'asc')->get();

                if ($myClass->onTeamAccess || Auth::user()->authority == 2 || Auth::user()->authority == 5) {
                    $_cards = Card::where('classObjectId', $card['classObjectId'])->where('type', 10)->whereNotNull('teamId')
                        ->with(['withItems' => function ($query) {
                            $query->where('type', 1)->with('withCard');
                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();

                    $cards = $cards->merge($_cards);
                }
                else if (Auth::user()->authority == 1) {
                    $_cards = Card::where('classObjectId', $card['classObjectId'])->where('type', 10)->whereNotNull('teamId')
                        ->with(['withItems' => function ($query) {
                            $query->where('userId', Auth::id())->where('type', 1)->with('withCard');
                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();

                    $cards = $cards->merge($_cards);
                }
//                else {
//                    $cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->where('teamId', null)->with(['withItems' => function ($query) {
//                            $query->where('type', 1);
//                        }])->orderBy('id', 'asc')->get();
//                }
            }
            else if ($card['type'] == 9/*팀활동*/) {
                $cards = collect([]);

                if ($myClass->onTeamAccess || Auth::user()->authority == 2 || Auth::user()->authority == 5) {
                    $cards = Card::where('classObjectId', $card['classObjectId'])->where('type', 9)->whereNotNull('teamId')
                        ->with(['withItems' => function ($query) {
                            $query->where('type', 5)->with('withCard');
                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();
                }
                else if (Auth::user()->authority == 1) {
                    $_cards = Card::where('classObjectId', $card['classObjectId'])->where('type', 9)->whereNotNull('teamId')
                        ->with(['withItems' => function ($query) {
                            $query->where('userId', Auth::id())->where('type', 5)->with('withCard');
                        }])->orderBy('id', 'asc')->orderBy('teamId', 'asc')->get();

                    $cards = $cards->merge($_cards);
                }
//                else {
//                    $cards = Card::where('classObjectId', $card['classObjectId'])
//                        ->where('teamId', null)->with(['withItems' => function ($query) {
//                            $query->where('type', 5);
//                        }])->orderBy('id', 'asc')->get();
//                }
            }

            if (count($cards) > 0 && ($card['type'] == 10 || $card['type'] == 7 || $card['type'] == 9)) {
                $_itemsNum = [];
                $_withItems = [];
                foreach ($cards as $c) {
                    if (!is_null($c->itemsNum)) {
                        foreach ($c->itemsNum as $itemNum) {
                            $item = $c->withItems->where('id', $itemNum)->first();
                            if ($item) {
                                $_itemsNum[] = $item->id;
                                $_withItems[] = $item;
                            }
                        }
                    }
                }

                $data['cards'][$key]['itemsNum'] = $_itemsNum;
                $data['cards'][$key]['with_items'] = $_withItems;
            }
        }

//        $data['cards']->each(function ($card, $key) use($myClass, $data) );

        return $data;
    }
    private function fetchByTeam (Request $request) {
        $team = Team::find($request['id']);
        if (!$team) {
            return ['fail' => 'teamId'];
        }
        if (!$team->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $data = [];
        $data['cardsNum'] = $team->cardsNum;
        $data['cards'] = $team->cards()->toArray();

//        foreach ($data['cards'] as $key=>$card) {
//            $cards = [];
//            if ($card['type'] == 10) {
//                $cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
//                    $query->where('type', 1);
//                }])->get();
//            }
//            else if ($card['type'] == 7) {
//                $cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
//                    $query->where('type', 2);
//                }])->get();
//            }
//            else if ($card['type'] == 9) {
//                $cards = Card::where('teamId', $card['teamId'])->with(['withItems' => function ($query) {
//                    $query->where('type', 5);
//                }])->get();
//            }
//
//            if ($card['type'] == 10 || $card['type'] == 7 || $card['type'] == 9) {
//                $_itemsNum = [];
//                $_with_items = [];
//                foreach ($cards as $c) {
//                    if (!is_null($c->itemsNum)) {
//                        foreach ($c->itemsNum as $itemNum) {
//                            $item = $c->withItems->where('id', $itemNum)->first();
//                            if ($item) {
//                                $_itemsNum[] = $item->id;
//                                $_with_items[] = $item;
//                            }
//                        }
//                    }
//                }
//
//                $data['cards'][$key]['itemsNum'] = $_itemsNum;
//                $data['cards'][$key]['with_items'] = $_with_items;
//            }
//        }

        return $data;
    }
    private function fetchByConsultantMyPage (Request $request) {
        $consultingApply = ConsultingApply::find($request['id']);
        if (!$consultingApply) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        $consulting = $consultingApply->consulting()->first();
        if (!$consulting) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        if ($consulting->consultantId != Auth::id()) {
            return redirect()->route('consultantMyPageConsultingView');
        }

        if (strtotime($consulting->startDateTime) > strtotime('now')) {
            return false;
        }

        if (strtotime($consulting->endDateTime) < strtotime('now')) {
            return false;
        }

        $user = User::find($consultingApply->userId);
        if (!$user) {
            return ['fail' => 'user'];
        }

        if ($user->authority != 2) {
            return ['fail' => 'permission denied'];
        }

        $myPage = MyPage::where('userId', $consultingApply->userId)->first();
        if (!$myPage) {
            return ['fail' => 'myPageId'];
        }

        $data = [];
        $data['cardsNum'] = $myPage->cardsNum;
        $data['cards'] = $myPage->cards();

        $cards = $data['cards']->toArray();

        $applyCard = $data['cards']->where('type', 0)->first();

        foreach ($cards as $key=>$card) {
            if ($card['type'] == 3) {
                $itemsNum = [];

                foreach ($applyCard->withItems as $item) {
                    //
                    if ($item->type == 4 && $item->classApply()->first()->state != 'wait') {
                        $withItems[] = $item;
                        $itemsNum[] = $item->id;
                    }
                }

                $itemsNum = collect($itemsNum);
                $itemsNum = $itemsNum->sortDesc();
                $itemsNum = $itemsNum->values()->all();

                $cards[$key]['itemsNum'] = $itemsNum;
            }
        }

        $data['cards'] = $cards;


        return $data;
    }

    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'subTypes' => ['required', 'string', 'in:myPage,classObject,team'],
            'type' => ['required', 'integer', 'min:0', 'max:12'],
            'title' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('subTypes')) {
                return ['fail' => 'validate error: subTypes'];
            }
            else if ($errors->has('type')) {
                return ['fail' => 'validate error: type'];
            }
            else if ($errors->has('title')) {
                return ['fail' => 'validate error: title'];
            }
        }

        switch ($request['subTypes']) {
            case 'myPage':
                return $this->createByMyPage($request);
            case 'classObject':
                $data = $this->createByClassObject($request);

                if ($data[0]) {
                    broadcast(
                        new CardEvent($data[0], $data[0]->classObjectId, 'MYCLASS', 'add')
                    )->toOthers();
                }
                return $data;
            case 'team':
                $data = $this->createByTeam($request);

                if ($data[0]) {
                    broadcast(
                            new CardEvent($data[0], $data[0]->teamId, 'MYTEAM', 'add')
                    )->toOthers();
                }
                return $data;
        }
    }
    private function createByMyPage (Request $request) {
        $myPage = MyPage::find($request['id']);

        if (Auth::user()->authority == 4) {
            $consultingApply = ConsultingApply::find($request['id']);
            if (!$consultingApply) {
                return ['fail' => 'permission denied'];
            }

            $myPage = MyPage::where('userId', $consultingApply->userId)->first();
            if (!$myPage) {
                return ['fail' => 'myPage'];
            }

            $user = User::find($consultingApply->userId);
            if (!$user) {
                return ['fail' => 'user'];
            }

            if ($user->autority != 2) {
                return ['fail' => 'permission denied'];
            }

            if ($consultingApply->userId != $myPage->userId) {
                return ['fail' => 'permission denied'];
            }

            $consulting = $consultingApply->consulting()->first();
            if (!$consulting) {
                return ['fail' => 'permission denied'];
            }

            if ($consulting->consultantId != Auth::id()) {
                return false;
            }

            if (strtotime($consulting->startDateTime) > strtotime('now')) {
                return ['fail' => 'permission denied'];
            }
            if (strtotime($consulting->endDateTime) < strtotime('now')) {
                return ['fail' => 'permission denied'];
            }
        }
        else if (!$myPage) {
            return ['fail' => 'myPage'];
        }
        else if ($myPage->userId != Auth::id()) {
            return ['fail' => 'permission denied'];
        }

        $card = Card::create([
            'userId' => Auth::id(),
            'myPageId' => $myPage->id,
            'type' => $request['type'],
            'title' => $request['title']
        ]);

        $cardsNum = $myPage->cardsNum;
        $cardsNum[] = $card->id;
        $myPage->cardsNum = $cardsNum;

        $myPage->save();

        if ($card) {
            return [$card];
        }
        else {
            return ['fail' => 'create card'];
        }
    }
    private function createByClassObject (Request $request) {
        $myClass = MyClass::where('classObjectId', $request['id'])->first();
        if (!$myClass) {
            return ['fail' => 'classObject'];
        }
        if (!$myClass->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $card = Card::create([
            'userId' => Auth::id(),
            'classObjectId' => $myClass->classObjectId,
            'type' => $request['type'],
            'title' => $request['title']
        ]);

        $cardsNum = $myClass->cardsNum;
        $cardsNum[] = $card->id;
        $myClass->cardsNum = $cardsNum;

        $myClass->save();

        if ($card) {
            return [$card];
        }
        else {
            return ['fail' => 'create card'];
        }
    }
    private function createByTeam (Request $request) {
        $team = Team::find($request['id']);
        if (!$team) {
            return ['fail' => 'team'];
        }
        if (!$team->isPermitted()) {
            return ['fail' => 'permission denied'];
        }

        $card = Card::create([
            'userId' => Auth::id(),
            'classObjectId' => $team->classObjectId,
            'teamId' => $team->id,
            'type' => $request['type'],
            'title' => $request['title']
        ]);

        $cardsNum = $team->cardsNum;
        $cardsNum[] = $card->id;
        $team->cardsNum = $cardsNum;

        $team->save();

        if ($card) {
            return [$card];
        }
        else {
            return ['fail' => 'create card'];
        }
    }

    public function createChatCard (Request $request) {
        $validator = Validator::make($request->all(), [
            'subTypes' => ['required', 'string', 'in:team,classObject'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('subTypes')) {
                return ['fail' => 'validate error: subTypes'];
            }
        }

        $card = null;
        $subTypes = $request['subTypes'];
        if ($subTypes == 'team') {
            $team = Team::find($request['id']);
            if (!$team) {
                return ['fail' => 'invalid parameter'];
            }
            if (!$team->isPermitted()) {
                return ['fail' => 'permission denied'];
            }

            if ($team->onTeamTalk) {
                return ['fail' => 'already exists'];
            }

            $card = Card::create([
                'title' => '팀톡',
                'userId' => Auth::id(),
                'classObjectId' => $team->classObjectId,
                'teamId' => $team->id,
                'type' => 12
            ]);

            if (!$card) {
                return ['fail' => true];
            }

            if (is_null($team->cardsNum)) {
                $team->cardsNum = [];
            }
            $cardsNum = $team->cardsNum;
            array_unshift($cardsNum, $card->id);
            $team->cardsNum = $cardsNum;

            $team->onTeamTalk = '1';
            $team->save();
        }
        else {
            $myClass = MyClass::where('classObjectId', $request['id'])->first();
            if (!$myClass) {
                return ['fail' => 'invalid parameter'];
            }

            if ($myClass->userId != Auth::id()) {
                return ['fail' => 'permission denied'];
            }

            if ($myClass->onClassTalk) {
                return ['fail' => 'already exists'];
            }

            $card = Card::create([
                'title' => '수업공지',
                'userId' => Auth::id(),
                'classObjectId' => $myClass->classObjectId,
                'type' => 5
            ]);

            if (!$card) {
                return ['fail' => true];
            }

            if (is_null($myClass->cardsNum)) {
                $myClass->cardsNum = [];
            }
            $cardsNum = $myClass->cardsNum;
            array_unshift($cardsNum, $card->id);
            $myClass->cardsNum = $cardsNum;

            $myClass->onClassTalk = '1';
            $myClass->save();
        }

        if (!$card) {
            return ['fail' => 'unknown error'];
        }

        return [$card];
    }

    public function delete (Request $request) {
        if (Auth::user()->authority == 4) {
            return ['fail' => 'permission denied'];
        }

        $card = Card::find($request['id']);
        if (!$card) {
            return ['fail' => 'card'];
        }

        $parent = null;
        switch ($request['subTypes']) {
            case 'myPage':
                $parent = MyPage::find($card->myPageId);
                if (!$parent) {
                    return ['fail' => 'parent'];
                }
                break;
            case 'classObject':
                $parent = MyClass::where('classObjectId', $card->classObjectId)->first();
                if (!$parent) {
                    return ['fail' => 'parent'];
                }
                break;
            case 'team':
                $parent = Team::find($card->teamId);
                if (!$parent) {
                    return ['fail' => 'parent'];
                }
                break;
            default:
                return ['fail' => 'validate error: subTypes'];
        }

        //자기가 만든 카드만 삭제할수 있게 임시 수정
        if ($card->myPageId == $parent->id && $parent->userId == Auth::id()) {
            //ok
        }
        else if ($card->userId != Auth::id()) {
            return ['fail' => 'permission denied'];
        }

        //권한 체크

        $cardsNum = $parent->cardsNum;
        $key = array_search($card->id, $cardsNum);
        array_splice($cardsNum, $key, 1);
        $parent->cardsNum = $cardsNum;

        foreach ($card->items() as $item) {
            $comments = $item->getComments();
            $files = [];
            foreach ($comments as $comment) {
                $files[] = 'comment/'.$comment->filePathName;
            }
            foreach ($item->itemFileList() as $image) {
                $files[] = 'images/'.$image->pathName;
            }
            foreach ($item->itemFiles() as $file) {
                $files[] = 'files/'.$file->pathName;
            }
            Storage::disk('local')->delete($files);
        }

        $card->delete();
        $parent->save();
        $parent->refresh();

        if ($parent && $request['subTypes'] != 'myPage') {
            broadcast(
                new CardEvent($request['id'], $request['subTypes'] == 'team' ?
                    $parent->id : $parent->classObjectId,
                    $request['subTypes'] == 'team' ? 'MYTEAM' : 'MYCLASS',
                    'del')
            )->toOthers();
        }

        return ['success' => true];
    }

    public function update (Request $request) {
        $validator = Validator::make($request->all(), [
            'subTypes' => ['required', 'string', 'in:title,myPage,classObject,team'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('subTypes')) {
                return ['fail' => 'subTypes'];
            }
        }

        if ($request['subTypes'] == 'title') { //edit title
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                if ($errors->has('title')) {
                    return ['fail' => 'title'];
                }
            }

            $card = Card::find($request['id']);
            if (!$card) {
                return ['fail' => 'id'];
            }

            //카드 타입 4번이 일반카드 고정카드들은 이름 수정 불가능
            if ($card->type != 4) {
                return ['fail' => 'permission denied'];
            }

            //현재 임시로 작성자만 삭제 가능함
            if (!is_null($card->myPageId) && in_array(Auth::user()->authority, [1, 2])) {
                $myPage = MyPage::find($card->myPageId);
                if (!$myPage) {
                    return ['fail' => 'my page'];
                }

                if ($myPage->userId != Auth::id()) {
                    return ['fail' => 'permission denied'];
                }
            }
            else if (Auth::user()->authority == 4) {
                if ($card->isPermitted(Auth::id(), $request['subId'])) {
                    if ($card->userId != Auth::id()) {
                        return ['fail' => 'permission denied'];
                    }
                }
                else {
                    return ['fail' => 'permission denied'];
                }
            }
            else if ($card->userId != Auth::id()) {
                return ['fail' => 'permission denied'];
            }

            $card->title = $request['title'];
            $card->save();
            $card->refresh();

            if ($card && is_null($card->myPageId)) {
                broadcast(
                    new CardEvent($card, $card->teamId ? $card->teamId : $card->classObjectId, $card->teamId ? 'MYTEAM' : 'MYCLASS', 'update')
                )->toOthers();
            }

            return ['success' => true];
        }
        //move
        else {
            $validator = Validator::make($request->all(), [
                'subId' => ['required', 'integer', 'min:1'],
                'destinationPosition' => ['required', 'integer', 'min:0'],
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();

                if ($errors->has('subId')) {
                    return ['fail' => 'subId'];
                }
                else if ($errors->has('destinationPosition')) {
                    return ['fail' => 'destinationPosition'];
                }
            }

            $card = Card::find($request['id']);
            if (!$card) {
                return ['fail' => 'card'];
            }
//            if ($card->type != 4) {
//                return ['fail' => 'invalid move'];
//            }
            if (!$card->isPermitted(Auth::id(), Auth::user()->authority == 4 ? $request['subId'] : null)) {
                return ['fail' => 'permission denied'];
            }

            $parent = null;
            switch ($request['subTypes']) {
                case 'myPage':
                    if (Auth::user()->authority == 4) {
                        $consultantApply = ConsultingApply::find($request['subId']);
                        if (!$consultantApply) {
                            return ['fail' => 'parent'];
                        }

                        $userId = $consultantApply->userId;

                        $parent = MyPage::where('userId', $userId)->first();
                    }
                    else {
                        $parent = MyPage::find($request['subId']);
                    }
                    break;
                case 'classObject':
                    $parent = MyClass::where('classObjectId', $request['subId'])->first();
                    break;
                case 'team':
                    $parent = Team::find($request['subId']);
                    break;
                default:
                    break;
            }
            if (!$parent) {
                return ['fail' => 'parent'];
            }

            $key = $request['destinationPosition'];
            $cardsNum = $parent->cardsNum;
            if (!$cardsNum || (gettype($cardsNum) != 'array' && count($cardsNum) <= 0)) {
                return ['fail' => 'cardsNum is empty'];
            }

            $sourceKey = array_search($card->id, $cardsNum);
            if (gettype($sourceKey) == 'boolean') {
                return ['fail' => 'not matchable card with parent'];
            }
            array_splice($cardsNum, $sourceKey, 1);
            if ($key < 0) {
                $key = 0;
            }
            else if ($key > count($cardsNum)) {
                $key = count($cardsNum);
            }

            if ($key > count($cardsNum)) {
                return ['fail' => 'invalid move'];
            }
            array_splice($cardsNum, $key, 0, $card->id);

            $parent->cardsNum = $cardsNum;
            $parent->save();
            $parent->refresh();

            if ($parent && $request['subTypes'] != 'myPage') {
                broadcast(
                    new CardEvent($parent->cardsNum, $request['subTypes'] == 'team' ?
                        $parent->id : $parent->classObjectId,
                        $request['subTypes'] == 'team' ? 'MYTEAM' : 'MYCLASS', 'move')
                )->toOthers();
            }

            return ['success' => true];
        }
    }
}
