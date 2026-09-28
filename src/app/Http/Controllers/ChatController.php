<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\MyClass;
use App\Models\Team;
use App\Models\Card;
use App\Models\Talk;
use App\Events\ChattingEvent;

class ChatController extends Controller
{
    //
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

        $card = Card::find($request['id']);
        if (!$card) {
            return ['fail' => 'id'];
        }

        if ($request['subTypes'] == 'team') {
            if ($card->type != 12) {
                return ['fail' => 'invalid parameter'];
            }

            if (is_null($card->teamId)) {
                return ['fail' => 'invalid parameter'];
            }

            if (!$card->isPermitted(Auth::id())) {
                return ['fail' => 'permission denied!!'];
            }

            return $card->messages()->sortBy('id')->values()->all();
        }
        else if ($request['subTypes'] == 'classObject') {
            if ($card->type != 5) {
                return ['fail' => 'invalid parameter'];
            }

            if (is_null($card->classObjectId)) {
                return ['fail' => 'invalid parameter'];
            }

            if (!$card->isPermitted(Auth::id())) {
                return ['fail' => 'permission denied'];
            }

            return $card->messages()->sortBy('id')->values()->all();
        }
    }

    public function chat (Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'min:1'],
            'types' => ['required', 'string', 'in:team,classObject'],
            'subId' => ['required', 'integer', 'min:1'],
            'message' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('id')) {
                return ['fail' => 'id'];
            }
            if ($errors->has('types')) {
                return ['fail' => 'types'];
            }
            if ($errors->has('subId')) {
                return ['fail' => 'subId'];
            }
            if ($errors->has('message')) {
                return ['fail' => 'message'];
            }
        }

        $card = Card::find($request['id']);
        if (!$card) {
            return ['fail' => 'invalid parameter'];
        }

        $parent = null;
        if ($request['types'] == 'team') {
            if ($card->teamId != $request['subId']) {
                return ['fail' => 'invalid parameter'];
            }

            $parent = Team::find($request['subId']);
            if (!$parent) {
                return ['fail' => 'invalid parameter'];
            }

//            if (!$parent->isMember(Auth::id())) {
//                return ['fail' => 'permission denied'];
//            }

            if (!$card->isPermitted(Auth::id())) {
                return ['fail' => 'permission denied'];
            }
        }
        else if ($request['types'] == 'classObject') {
            if ($card->classObjectId != $request['subId']) {
                return ['fail' => 'invalid parameter'];
            }

            $parent = MyClass::where('classObjectId', $request['subId'])->first();
            if (!$parent) {
                return ['fail' => 'invalid parameter'];
            }

            if (!($parent->isMember(Auth::id()) || $parent->userId == Auth::id())) {
                return ['fail' => 'permission denied'];
            }
        }

        if (!$parent) {
            return ['fail' => 'invalid parameter'];
        }

        $message = Talk::create([
            'userId' => Auth::id(),
            'cardId' => $card->id,
            'name' => Auth::user()->name,
            'message' => $request['message']
        ]);

        broadcast(
            new ChattingEvent($message, $request['types'] == 'team' ? 'MYTEAM' : 'MYCLASS', $request['subId'])
        )->toOthers();

//        broadcast(
//            new ChattingEvent(Talk::find(2), 'MYCLASS', 1)
//        )->toOthers();

        return ['success' => true];
    }
}
