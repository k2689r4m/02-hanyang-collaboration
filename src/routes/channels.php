<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
//
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('MYCLASS_{id}', function ($user, $id) {
//    $class = Classes::find($id);
//    $lecture = Lecture::find($class->lecture_id);

//    $class->active_member_count = $class->active_member_count + 1;
//    $class->save();

    $isUser = [];
//    if($lecture->use_nickname){
//        $isUser = [
//            'id' => $user->id,
//            'name' => $user->nickname
//        ];
//    }
//    else{
//        $isUser = [
//            'id' => $user->id,
//            'name' => $user->name,
//        ];
//    }

//    return (int) $user->id === (int) Auth::id();
    return $user;
});

Broadcast::channel('SUPER_{id}', function ($user, $id) {
    return (int) $user->id === (int) Auth::id();
});


Broadcast::channel('MYTEAM_{id}', function ($user, $id) {
    return (int) $user->id === (int) Auth::id();
});

Broadcast::channel('ADMIN', function ($user) {
    // 어드민 체크 필요?
    return true;
});

Broadcast::channel('PRO_{id}', function ($user, $id) {
    return (int) $user->id === (int) Auth::id();
});

Broadcast::channel('ACT_{id}', function ($user, $id) {
    return (int) $user->id === (int) Auth::id();
});

Broadcast::channel('SCH_{id}', function ($user, $id) {
    return (int) $user->id === (int) Auth::id();
});

Broadcast::channel('STU_SCH_{id}', function ($user, $id) {
    return (int) $user->id === (int) Auth::id();
});