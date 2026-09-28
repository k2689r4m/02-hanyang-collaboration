<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyClass;
use App\Models\MyPage;
use App\Models\Card;
use App\Models\Item;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\ItemFileList;
use App\Models\Talk;
use App\Models\ClassList;
use App\Models\ProblemAnalysis;
use App\Models\ReflectionLog;
use App\Models\ClassApply;
use App\Models\User;
use App\Models\Comment;
//시연용 테스트
use App\Models\ClassObject;
//시연용 테스트 끝
use App\Events\ChattingEvent;
use App\Events\FixedCardItemEvent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestController extends Controller
{
    //
//    public function fetch (Request $request) {
////        return dd($request->all());
////        broadcast(
////            new ChattingEvent(Talk::find(2), 'MYCLASS', 1)
////        )->toOthers();
//
//        //유효성 체크
//        $validator = Validator::make($request->all(), [
//            'id' => ['required', 'integer', 'min:1'],
//            'types' => ['required', 'string', 'in:card,item,user,chat,myClass,comment'],
//            'subTypes' => ['required', 'string', 'in:id,myPage,classObject,team,card,comment,item'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('id')) {
//                return ['fail' => 'id'];
//            }
//            else if ($errors->has('types')) {
//                return ['fail' => 'types'];
//            }
//            else if ($errors->has('subTypes')) {
//                return ['fail' => 'subTypes'];
//            }
//        }
//
//        $data = ['fail' => 'unknown error'];
//        if ($request['types'] == 'item') {
//            $item = Item::find($request['id']);
//            if (!$item) {
//                return ['fail' => 'id'];
//            }
//
//            if (!$item->isPermitted(Auth::id())) {
//                return ['fail' => 'permission denied'];
//            }
//
////            if (!is_null($item->type) && $item->type != 0) {
////                if (!is_null($item->card()->classObjectId) && !is_null($item->card()->teamId) && is_null($item->card()->myPageId)) {
////                    //팀 일때
////                    $myClass = $item->card()->myClass();
////                    if (!$myClass) {
////                        return ['fail' => 'permission denied'];
////                    }
////
////                    if (!$myClass->onTeamAccess) {
////                        if (!$item->card()->isPermitted(Auth::id())) {
////                            return ['fail' => 'permission denied'];
////                        }
////                    }
////                }
////                else {
////                    if (!$item->card()->isPermitted(Auth::id())) {
////                        return ['fail' => 'permission denied'];
////                    }
////                }
////            }
////            else if (!is_null($item->type) && $item->type == 0) {
////                if (!$item->card()->isPermitted(Auth::id())) {
////                    return ['fail' => 'permission denied'];
////                }
////            }
////            else {
////                return ['fail' => 'invalid request'];
////            }
//
//            $item->with_images = $item->withImages()->get();
//            $item->item_file_list = $item->itemFileList();
//            $item->with_comments = $item->withComments()->get();
//
//            switch($item->type) {
//                case 1:
//                    $item->with_problem_analysis = $item->withProblemAnalysis()->first();
//                    break;
//                case 2:
//                    $item->with_reflection_log = $item->withReflectionLog()->first();
//                    break;
//                case 4:
//                    $item->with_class_apply = $item->withClassApply()->first();
//                    break;
//                default:
//                    break;
//            }
//
//            return [$item];
//        }
//        else if ($request['types'] == 'card') {
//            switch ($request['subTypes']) {
//                case 'id':
//                    $card = Card::find($request['id']);
//                    if (!$card) {
//                        return ['fail' => 'cardId'];
//                    }
//
//                    if (!$card->isPermitted(Auth::id())) {
//                        return ['fail' => 'permission denied'];
//                    }
//
//                    return $card;
//                case 'myPage':
//                    $myPage = MyPage::find($request['id']);
//                    if (!$myPage) {
//                        return ['fail' => 'myPageId'];
//                    }
//
//                    if ($myPage->userId != Auth::id()) {
//                        return ['fail' => 'permission denied'];
//                    }
//
//                    $data = [];
//                    $data['cardsNum'] = $myPage->cardsNum;
//                    $data['cards'] = $myPage->cards();
//
//                    if (gettype($data['cardsNum']) === 'array' &&
//                        gettype($data['cards']) === 'array' &&
//                        count($data['cardsNum']) != count($data['cards'])) {
//                        return ['fail' => 'invalid database'];
//                    }
//                    break;
//                case 'classObject':
//                    $myClass = MyClass::where('classObjectId', $request['id'])->first();
//                    if (!$myClass) {
//                        return ['fail' => 'classObjectId'];
//                    }
//
//                    if (!($myClass->isMember(Auth::id()) || $myClass->userId == Auth::id())) {
//                        return ['fail' => 'permission denied'];
//                    }
//
//                    $data = [];
//                    $data['cardsNum'] = $myClass->cardsNum;
//                    $data['cards'] = $myClass->exclusiveCards();
//
//                    foreach($data['cards'] as $cardData) {
//                        if ($cardData->type == 10) {
//                            //문제분석
////                            $cards = Card::where('classObjectId', $cardData->classObjectId)->whereNotNull('teamId')->with(['withItems' => function ($query) {
////                                $query->where('type', 1);
////                            }])->get();
//                            $cards = [];
//                            if ($myClass->onTeamAccess) {
//                                $cards = Card::where('classObjectId', $cardData->classObjectId)->with(['withItems' => function ($query) {
//                                    $query->where('type', 1);
//                                }])->get();
//                            }
//                            else {
//                                if (Auth::user()->authority == 2 && $myClass->userId == Auth::id()) {
//                                    $cards = Card::where('classObjectId', $cardData->classObjectId)->with(['withItems' => function ($query) {
//                                        $query->where('type', 1);
//                                    }])->get();
//                                }
//                                else if (Auth::user()->authority == 1) {
//                                    $cards = Card::where('classObjectId', $cardData->classObjectId)
//                                        ->with(['withItems' => function ($query) {
//                                            $query->where('type', 1);
//                                        }])->get();
//
//                                    $_cards = [];
//
//                                    foreach ($cards as $card) {
//                                        if ($card->isPermitted(Auth::id())) {
//                                            $_cards[] = $card;
//                                        }
//                                    }
//
//                                    $cards = $_cards;
//                                }
//                            }
//
//                            foreach ($cards as $c) {
//                                if ($c->withItems) {
//                                    foreach ($c->withItems as $i) {
//                                        if ($cardData->itemsNum) {
//                                            $_itemsNum = $cardData->itemsNum;
//                                            $_itemsNum[] = $i->id;
//                                            $cardData->itemsNum = $_itemsNum;
//                                        }
//                                        else {
//                                            $cardData->itemsNum = [$i->id];
//                                        }
//
//                                        if ($cardData->withItems) {
//                                            $_withItems = $cardData->withItems;
//                                            $_withItems[] = $i;
//                                            $cardData->with_items = $_withItems;
//                                        }
//                                        else {
//                                            $cardData->withItems = [$i];
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                        else if ($cardData->type == 7) {
//                            //성찰일지
////                            $cards = Card::where('classObjectId', $cardData->classObjectId)->whereNotNull('teamId')->with(['withItems' => function ($query) {
////                                $query->where('type', 2);
////                            }])->get();
//
//                            $cards = [];
//                            if ($myClass->onTeamAccess) {
//                                $cards = Card::where('classObjectId', $cardData->classObjectId)->with(['withItems' => function ($query) {
//                                    $query->where('type', 2);
//                                }])->get();
//                            }
//                            else {
//                                if (Auth::user()->authority == 2 && $myClass->userId == Auth::id()) {
//                                    $cards = Card::where('classObjectId', $cardData->classObjectId)->with(['withItems' => function ($query) {
//                                        $query->where('type', 2);
//                                    }])->get();
//                                }
//                                else if (Auth::user()->authority == 1) {
//                                    $cards = Card::where('classObjectId', $cardData->classObjectId)
//                                        ->with(['withItems' => function ($query) {
//                                            $query->where('type', 2);
//                                        }])->get();
//
//                                    $_cards = [];
//
//                                    foreach ($cards as $card) {
//                                        if ($card->isPermitted(Auth::id())) {
//                                            $_cards[] = $card;
//                                        }
//                                    }
//
//                                    $cards = $_cards;
//                                }
//                            }
//
////                            $cards = Card::where('classObjectId', $cardData->classObjectId)->with(['withItems' => function ($query) {
////                                $query->where('type', 2);
////                            }])->get();
//                            foreach ($cards as $c) {
//                                if ($c->withItems) {
//                                    foreach ($c->withItems as $i) {
//                                        if ($cardData->itemsNum) {
//                                            $_itemsNum = $cardData->itemsNum;
//                                            $_itemsNum[] = $i->id;
//                                            $cardData->itemsNum = $_itemsNum;
//                                        }
//                                        else {
//                                            $cardData->itemsNum = [$i->id];
//                                        }
//
//                                        if ($cardData->withItems) {
//                                            $_withItems = $cardData->withItems;
//                                            $_withItems[] = $i;
//                                            $cardData->with_items = $_withItems;
//                                        }
//                                        else {
//                                            $cardData->withItems = [$i];
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                    }
//
//                    if (gettype($data['cardsNum']) === 'array' &&
//                        gettype($data['cards']) === 'array' &&
//                        count($data['cardsNum']) != count($data['cards'])) {
//                        return ['fail' => 'invalid database'];
//                    }
//                    break;
//                case 'team':
//                    $team = Team::find($request['id']);
//                    if (!$team) {
//                        return ['fail' => 'teamId'];
//                    }
//                    if (!($team->myClass()->userId == Auth::id() || $team->isMember(Auth::id()))) {
//                        return ['fail' => 'permission denied'];
//                    }
//
//                    $data = [];
//                    $data['cardsNum'] = $team->cardsNum;
//                    $data['cards'] = $team->cards();
//
//                    if (gettype($data['cardsNum']) === 'array' &&
//                        gettype($data['cards']) === 'array' &&
//                        count($data['cardsNum']) != count($data['cards'])) {
//                        return ['fail' => 'invalid database'];
//                    }
//                    break;
//                default:
//                    break;
//            }
//        }
//        else if ($request['types'] == 'user') {
//            $parent = null;
//            if ($request['subTypes'] == 'classObject') {
//                $myClass = MyClass::where('classObjectId', $request['id'])->first();
//                if (!$myClass) {
//                    return ['fail' => 'invalid parameter'];
//                }
//                if (!($myClass->userId == Auth::id() || $myClass->isMember(Auth::id()))) {
//                    return ['fail' => 'permission denied'];
//                }
//
//
//                $parent = ClassList::where('classObjectId', $request['id'])->with(['user_info' => function ($query) {
//                    $query->select('id', 'name');
//                }])->get();
//            }
//            else if ($request['subTypes'] == 'team') {
//                $team = Team::find($request['id']);
//                $myClass = $team->myClass();
//
//                if (!$myClass) {
//                    return ['fail' => 'invalid parameter'];
//                }
//                else if (!($myClass->userId == Auth::id() || $team->isMember(Auth::id()))) {
//                    //지금은 일단 교수 이거나 팀 소속 일때만 팀원 받아올 수 있게 해놨음
//                    return ['fail' => 'permission denied'];
//                }
//
//                $parent = TeamMember::where('teamId', $request['id'])->with(['user_info' => function ($query) {
//                    $query->select('id', 'name');
//                }])->get();
//            }
//
//            if (!$parent) {
//                return ['fail' => 'parent'];
//            }
//
////            $users = $parent->user_info;
//            $user = [];
//            foreach ($parent as $p) {
//                $user[] = $p->user_info;
//            }
//
//            return $user;
//        }
//        else if ($request['types'] == 'myClass') {
//            $myClass = MyClass::find($request['id']);
//            if (!$myClass) {
//                return ['fail' => 'invalid parameter'];
//            }
//
//            if (!($myClass->userId == Auth::id() || $myClass->isMember(Auth::id()))) {
//                return ['fail' => 'permission denied'];
//            }
//
//            return [$myClass];
//        }
//        else if ($request['types'] == 'chat') {
//            $validator = Validator::make($request->all(), [
//                'subTypes' => ['required', 'string', 'in:classObject,team'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('subTypes')) {
//                    return ['fail' => 'subTypes'];
//                }
//            }
//
//            $card = Card::find($request['id']);
//            if (!$card) {
//                return ['fail' => 'invalid parameter'];
//            }
//
//
//            if ($request['subTypes'] == 'team') {
//                if ($card->type != 12) {
//                    return ['fail' => 'invalid parameter'];
//                }
//
//                if (!($card->teamId && $card->classObjectId)) {
//                    return ['fail' => 'invalid parameter'];
//                }
//
//                if (!$card->isPermitted(Auth::id())) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                return $card->messages()->sortBy('id')->values()->all();
//            }
//            else if ($request['subTypes'] == 'classObject') {
//                if ($card->type != 5) {
//                    return ['fail' => 'invalid parameter'];
//                }
//
//                if (!$card->classObjectId) {
//                    return ['fail' => 'invalid parameter'];
//                }
//
//                if (!$card->isPermitted(Auth::id())) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                return $card->messages()->sortBy('id')->values()->all();
//            }
//            else {
//                return ['fail' => 'unknown error'];
//            }
//        }
//        else if ($request['types'] == 'comment') {
//            //id: item->id
//            //types: comment
//
//            $item = Item::find($request['id']);
//            if (!$item) {
//                return ['fail' => 'id'];
//            }
//
////            $card = $item->card();
////            if (!$card) {
////                return ['fail' => 'card'];
////            }
//            if (!$item->isPermitted(Auth::id())) {
//                return ['fail' => 'permission denied'];
//            }
//
//            $data = $item->comments();
//        }
//
//        return $data;
//    }

//    public function classSetting (Request $request) {
////        return dd($request->all());
//
//        /**
//         *
//         * if type: setting
//         * id: classObjectId
//         * type: setting
//         *
//         */
//
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
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('id')) {
//                return ['fail' => 'id'];
//            }
//            else if ($errors->has('userId')) {
//                return ['fail' => 'userId'];
//            }
//            else if ($errors->has('classObjectId')) {
//                return ['fail' => 'classObjectId'];
//            }
//            else if ($errors->has('version')) {
//                return ['fail' => 'version'];
//            }
//            else if ($errors->has('cardsNum')) {
//                return ['fail' => 'cardsNum'];
//            }
//            else if ($errors->has('onClassTalk')) {
//                return ['fail' => 'onClassTalk'];
//            }
//            else if ($errors->has('onTeamTalk')) {
//                return ['fail' => 'onTeamTalk'];
//            }
//            else if ($errors->has('onOrientation')) {
//                return ['fail' => 'onOrientation'];
//            }
//            else if ($errors->has('onReflectionLog')) {
//                return ['fail' => 'onReflectionLog'];
//            }
//            else if ($errors->has('onEvaluation')) {
//                return ['fail' => 'onEvaluation'];
//            }
//            else if ($errors->has('onTeamActivity')) {
//                return ['fail' => 'onTeamActivity'];
//            }
//            else if ($errors->has('onProblemAnalysis')) {
//                return ['fail' => 'onProblemAnalysis'];
//            }
//            else if ($errors->has('onProblemAnalysis')) {
//                return ['fail' => 'onProblemAnalysis'];
//            }
//            else if ($errors->has('onTeamAccess')) {
//                return ['fail' => 'onTeamAccess'];
//            }
//            else if ($errors->has('onTeamOrientation')) {
//                return ['fail' => 'onTeamOrientation'];
//            }
//            else if ($errors->has('onSetting')) {
//                return ['fail' => 'onSetting'];
//            }
//        }
//
//        $data = ['fail' => 'unknown error'];
//        $myClass = MyClass::find($request['id']);
//        if (!$myClass) {
//            return ['fail' => 'id'];
//        }
//
//        if ($myClass->userId != Auth::id()) {
//            return ['fail' => 'permission denied'];
//        }
//
//        if ($myClass->classObjectId != $request['classObjectId']) {
//            return ['fail' => 'classObjectId'];
//        }
//
//        if ($myClass->onSetting) {
//            return ['fail' => 'already changed'];
//        }
//
//
//        $myClass->onReflectionLog = $request['onReflectionLog'];
//        if ($request['onReflectionLog'] == '1') {
//            $card = Card::create([
//                'title' => '성찰',
//                'userId' => Auth::id(),
//                'classObjectId' => $myClass->classObjectId,
//                'type' => 7
//            ]);
//
//            if (is_null($myClass->cardsNum)) {
//                $myClass->cardsNum = [];
//            }
//            $cardsNum = $myClass->cardsNum;
//            array_unshift($cardsNum, $card->id);
//            $myClass->cardsNum = $cardsNum;
//
//            $myClass->save();
//
//            $teams = Team::where('classObjectId', $myClass->classObjectId)->get();
//            foreach ($teams as $team) {
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 7)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '성찰',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 7,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
//            }
//        }
//        $myClass->onEvaluation = $request['onEvaluation'];
//        if ($request['onEvaluation'] == '1') {
//            $card = Card::create([
//                'title' => '평가',
//                'userId' => Auth::id(),
//                'classObjectId' => $myClass->classObjectId,
//                'type' => 8
//            ]);
//
//            if (is_null($myClass->cardsNum)) {
//                $myClass->cardsNum = [];
//            }
//            $cardsNum = $myClass->cardsNum;
//            array_unshift($cardsNum, $card->id);
//            $myClass->cardsNum = $cardsNum;
//
//            $myClass->save();
//
//            $teams = Team::where('classObjectId', $myClass->classObjectId)->get();
//            foreach ($teams as $team) {
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 8)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '평가',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 8,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
//            }
//        }
//        $myClass->onTeamActivity = $request['onTeamActivity'];
//        if ($request['onTeamActivity'] == '1') {
//            $card = Card::create([
//                'title' => '팀활동',
//                'userId' => Auth::id(),
//                'classObjectId' => $myClass->classObjectId,
//                'type' => 9,
//            ]);
//
//            if (is_null($myClass->cardsNum)) {
//                $myClass->cardsNum = [];
//            }
//            $cardsNum = $myClass->cardsNum;
//            array_unshift($cardsNum, $card->id);
//            $myClass->cardsNum = $cardsNum;
//
//            $myClass->save();
//
//            $teams = Team::where('classObjectId', $myClass->classObjectId)->get();
//            foreach ($teams as $team) {
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 9)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '팀활동',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 9,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
//            }
//        }
//        $myClass->onProblemAnalysis = $request['onProblemAnalysis'];
//        if ($request['onProblemAnalysis'] == '1') {
//            $card = Card::create([
//                'title' => '문제분석',
//                'userId' => Auth::id(),
//                'classObjectId' => $myClass->classObjectId,
//                'type' => 10
//            ]);
//
//            if (is_null($myClass->cardsNum)) {
//                $myClass->cardsNum = [];
//            }
//            $cardsNum = $myClass->cardsNum;
//            array_unshift($cardsNum, $card->id);
//            $myClass->cardsNum = $cardsNum;
//
//            $myClass->save();
//
//            $teams = Team::where('classObjectId', $myClass->classObjectId)->get();
//            foreach ($teams as $team) {
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 10)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '문제분석',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'type' => 10,
//                        'teamId' => $team->id,
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
//            }
//        }
//        $myClass->onOrientation = $request['onOrientation'];
//        if ($request['onOrientation'] == '1') {
//            $card = Card::create([
//                'title' => '오리엔테이션',
//                'userId' => Auth::id(),
//                'classObjectId' => $myClass->classObjectId,
//                'type' => 6
//            ]);
//
//            if (is_null($myClass->cardsNum)) {
//                $myClass->cardsNum = [];
//            }
//            $cardsNum = $myClass->cardsNum;
//            array_unshift($cardsNum, $card->id);
//            $myClass->cardsNum = $cardsNum;
//
//            $myClass->save();
//        }
//        $myClass->onTeamOrientation = $request['onTeamOrientation'];
//        if ($request['onTeamOrientation'] == '1') {
//            $teams = Team::where('classObjectId', $myClass->classObjectId)->get();
//            foreach ($teams as $team) {
//                $card = Card::where('classObjectId', $team->classObjectId)->where('teamId', $team->id)->where('type', 11)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '팀 오리엔테이션',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'teamId' => $team->id,
//                        'type' => 11
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->save();
//                }
//            }
//        }
//        if ($request['onClassTalk'] == '1') {
//            if ($myClass->onClassTalk == 0) {
//                $card = Card::create([
//                    'title' => '수업공지',
//                    'userId' => Auth::id(),
//                    'classObjectId' => $myClass->classObjectId,
//                    'type' => 5
//                ]);
//
//                if (is_null($myClass->cardsNum)) {
//                    $myClass->cardsNum = [];
//                }
//                $cardsNum = $myClass->cardsNum;
//                array_unshift($cardsNum, $card->id);
//                $myClass->cardsNum = $cardsNum;
//
//                $myClass->save();
//            }
//        }
//        $myClass->onClassTalk = $request['onClassTalk'];
//        if ($request['onTeamTalk'] == '1') {
//            $teams = $myClass->teams();
//
//            foreach ($teams as $team) {
//                $card = Card::where('classObjectId', $myClass->classObjectId)
//                    ->where('teamId', $team->id)
//                    ->where('type', 12)->first();
//                if (!$card) {
//                    $card = Card::create([
//                        'title' => '팀톡',
//                        'userId' => Auth::id(),
//                        'classObjectId' => $myClass->classObjectId,
//                        'teamId' => $team->id,
//                        'type' => 12
//                    ]);
//
//                    if (is_null($team->cardsNum)) {
//                        $team->cardsNum = [];
//                    }
//                    $cardsNum = $team->cardsNum;
//                    array_unshift($cardsNum, $card->id);
//                    $team->cardsNum = $cardsNum;
//
//                    $team->onTeamTalk = $request['onTeamTalk'];
//                    $team->save();
//                }
//                else {
//                    $card = null;
//                }
//            }
//        }
//        $myClass->onTeamTalk = $request['onTeamTalk'];
//        /////////////////////////////////////////////////////////////
//        $myClass->onTeamAccess = $request['onTeamAccess'];
//
//        $myClass->onSetting = true;
//
//        $myClass->save();
//
//        $myClass->refresh();
//
//        return ['success' => true];
//    }

//    public function create (Request $request) {
////        return dd($request->all());
//        $validator = Validator::make($request->all(), [
//            'id' => ['required', 'integer', 'min:1'],
//            'types' => ['required', 'string', 'in:card,item,talk'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('id')) {
//                return ['fail' => 'id'];
//            }
//            else if ($errors->has('types')) {
//                return ['fail' => 'types'];
//            }
//        }
//
//        if ($request['types'] == 'card') {
//            $validator = Validator::make($request->all(), [
//                'subTypes' => ['required', 'string', 'in:myPage,classObject,team'],
//                'type' => ['required', 'integer', 'min:0', 'max:12'],
//                'title' => ['required', 'string'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('subTypes')) {
//                    return ['fail' => 'subTypes'];
//                }
//                else if ($errors->has('myPageId')) {
//                    return ['fail' => 'myPageId'];
//                }
//                else if ($errors->has('classObjectId')) {
//                    return ['fail' => 'classObjectId'];
//                }
//                else if ($errors->has('teamId')) {
//                    return ['fail' => 'teamId'];
//                }
//                else if ($errors->has('type')) {
//                    return ['fail' => 'type'];
//                }
//                else if ($errors->has('title')) {
//                    return ['fail' => 'title'];
//                }
//            }
//
//            $parent = null;
//            switch ($request['subTypes']) {
//                case 'myPage':
//                    $parent = MyPage::find($request['id']);
//                    if (!$parent) {
//                        return ['fail' => 'parent'];
//                    }
//                    if ($parent->userId != Auth::id()) {
//                        return ['fail' => 'permission denied'];
//                    }
//                    break;
//                case 'classObject':
//                    $parent = MyClass::where('classObjectId', $request['id'])->first();
//                    if (!$parent) {
//                        return ['fail' => 'parent'];
//                    }
//                    if (!($parent->userId == Auth::id() || $parent->isMember(Auth::id()))) {
//                        return ['fail' => 'permission denied'];
//                    }
//                    break;
//                case 'team':
//                    $parent = Team::find($request['id']);
//                    if (!$parent) {
//                        return ['fail' => 'parent'];
//                    }
//                    if (!($parent->myClass()->userId == Auth::id() || $parent->isMember(Auth::id()))) {
//                        return ['fail' => 'permission denied'];
//                    }
//                    break;
//                default:
//                    return ['fail' => 'invalid parameter'];
//            }
//
//            $card = null;
//            switch ($request['subTypes']) {
//                case 'myPage':
//                    $card = Card::create([
//                        'userId' => Auth::id(),
//                        'myPageId' => $parent->id,
//                        'type' => $request['type'],
//                        'title' => $request['title']
//                    ]);
//
//                    $cardsNum = $parent->cardsNum;
//                    $cardsNum[] = $card->id;
//                    $parent->cardsNum = $cardsNum;
//
//                    break;
//                case 'classObject':
//                    $card = Card::create([
//                        'userId' => Auth::id(),
//                        'classObjectId' => $parent->classObjectId,
//                        'type' => $request['type'],
//                        'title' => $request['title']
//                    ]);
//
//                    $cardsNum = $parent->cardsNum;
//                    $cardsNum[] = $card->id;
//                    $parent->cardsNum = $cardsNum;
//                    break;
//                case 'team':
//                    $card = Card::create([
//                        'userId' => Auth::id(),
//                        'classObjectId' => $parent->classObjectId,
//                        'teamId' => $parent->id,
//                        'type' => $request['type'],
//                        'title' => $request['title']
//                    ]);
//
//                    $cardsNum = $parent->cardsNum;
//                    $cardsNum[] = $card->id;
//                    $parent->cardsNum = $cardsNum;
//                    break;
//                default:
//                    break;
//            }
//
//            $parent->save();
//
//            if ($card) {
//                $card->refresh();
//                return [$card];
//            }
//            else {
//                return ['fail' => 'create card'];
//            }
//        }
//        else if ($request['types'] == 'item') {
//            $card = Card::find($request['id']);
//            if (!$card) {
//                return ['fail' => 'card'];
//            }
//            if (!$card->isPermitted(Auth::id())) {
//                return ['fail' => 'permission denied'];
//            }
//
//            $validator = Validator::make($request->all(), [
//                'title' => ['required', 'string'],
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
//                if ($errors->has('title')) {
//                    return ['fail' => 'title'];
//                }
//                else if ($errors->has('content')) {
//                    return ['fail' => 'content'];
//                }
//                else if ($errors->has('deadLine')) {
//                    return ['fail' => 'deadLine'];
//                }
//                else if ($errors->has('activationDeadline')) {
//                    return ['fail' => 'activationDeadline'];
//                }
//                else if ($errors->has('label')) {
//                    return ['fail' => 'label'];
//                }
//                else if ($errors->has('activationLabel')) {
//                    return ['fail' => 'activationLabel'];
//                }
//                else if ($errors->has('checks')) {
//                    return ['fail' => 'checks'];
//                }
//                else if ($errors->has('activationCheck')) {
//                    return ['fail' => 'activationCheck'];
//                }
//                else if ($errors->has('activationImage')) {
//                    return ['fail' => 'activationImage'];
//                }
//                else if ($errors->has('party')) {
//                    return ['fail' => 'party'];
//                }
//                else if ($errors->has('activationParty')) {
//                    return ['fail' => 'activationParty'];
//                }
//                else if ($errors->has('type')) {
//                    return ['fail' => 'type'];
//                }
//            }
//
//            $itemData = [];
//            $itemData['cardId'] = $card->id;
//            $itemData['title'] = $request['title'];
//            $itemData['userId'] = Auth::id();
//            if ($request->has('content')) {
//                $itemData['content'] = $request['content'];
//            }
//            if ($request->has('deadLine')) {
//                $itemData['deadLine'] = $request['deadLine'];
//            }
//            if ($request->has('activationDeadline')) {
//                $itemData['activationDeadline'] = $request['activationDeadline'];
//            }
//            if ($request->has('label')) {
//                $itemData['label'] = $request['label'];
//            }
//            if ($request->has('activationLabel')) {
//                $itemData['activationLabel'] = $request['activationLabel'];
//            }
//            if ($request->has('activationCheck')) {
//                $itemData['activationCheck'] = $request['activationCheck'];
//            }
//            if ($request->has('activationImage')) {
//                $itemData['activationImage'] = $request['activationImage'];
//            }
//            if ($request->has('activationParty')) {
//                $itemData['activationParty'] = $request['activationParty'];
//            }
//            if ($request->has('type')) {
//                $itemData['type'] = $request['type'];
//            }
//            if ($request->has('checks')) {
//                $itemData['checks'] = $request['checks'];
//            }
//            if ($request->has('party')) {
//                $itemData['party'] = $request['party'];
//            }
//
//            if ($request['type'] == 3) {
//                $itemData['brain'] = $request['brain'];
//            }
//
//            $item = Item::create($itemData);
//            if (!$item) {
//                return ['fail' => 'create error'];
//            }
//
//            if ($request['type'] == 1) {
////                if ($card->type != 10) {
////                    return ['fail' => 'invalid parameter'];
////                }
//                try {
//                    $problemAnalysis = json_decode($request['problemAnalysis']);
//
//                    ProblemAnalysis::create([
//                        'itemId' => $item->id,
//                        'studentId' => $problemAnalysis->studentId,
//                        'name' => Auth::user()->name,
//                        'content1' => $problemAnalysis->content1,
//                        'content2' => $problemAnalysis->content2,
//                        'content3' => $problemAnalysis->content3,
//                        'content4' => $problemAnalysis->content4,
//                    ]);
//                }
//                catch (Exception $e) {
//                    $item->delete();
//
//                    return ['fail' => 'exception'];
//                }
//            }
//            else if ($request['type'] == 2) {
////                if ($card->type != 7) {
////                    return ['fail' => 'invalid parameter'];
////                }
//
//                try {
//                    $reflectionLog = json_decode($request['reflectionLog']);
//
//                    ReflectionLog::create([
//                        'itemId' => $item->id,
//                        'studentId' => $reflectionLog->studentId,
//                        'name' => Auth::user()->name,
//                        'content1' => $reflectionLog->content1,
//                        'content2' => $reflectionLog->content2,
//                        'content3' => $reflectionLog->content3,
//                        'content4' => $reflectionLog->content4,
//                        'content5' => $reflectionLog->content5,
//                        'content6' => $reflectionLog->content6,
//                        'content7' => $reflectionLog->content7,
//                    ]);
//                }
//                catch (Exception $e) {
//                    $item->delete();
//
//                    return ['fail' => 'exception'];
//                }
//            }
//            else if ($request['type'] == 4) {
//                if ($card->type != 0) {
//                    return ['fail' => 'invalid parameter'];
//                }
//                if (Auth::user()->authority != 2) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                try {
//                    $classApply = (array)json_decode($request['classApply']);
//
//                    $validator = Validator::make($classApply, [
//                        'state' => ['string'],
//                        'type' => ['string'],
//                        'grade' => ['string'],
//                        'size' => ['string'],
//                        'eSize' => ['string'],
//                        'professorCount' => ['string'],
//                        'eProfessorCount' => ['string'],
//                        'division' => ['string'],
//                        'eDivision' => ['string'],
//                        'department' => ['string'],
//                        'sprcialClassType' => ['string'],
//                        'korName' => ['string', 'unique:class_applies'],
//                        'enName' => ['string'],
//                        'gradesPoint' => ['integer', 'required'],
//                        'lecturePoint' => ['integer', 'required'],
//                        'trainingPoint' => ['integer', 'required'],
//                        'summary' => ['string'],
//                        'expectedOutcome1' => ['string'],
//                        'expectedOutcome2' => ['string'],
//                        'expectedOutcome3' => ['string'],
//                        'expectedOutcome4' => ['string'],
//                        'expectedOutcome5' => ['string'],
//                        'expectedOutcome6' => ['string'],
//                        'expectedOutcome7' => ['string'],
//                        'eExpectedOutcome7' => ['string'],
//                        'applicantName' => ['string'],
//                        'applicantAffiliation' => ['string'],
//                        'applicantContact1' => ['string'],
//                        'applicantContact2' => ['string'],
//                        'applicantEmail' => ['email'],
//                        'applicantAgree1' => ['string'],
//                        'applicantPeriod' => ['string'],
//                        'applicantContribute' => ['string'],
//                        'applicantContent' => ['string'],
//                        'applicantAgree2' => ['string'],
//                        'applicantAgree3' => ['string'],
//                        'applicantYear' => ['string'],
//                        'applicantMonth' => ['string'],
//                        'applicantDay' => ['string'],
//                    ]);
//
//                    if ($validator->fails()) {
//                        $errors = $validator->errors();
//
//                        $item->delete();
//
//                        if ($errors->has('korName')) {
//                            return ['fail' => 'korName is unique'];
//                        }
//
//                        return ['fail' => 'invalid parameter'];
//                    }
//
////                return dd($classApply);
//
//                    $_classApply = ClassApply::create([
//                        'itemId' => $item->id,
//                        'state' => 'wait',
//                        'type' => $classApply['type'],
//                        'grade' => $classApply['grade'],
//                        'size' => $classApply['size'],
//                        'eSize' => $classApply['eSize'],
//                        'professorCount' => $classApply['professorCount'],
//                        'eProfessorCount' => $classApply['eProfessorCount'],
//                        'division' => $classApply['division'],
//                        'eDivision' => $classApply['eDivision'],
//                        'department' => $classApply['department'],
//                        'sprcialClassType' => $classApply['sprcialClassType'],
//                        'korName' => $classApply['korName'],
//                        'enName' => $classApply['enName'],
//                        'gradesPoint' => $classApply['gradesPoint'],
//                        'lecturePoint' => $classApply['lecturePoint'],
//                        'trainingPoint' => $classApply['trainingPoint'],
//                        'summary' => $classApply['summary'],
//                        'expectedOutcome1' => $classApply['expectedOutcome1'],
//                        'expectedOutcome2' => $classApply['expectedOutcome2'],
//                        'expectedOutcome3' => $classApply['expectedOutcome3'],
//                        'expectedOutcome4' => $classApply['expectedOutcome4'],
//                        'expectedOutcome5' => $classApply['expectedOutcome5'],
//                        'expectedOutcome6' => $classApply['expectedOutcome6'],
//                        'expectedOutcome7' => $classApply['expectedOutcome7'],
//                        'eExpectedOutcome7' => $classApply['eExpectedOutcome7'],
//                        'applicantName' => $classApply['applicantName'],
//                        'applicantAffiliation' => $classApply['applicantAffiliation'],
//                        'applicantContact1' => $classApply['applicantContact1'],
//                        'applicantContact2' => $classApply['applicantContact2'],
//                        'applicantEmail' => $classApply['applicantEmail'],
//                        'applicantAgree1' => $classApply['applicantAgree1'],
//                        'applicantPeriod' => $classApply['applicantPeriod'],
//                        'applicantContribute' => $classApply['applicantContribute'],
//                        'applicantContent' => $classApply['applicantContent'],
//                        'applicantAgree2' => $classApply['applicantAgree2'],
//                        'applicantAgree3' => $classApply['applicantAgree3'],
//                        'applicantYear' => $classApply['applicantYear'],
//                        'applicantMonth' => $classApply['applicantMonth'],
//                        'applicantDay' => $classApply['applicantDay'],
//                    ]);
//
//                    //시연용 테스트 코드
//                    if ($request->has('test')) {
//                        if ($_classApply) {
//                            $classObject = ClassObject::create([
//                                'name' => $_classApply->korName,
//                            ]);
//
//                            if ($classObject) {
//                                $myClass = MyClass::create([
//                                    'userId' => Auth::id(),
//                                    'classObjectId' => $classObject->id,
//                                ]);
//
//                                $teams = Team::where('classObjectId', $classObject->id)->get();
//
//                                if ($teams->count() < 20) {
//                                    foreach (range($teams->count() + 1, 20) as $i) {
//                                        Team::create([
//                                            'name' => '팀'.$i,
//                                            'classObjectId' => $classObject->id,
//                                        ]);
//                                    }
//                                }
//
//                                $__classApply = $_classApply->replicate();
//                                $__classApply->itemId = null;
//                                $__classApply->classObjectId = $classObject->id;
//
//                                $_classApply->state = 'complete';
//
//                                $_classApply->save();
//                                $__classApply->save();
//
//                                $users = User::where('authority', 1)->get();
//                                foreach ($users as $user) {
//                                    ClassList::create([
//                                        'userId' => $user->id,
//                                        'classObjectId' => $classObject->id,
//                                        'classObjectName' => $classObject->name,
//                                    ]);
//                                }
//                            }
//                        }
//                    }
//                }
//                catch (Exception $e) {
//                    $item->delete();
//
//                    return ['fail' => 'exception'];
//                }
//            }
//
//            if (is_null($card->itemsNum)) {
//                $card->itemsNum = [];
//            }
//            $itemsNum = $card->itemsNum;
//            array_unshift($itemsNum, $item->id);
//            $card->itemsNum = $itemsNum;
//            $card->save();
//
//            if ($request->hasfile('images')) {
//                $imageNames = [];
//                foreach ($request->file('images') as $image) {
//                    $imageName = uniqid();
////        $request->image->move(public_path('uploads').'/'.$myPage->id, $imageName);
//                    Storage::disk('local')->putFileAs('/images', $image, $imageName);
//                    $imageFile = ItemFileList::create([
//                        'itemId' => $item->id,
//                        'type' => 'image',
//                        'fileName' => $image->getClientOriginalName(),
//                        'pathName' => $imageName,
//                        'imgUrl' => env('APP_URL').':'.env('PORT').'/images/'.$imageName
//                    ]);
//                    $itemImages = $item->images;
//                    if (!$itemImages) {
//                        $itemImages = [];
//                    }
//                    $itemImages[] = $imageFile->id;
//                    $item->images = $itemImages;
//                }
//
//                $item->save();
//            }
//
//            $item->with_images = $item->itemFileList();
//
//            if ($item->type == 1) {
//                if (!is_null($card->classObjectId) && is_null($card->teamId)) {
//                    if ($card->type != 10) {
//                        $_myClass = $card->myClass();
//                        $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 10)->first();
//                        $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', 10)->get();
//
//                        $_itemsNum = [];
//                        foreach($_cards as $_c) {
//                            if (!is_null($_c->itemsNum)) {
//                                foreach ($_c->itemsNum as $itemNum) {
//                                    $_itemsNum[] = $itemNum;
//                                }
//                            }
//                        }
//
//                        broadcast(
//                            new FixedCardItemEvent($item, $_card->id, $_itemsNum, $card->classObjectId, 'create')
//                        );
//                    }
//                }
//                else if (!is_null($card->classObjectId) && !is_null($card->teamId)) {
//                        $_myClass = $card->myClass();
//                        $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 10)->first();
//                        $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=' ,null)->where('type', 10)->get();
//
//                        $_itemsNum = [];
//                        foreach($_cards as $_c) {
//                            if (!is_null($_c->itemsNum)) {
//                                foreach ($_c->itemsNum as $itemNum) {
//                                    $_itemsNum[] = $itemNum;
//                                }
//                            }
//                        }
//                    if ($card->type != 10) {
//                        broadcast(
//                            new FixedCardItemEvent($item, $_card->id, $_itemsNum, $card->classObjectId, 'create')
//                        );
//                    }
//                    else {
//                        broadcast(
//                            new FixedCardItemEvent($item, $_card->id, $_itemsNum, $card->classObjectId, 'create')
//                        )->toOthers();
//                    }
//                }
//
//                $item->with_problem_analysis = $item->withProblemAnalysis()->first();
//            }
//            if ($item->type == 2) {
//                if (!is_null($card->classObjectId)) {
//                    if ($card->type == 7) {
//                        $_myClass = $card->myClass();
//                        $_card = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', null)->where('type', 7)->first();
//                        $_cards = Card::where('classObjectId', $_myClass->classObjectId)->where('teamId', '!=', null)->where('type', 7)->get();
//
//                        $_itemsNum = [];
//                        foreach ($_cards as $_c) {
//                            if (!is_null($_c->itemsNum)) {
//                                foreach ($_c->itemsNum as $itemNum) {
//                                    $_itemsNum[] = $itemNum;
//                                }
//                            }
//                        }
//
//                        broadcast(
//                            new FixedCardItemEvent($item, $_card->id, $_itemsNum, $card->classObjectId, 'create')
//                        );
//                    }
//                }
//
//                $item->with_reflection_log = $item->withReflectionLog()->first();
//            }
//            if ($item->type == 4) {
//                $item->with_class_apply = $item->withClassApply()->first();
//            }
//
//            return [$item];
//        }
//        else if ($request['types'] == 'talk') {
//            $validator = Validator::make($request->all(), [
//                'subTypes' => ['required', 'string', 'in:team,classObject'],
//            ]);
//
//            if ($validator->fails()) {
//                $errors = $validator->errors();
//
//                if ($errors->has('subTypes')) {
//                    return ['fail' => 'subTypes'];
//                }
//            }
//
//            $card = null;
//            $subTypes = $request['subTypes'];
//            if ($subTypes == 'team') {
//                $team = Team::find($request['id']);
//                if (!$team) {
//                    return ['fail' => 'invalid parameter'];
//                }
//                if (!$team->isMember(Auth::id()) && $team->myClass()->userId != Auth::id()) {
//                    return ['fail' => 'permission denied'];
//                }
////                if (!$team->myClass()->onTeamTalk) {
////                    return ['fail' => 'permission denied'];
////                }
//                if ($team->onTeamTalk) {
//                    return ['fail' => 'already exists'];
//                }
//
//                $card = Card::create([
//                    'title' => '팀톡',
//                    'userId' => Auth::id(),
//                    'classObjectId' => $team->classObjectId,
//                    'teamId' => $team->id,
//                    'type' => 12
//                ]);
//
//                $cardsNum = $team->cardsNum;
//                $cardsNum[] = $card->id;
//                $team->cardsNum = $cardsNum;
//
//                $team->onTeamTalk = '1';
//                $team->save();
//            }
//            else {
//                $myClass = MyClass::where('classObjectId', $request['id'])->first();
//                if (!$myClass) {
//                    return ['fail' => 'invalid parameter'];
//                }
//
//                if ($myClass->userId != Auth::id()) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                if ($myClass->onClassTalk) {
//                    return ['fail' => 'already exists'];
//                }
//
//                $card = Card::create([
//                    'title' => '수업공지',
//                    'userId' => Auth::id(),
//                    'classObjectId' => $myClass->classObjectId,
//                    'type' => 5
//                ]);
//
//                $cardsNum = $myClass->cardsNum;
//                $cardsNum[] = $card->id;
//                $myClass->cardsNum = $cardsNum;
//                $myClass->onClassTalk = '1';
//
//                $myClass->save();
//            }
//
//            if (!$card) {
//                return ['fail' => 'unknown error'];
//            }
//            return [$card];
//        }
//    }

//    public function delete (Request $request)
//    {
//        if ($request['types'] == 'card') {
//            $card = Card::find($request['id']);
//            if (!$card) {
//                return ['fail' => 'card'];
//            }
//
//            $parent = null;
//            switch ($request['subTypes']) {
//                case 'myPage':
//                    $parent = MyPage::find($card->myPageId);
//                    if (!$parent) {
//                        return ['fail' => 'parent'];
//                    }
////                    if ($parent->userId != Auth::id()) {
////                        return ['fail' => 'permission denied'];
////                    }
//                    break;
//                case 'classObject':
//                    $parent = MyClass::where('classObjectId', $card->classObjectId)->first();
//                    if (!$parent) {
//                        return ['fail' => 'parent'];
//                    }
////                    if (!($parent->userId == Auth::id() || $card->userId == Auth::id())) {
////                        return ['fail' => 'permission denied'];
////                    }
//                    break;
//                case 'team':
//                    $parent = Team::find($card->teamId);
//                    if (!$parent) {
//                        return ['fail' => 'parent'];
//                    }
////                    if (!($parent->myClass()->userId == Auth::id() || $card->userId == Auth::id())) {
////                        return ['fail' => 'permission denied'];
////                    }
//                    break;
//                default:
//                    return ['fail' => 'parent'];
//            }
//
//            //자기가 만든 카드만 삭제할수 있게 임시 수정
//            if ($card->userId != Auth::id()) {
//                return ['fail' => 'permission denied'];
//            }
//
//            //권한 체크
//
//            $cardsNum = $parent->cardsNum;
//            $key = array_search($card->id, $cardsNum);
//            array_splice($cardsNum, $key, 1);
//            $parent->cardsNum = $cardsNum;
//
//            foreach ($card->items() as $item) {
//                $comments = $item->getComments();
//                $files = [];
//                foreach ($comments as $comment) {
//                    $files[] = 'comment/'.$comment->filePathName;
//                }
//                foreach ($item->itemFileList() as $image) {
//                    $files[] = 'images/'.$image->pathName;
//                }
//                Storage::disk('local')->delete($files);
//            }
//
//            $card->delete();
//            $parent->save();
//
//            return ['success' => true];
//        }
//        else if ($request['types'] == 'item') {
//            //id: item->id
//            //types: 'item'
//            $item = Item::find($request['id']);
//            if (!$item) {
//                return ['fail' => 'id'];
//            }
//
//            $card = $item->card();
//            if (!$card) {
//                return ['fail' => 'card'];
//            }
//
////            if ($item->userId == Auth::id() || $card->isOwned(Auth::id())) {
//            if ($item->userId == Auth::id()) {
//                $comments = $item->getComments();
//                $files = [];
//                foreach ($comments as $comment) {
//                    $files[] = 'comment/'.$comment->filePathName;
//                }
//                foreach ($item->itemFileList() as $image) {
//                    $files[] = 'images/'.$image->pathName;
//                }
//                Storage::disk('local')->delete($files);
//                $itemsNum = $card->itemsNum;
//                $key = array_search($item->id, $itemsNum);
//                array_splice($itemsNum, $key, 1);
//                $card->itemsNum = $itemsNum;
//                $card->save();
//                $item->delete();
//
//                return ['success' => true];
//            }
//            else {
//                return ['fail' => 'permission denied'];
//            }
//        }
//        else if ($request['types'] == 'comment') {
//            $comment = Comment::find($request['id']);
//            if (!$comment) {
//                return ['fail' => 'id'];
//            }
//
////            if (!$comment->isDeletable(Auth::id())) {
////                return ['fail' => 'permission denied'];
////            }
//            if (!$comment->userId != Auth::id()) {
//                return ['fail' => 'permission denied'];
//            }
//
//            if ($comment->filePathName) {
//                Storage::disk('local')->delete('comment/'.$comment->filePathName);
//            }
//
//            $comment->delete();
//
//            return ['success' => true];
//        }
//    }

//    public function update (Request $request) {
////        return dd($request->all());
//
//        $validator = Validator::make($request->all(), [
//            'subTypes' => ['required', 'string', 'in:title,myPage,classObject,team,move,edit,comment'],
//            'title' => ['string'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('id')) {
//                return ['fail' => 'id'];
//            }
//            else if ($errors->has('types')) {
//                return ['fail' => 'types'];
//            }
//            else if ($errors->has('subTypes')) {
//                return ['fail' => 'subTypes'];
//            }
//            else if ($errors->has('title')) {
//                return ['fail' => 'title'];
//            }
//        }
//
//        if ($request['types'] == 'card') {
//            if ($request['subTypes'] == 'title') { //edit title
//                $card = Card::find($request['id']);
//                if (!$card) {
//                   return ['fail' => 'id'];
//                }
//
//                //카드 타입 4번이 일반카드 고정카드들은 이름 수정 불가능
//                if ($card->type != 4) {
//                    return ['fail' => 'permission denied'];
//                }
//
////                if (!$card->isPermitted(Auth::id())) {
////                    return ['fail' => 'permission denied'];
////                }
//                if ($card->userId != Auth::id()) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                $card->title = $request['title'];
//                $card->save();
//                return ['success' => true];
//            }
//            else { //move
//                $validator = Validator::make($request->all(), [
//                    'subTypes' => ['required', 'string', 'in:myPage,classObject,team'],
//                    'subId' => ['required', 'integer', 'min:1'],
//                    'destinationPosition' => ['required', 'integer', 'min:0'],
//                ]);
//
//                if ($validator->fails()) {
//                    $errors = $validator->errors();
//
//                    if ($errors->has('subTypes')) {
//                        return ['fail' => 'subTypes'];
//                    }
//                    else if ($errors->has('subId')) {
//                        return ['fail' => 'subId'];
//                    }
//                    else if ($errors->has('destinationPosition')) {
//                        return ['fail' => 'destinationPosition'];
//                    }
//                }
//
//                $card = Card::find($request['id']);
//                if (!$card) {
//                    return ['fail' => 'card'];
//                }
//                if ($card->type != 4) {
//                    return ['fail' => 'invalid move'];
//                }
//                if (!$card->isPermitted(Auth::id())) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                $parent = null;
//                switch ($request['subTypes']) {
//                    case 'myPage':
//                        $parent = MyPage::find($request['subId']);
//                        break;
//                    case 'classObject':
//                        $parent = MyClass::where('classObjectId', $request['subId'])->first();
//                        break;
//                    case 'team':
//                        $parent = Team::find($request['subId']);
//                        break;
//                    default:
//                        break;
//                }
//                if (!$parent) {
//                    return ['fail' => 'parent'];
//                }
//
//                $key = $request['destinationPosition'];
//                $cardsNum = $parent->cardsNum;
//                if (!$cardsNum || (gettype($cardsNum) != 'array' && count($cardsNum) <= 0)) {
//                    return ['fail' => 'cardsNum is empty'];
//                }
//
//                $sourceKey = array_search($card->id, $cardsNum);
//                if (gettype($sourceKey) == 'boolean') {
//                    return ['fail' => 'not matchable card with parent'];
//                }
//                array_splice($cardsNum, $sourceKey, 1);
//                if ($key < 0) {
//                    $key = 0;
//                }
//                else if ($key > count($cardsNum)) {
//                    $key = count($cardsNum);
//                }
//                if ($key < count($cardsNum) && Card::find($cardsNum[$key])->type != 4) {
//                    return ['fail' => 'invalid move'];
//                }
//                array_splice($cardsNum, $key, 0, $card->id);
//
//                $parent->cardsNum = $cardsNum;
//                $parent->save();
//
//                return ['success' => true];
//            }
//        }
//        else if ($request['types'] == 'item') {
//            if ($request['subTypes'] == 'move') {
//                $validator = Validator::make($request->all(), [
//                    'subId' => ['required', 'integer', 'min:1'],
//                    'destinationPosition' => ['required', 'integer', 'min:0'],
//                ]);
//
//                if ($validator->fails()) {
//                    $errors = $validator->errors();
//
//                    if ($errors->has('subId')) {
//                        return ['fail' => 'subId'];
//                    }
//                    else if ($errors->has('destinationPosition')) {
//                        return ['fail' => 'destinationPosition'];
//                    }
//                }
//
//                $item = Item::find($request['id']);
//                if (!$item) {
//                    return ['fail' => 'item'];
//                }
//                $card = Card::find($request['subId']);
//                if (!$card) {
//                    return ['fail' => 'card'];
//                }
//
//                if ($item->card()->type != 4 || $card->type != 4) {
//                    return ['fail' => 'invalid move'];
//                }
//
//                if (!$card->isPermitted(Auth::id())) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                if (($item->card()->id != $card->id) && ($item->card()->type != 4 || $card->type != 4)) {
//                    return ['fail' => 'invalid move'];
//                }
//
//                if ($item->card()->myPageId != $card->myPageId || $item->card()->classObjectId != $card->classObjectId || $item->card()->teamId != $card->teamId) {
//                    return ['fail' => 'not matchable card group'];
//                }
//
//                $sourceCard = $item->card();
//                $sourceItemsNum = $sourceCard->itemsNum;
//                $sourceKey = array_search($item->id, $sourceItemsNum);
//                $key = $request['destinationPosition'];
//                array_splice($sourceItemsNum, $sourceKey, 1);
//                $sourceCard->itemsNum = $sourceItemsNum;
//
//                if ($card->id == $sourceCard->id) {
//                    $card = $sourceCard;
//                }
//
//                if ($key < 0) {
//                    $key = 0;
//                }
//                else if (gettype($card->itemsNum) != 'array') {
//
//                }
//                else if ($key > count($card->itemsNum)) {
//                    $key = count($card->itemsNum);
//                }
//
//                if (!$card->itemsNum) {
//                    $card->itemsNum = [$item->id];
//                }
//                else {
//                    $itemsNum = $card->itemsNum;
//                    array_splice($itemsNum, $key, 0, $item->id);
//                    $card->itemsNum = $itemsNum;
//                }
//
////                return dd($card->itemsNum);
//
//                $item->cardId = $card->id;
//
//                if ($card->id != $sourceCard->id) {
//                    $sourceCard->save();
//                    $item->save();
//                }
//                $card->save();
//
//
//                return ['success' => true];
//            }
//            else if ($request['subTypes'] == 'edit') {
//                $validator = Validator::make($request->all(), [
//                    'content' => ['string', 'nullable'],
//                    'deadLine' => ['date', 'nullable'],
//                    'activationDeadline' => ['boolean'],
//                    'label' => ['integer', 'min:0', 'max:8'],
//                    'activationLabel' => ['boolean'],
//                    'checks' => ['json', 'nullable'],
//                    'activationCheck' => ['boolean'],
//                    'activationImage' => ['boolean'],
//                    'party' => ['json', 'nullable'],
//                    'activationParty' => ['boolean'],
//                    'type' => ['required', 'integer', 'min:0'],//max:3
//                    'brain' => ['json', 'nullable'],
//                ]);
//
//                if ($validator->fails()) {
//                    $errors = $validator->errors();
//
//                    if ($errors->has('id')) {
//                        return ['fail' => 'id'];
//                    }
//                    if ($errors->has('cardId')) {
//                        return ['fail' => 'cardId'];
//                    }
//                    if ($errors->has('title')) {
//                        return ['fail' => 'title'];
//                    }
//                    if ($errors->has('content')) {
//                        return ['fail' => 'content'];
//                    }
//                    if ($errors->has('images')) {
//                        return ['fail' => 'images'];
//                    }
//                    if ($errors->has('images.*')) {
//                        return ['fail' => 'images.*'];
//                    }
//                    if ($errors->has('activationImage')) {
//                        return ['fail' => 'activationImage'];
//                    }
//                    if ($errors->has('imagesNum')) {
//                        return ['fail' => 'imagesNum'];
//                    }
//                    if ($errors->has('imagesNum.*')) {
//                        return ['fail' => 'imagesNum.*'];
//                    }
//                    if ($errors->has('deadLine')) {
//                        return ['fail' => 'deadLine'];
//                    }
//                    if ($errors->has('activationDeadline')) {
//                        return ['fail' => 'activationDeadline'];
//                    }
//                    if ($errors->has('label')) {
//                        return ['fail' => 'label'];
//                    }
//                    if ($errors->has('activationLabel')) {
//                        return ['fail' => 'activationLabel'];
//                    }
//                    if ($errors->has('checks')) {
//                        return ['fail' => 'checks'];
//                    }
//                    if ($errors->has('activationCheck')) {
//                        return ['fail' => 'activationCheck'];
//                    }
//                    if ($errors->has('brain')) {
//                        return ['fail' => 'brain'];
//                    }
//                    if ($errors->has('party')) {
//                        return ['fail' => 'party'];
//                    }
//                    if ($errors->has('activationParty')) {
//                        return ['fail' => 'activationParty'];
//                    }
//                    if ($errors->has('type')) {
//                        return ['fail' => 'type'];
//                    }
//                }
//
//                $item = Item::find($request['id']);
//                if (!$item) {
//                    return false;
//                }
//                if ($item->userId != Auth::id()) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                if ($request->has('title')) {
//                    $item->title = $request['title'];
//                }
//                if ($request->has('content')) {
//                    $item->content = $request['content'];
//                }
//                if ($request->has('deadLine')) {
//                    $item->deadLine = $request['deadLine'];
//                }
//                if ($request->has('activationImage')) {
//                    $item->activationImage = $request['activationImage'];
//                }
//                if ($request->has('activationDeadline')) {
//                    $item->activationDeadline = $request['activationDeadline'];
//                }
//                if ($request->has('label')) {
//                    $item->label = $request['label'];
//                }
//                if ($request->has('activationLabel')) {
//                    $item->activationLabel = $request['activationLabel'];
//                }
//                if ($request->has('checks')) {
//                    $item->checks = $request['checks'];
//                }
//                if ($request->has('party')) {
//                    $item->party = $request['party'];
//                }
//
//
//                $imageNames = $item->images;
//                $_imageNames = [];
//                $newNum = 0;
//                $imagesNum = $request['imagesNum'];
//                if (!$imagesNum) {
//                    $imagesNum = [];
//                }
//                foreach ($imagesNum as $imageNum) {
//                    if ($imageNum > 0) {
//                        $key = array_search($imageNum, $imageNames);
//                        if (!$key && gettype($key) == 'boolean') {
//                            continue;
//                        }
//                        $_imageNames[] = $imageNames[$key];
//
//                        array_splice($imageNames, $key, 1);
//                    }
//                    else {
//                        if ($request->hasfile('images')) {
//                            $file = $request->file('images')[$newNum++];
//                            if (!$file) {
//                                continue;
//                            }
//
//                            $imageName = uniqid();
//                            Storage::disk('local')->putFileAs('/images', $file, $imageName);
//                            $imageFile = ItemFileList::create([
//                                'itemId' => $item->id,
//                                'type' => 'image',
//                                'fileName' => $file->getClientOriginalName(),
//                                'pathName' => $imageName,
//                                'imgUrl' => env('APP_URL').':'.env('PORT').'/images/'.$imageName
//                            ]);
//                            $_imageNames[] = $imageFile->id;
//                        }
//                    }
//                }
//
//                $item->images = $_imageNames;
//
//                if ($imageNames) {
//                    foreach ($imageNames as $imageName) {
//                        $itemFileList = ItemFileList::find($imageName);
//                        if ($itemFileList) {
//                            Storage::disk('local')->delete('images/'.$itemFileList->pathName);
//
//                            $itemFileList->delete();
//                        }
//                    }
//                }
//
//                if ($request->has('checks')) {
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
//                    $item->checks = $request['checks'];
//                }
//                if ($request->has('activationCheck')) {
//                    $item->activationCheck = $request['activationCheck'];
//                }
//                if ($request->has('brain')) {
////                    $brain = json_decode($request['brain']);
////                    $_brain = [];
////                    if ($brain) {
////                        foreach ($brain as $b) {
////                            if (gettype($b) == 'object') {
////                                if (
////                                    property_exists($b, 'userId') && property_exists($b, 'name')
////                                    && property_exists($b, 'content') && property_exists($b, 'date')
////                                    && property_exists($b, 'color')
////                                ) {
////                                    $__brain = [
////                                        'userId' => $b->userId,
////                                        'name' => $b->name,
////                                        'content' => $b->content,
////                                        'date' => $b->date,
////                                        'color' => $b->color,
////                                    ];
////
////                                    $validator = Validator::make($__brain, [
////                                        'userId' => ['required', 'integer'],
////                                        'name' => ['required', 'string'],
////                                        'content' => ['required', 'string'],
////                                        'date' => ['required', 'date'],
////                                        'color' => ['required', 'integer', 'min:1', 'max:8'],
////                                    ]);
////
////                                    if (!$validator->fails()) {
////                                        $_brain[] = $__brain;
////                                    }
////                                    else {
////                                        return 'brain';
////                                    }
////                                }
////                            }
////                        }
////                    }
////                    $request['brain'] = $_brain;
////
////                    $item->brain = $request['brain'];
//                    $item->brain = $request['brain'];
//                }
//                if ($request->has('party')) {
//                    $item->party = $request['party'];
//                }
//                if ($request->has('activationParty')) {
//                    $item->activationParty = $request['activationParty'];
//                }
//
////                return dd($request->all());
//
//                if ($request->has('type')) {
//                    if ($request['type'] == 1) {
//                        $problemAnalysis = json_decode($request['problemAnalysis']);
//
//                        $_problemAnalysis = ProblemAnalysis::where('itemId', $item->id)->first();
//                        if ($_problemAnalysis) {
//                            $_problemAnalysis->studentId = $problemAnalysis->studentId;
//                            $_problemAnalysis->content1 = $problemAnalysis->content1;
//                            $_problemAnalysis->content2 = $problemAnalysis->content2;
//                            $_problemAnalysis->content3 = $problemAnalysis->content3;
//                            $_problemAnalysis->content4 = $problemAnalysis->content4;
//
//                            $_problemAnalysis->save();
//                        }
//                    }
//                    else if ($request['type'] == 2) {
//                        $reflectionLog = json_decode($request['reflectionLog']);
//
//                        $_reflectionLog = ReflectionLog::where('itemId', $item->id)->first();
//                        if ($_reflectionLog) {
//                            $_reflectionLog->studentId = $reflectionLog->studentId;
//                            $_reflectionLog->content1 = $reflectionLog->content1;
//                            $_reflectionLog->content2 = $reflectionLog->content2;
//                            $_reflectionLog->content3 = $reflectionLog->content3;
//                            $_reflectionLog->content4 = $reflectionLog->content4;
//
//                            $_reflectionLog->save();
//                        }
//                    }
//                }
//
//                $item->save();
//
//                $item->refresh();
//
//                $item->imagesNum = $item->images;
//                $item->images = $item->itemFileList();
//                if ($item->type == 1) {
//                    $item->with_problem_analysis = $item->withProblemAnalysis()->first();
//                }
//                if ($item->type == 2) {
//                    $item->with_reflection_log = $item->withReflectionLog()->first();
//                }
//
//                return [$item];
//            }
//            else if ($request['subTypes'] == 'comment') {
//                //id: item->id
//                //types: 'item'
//                //subTypes: 'comment'
//                //content: '댓글 내용'
//                //file: 파일
//                $validator = Validator::make($request->all(), [
//                    'content' => ['required', 'string'],
//                    'file' => ['file', 'nullable'],
//                ]);
//
//                if ($validator->fails()) {
//                    $errors = $validator->errors();
//
//                    if ($errors->has('content')) {
//                        return ['fail' => 'content'];
//                    }
//                    else if ($errors->has('file')) {
//                        return ['fail' => 'file'];
//                    }
//                }
//
//                $item = Item::find($request['id']);
//                if (!$item) {
//                    return ['fail' => 'id'];
//                }
//
////                $card = $item->card();
////                if (!$card) {
////                    return ['fail' => 'card'];
////                }
//
//                if (!($item->isPermitted(Auth::id()))) {
//                    return ['fail' => 'permission denied'];
//                }
//
//                $comment = null;
//                if ($request->hasFile('file')) {
//                    $file = $request->file('file');
//                    $filePathName = uniqid();
//                    Storage::disk('local')->putFileAs('/comment', $file, $filePathName);
//                    $comment = Comment::create([
//                        'userId' => Auth::id(),
//                        'itemId' => $item->id,
//                        'content' => $request['content'],
//                        'fileName' => $file->getClientOriginalName(),
//                        'filePathName' => $filePathName,
//                    ]);
//                }
//                else {
//                    $comment = Comment::create([
//                        'userId' => Auth::id(),
//                        'itemId' => $item->id,
//                        'content' => $request['content'],
//                    ]);
//
//                    $comment->refresh();
//                }
//
//                if (!$comment) {
//                    return ['fail' => 'comment'];
//                }
//                else {
//                    $comment->with_user = User::select('id', 'name')->where('id', $comment->userId)->first();
//                }
//
//                return [$comment];
//            }
//        }
//        else if ($request['types'] == 'comment') {
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
//            $comment = Comment::find($request['id']);
//            if (!$comment) {
//                return ['fail' => 'id'];
//            }
//
//            if ($comment->userId != Auth::id()) {
//                return ['fail' => 'permission denied'];
//            }
//
//            if ($request->hasFile('file')) {
//                Storage::disk('local')->delete('comment/'.$comment->filePathName);
//                $file = $request->file('file');
//                $filePathName = uniqid();
//                Storage::disk('local')->putFileAs('/comment', $file, $filePathName);
//
//                $comment->content = $request['content'];
//                $comment->fileName = $file->getClientOriginalName();
//                $comment->filePathName = $filePathName;
//            }
//            else {
//                $comment->content = $request['content'];
//            }
//
//            $comment->save();
//            $comment->refresh();
//
//            return [$comment];
//        }
//    }

//    public function chat (Request $request) {
//        $validator = Validator::make($request->all(), [
//            'id' => ['required', 'integer', 'min:1'],
//            'types' => ['required', 'string', 'in:team,classObject'],
//            'subId' => ['required', 'integer', 'min:1'],
//            'message' => ['required', 'string'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('id')) {
//                return ['fail' => 'id'];
//            }
//            if ($errors->has('types')) {
//                return ['fail' => 'types'];
//            }
//            if ($errors->has('subId')) {
//                return ['fail' => 'subId'];
//            }
//            if ($errors->has('message')) {
//                return ['fail' => 'message'];
//            }
//        }
//
//        $card = Card::find($request['id']);
//        if (!$card) {
//            return ['fail' => 'invalid parameter'];
//        }
//
//        $parent = null;
//        if ($request['types'] == 'team') {
//            if ($card->teamId != $request['subId']) {
//                return ['fail' => 'invalid parameter'];
//            }
//
//            $parent = Team::find($request['subId']);
//            if (!$parent) {
//                return ['fail' => 'invalid parameter'];
//            }
//
////            if (!$parent->isMember(Auth::id())) {
////                return ['fail' => 'permission denied'];
////            }
//
//            if (!$card->isPermitted(Auth::id())) {
//                return ['fail' => 'permission denied'];
//            }
//        }
//        else if ($request['types'] == 'classObject') {
//            if ($card->classObjectId != $request['subId']) {
//                return ['fail' => 'invalid parameter'];
//            }
//
//            $parent = MyClass::where('classObjectId', $request['subId'])->first();
//            if (!$parent) {
//                return ['fail' => 'invalid parameter'];
//            }
//
//            if (!($parent->isMember(Auth::id()) || $parent->userId == Auth::id())) {
//                return ['fail' => 'permission denied'];
//            }
//        }
//
//        if (!$parent) {
//            return ['fail' => 'invalid parameter'];
//        }
//
//        $message = Talk::create([
//            'userId' => Auth::id(),
//            'cardId' => $card->id,
//            'name' => Auth::user()->name,
//            'message' => $request['message']
//        ]);
//
//        broadcast(
//            new ChattingEvent($message, $request['types'] == 'team' ? 'MYTEAM' : 'MYCLASS', $request['subId'])
//        )->toOthers();
//
////        broadcast(
////            new ChattingEvent(Talk::find(2), 'MYCLASS', 1)
////        )->toOthers();
//
//        return ['success' => true];
//    }

//    public function download (Request $request, $commentId) {
//        $validator = Validator::make(['commentId' => $commentId], [
//            'commentId' => ['required', 'integer', 'min:1'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('commentId')) {
//                return ['fail' => 'id'];
//            }
//        }
//
//        $comment = Comment::find($commentId);
//        if (!$comment) {
//           return ['fail' => 'id'];
//        }
//
//        if (!$comment->item()->isPermitted(Auth::id())) {
//            return ['fail' => 'permission denied'];
//        }
//
////        if (!is_null($comment->item()->card()->classObjectId) && !is_null($comment->item()->card()->teamId) && is_null($comment->item()->card()->myPageId)) {
////            //팀 일때
////            $myClass = $comment->item()->card()->myClass();
////            if (!$myClass) {
////                return null;
////            }
////
////            if (!$myClass->onTeamAccess) {
////                if (!$comment->item()->card()->isPermitted(Auth::id())) {
////                    return null;
////                }
////            }
////        }
////        else {
////            if (!$comment->item()->card()->isPermitted(Auth::id())) {
////                return null;
////            }
////        }
//
//        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
//        $path = $storagePath.'comment/'.$comment->filePathName;
//
//        return response()->download($path, $comment->fileName);
//    }

//    public function downloadImage(Request $request, $pathName) {
//        $validator = Validator::make(['pathName' => $pathName], [
//            'pathName' => ['required', 'string'],
//        ]);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors();
//
//            if ($errors->has('pathName')) {
//                return ['fail' => 'pathName'];
//            }
//        }
//
//        $itemFileList = ItemFileList::where('pathName', $pathName)->first();
//        if (!$itemFileList) {
//            return ['fail' => 'id'];
//        }
//
//        if (!$itemFileList->item()->isPermitted(Auth::id())) {
//            return null;
//        }
//
//        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
//        $path = $storagePath.'images/'.$itemFileList->pathName;
//
//        return response()->download($path, $itemFileList->fileName);
//    }
}
