<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Item;
use App\Models\Card;
use App\Models\Comment;
use App\Models\ProLog;
use App\Events\CommentEvent;
use App\Events\ProLogEvent;
use App\Events\SuperEvent;


class CommentController extends Controller
{
    //

    public function fetch (Request $request) {
        $item = Item::find($request['id']);
        if (!$item) {
            return ['fail' => 'id'];
        }

        if (!$item->isPermitted(Auth::id())) {
            return ['fail' => 'permission denied'];
        }

        return $item->commentsOrderByDesc();
    }

    public function fetchOne (Request $request) {
        $comment = Comment::find($request['id']);
        if (!$comment) {
            return ['fail' => 'id'];
        }

        if (!$comment->item()->isPermitted(Auth::id())) {
            return ['fail' => 'permission denied'];
        }

        $comment->with_user = User::select('id', 'name')->where('id', $comment->userId)->first();

        return [$comment];
    }

    public function delete (Request $request) {
        $comment = Comment::find($request['id']);
        if (!$comment) {
            return ['fail' => 'id'];
        }

        if ($comment->userId != Auth::id()) {
            return ['fail' => 'permission denied'];
        }

        if ($comment->filePathName) {
            Storage::disk('local')->delete('comment/'.$comment->filePathName);
        }

        $item = $comment->item();
        $comment->delete();

        broadcast(
            new CommentEvent(['itemId' => $item->id, 'commentId' => $comment->id], $item->card()->teamId ?? $item->card()->classObjectId,
                $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                'del')
        )->toOthers();

        $card = $item->card();
        if (!is_null($card->teamId) && in_array($item->type, [1, 2, 5]) && in_array($card->type, [7, 9, 10])) {
            $_card = Card::where('classObjectId', $card->classObjectId)->where('teamId', null)->where('type', $card->type)->first();
            if ($_card) {
                if ($card->myClass()->onTeamAccess) {
                    broadcast(
                        new CommentEvent(['itemId' => $item->id, 'commentId' => $comment->id], $item->card()->classObjectId,
                            'MYCLASS',
                            'del')
                    )->toOthers();
                } else {
                    $item->commentId = $comment->id;

                    broadcast(
                        new SuperEvent(
                            $item->toArray(),
                            $card->classObjectId,
                            $_card->id,
                            'comment.del')
                    )->toOthers();
                }
            }
        }

        return ['success' => true];
    }

    public function update (Request $request) {
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string'],
            'file' => ['file', 'nullable'],
            'mentions' => ['required', 'json'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('content')) {
                return ['fail' => 'content'];
            }
            else if ($errors->has('file')) {
                return ['fail' => 'file'];
            }
        }

        $comment = Comment::find($request['id']);
        if (!$comment) {
            return ['fail' => 'id'];
        }

        if ($comment->userId != Auth::id()) {
            return ['fail' => 'permission denied'];
        }

        $mentions = (array)json_decode($request['mentions']);
        $mentions = array_unique($mentions);
        $users = User::whereIn('id', $mentions)->get();
        $item = $comment->item();
        foreach ($mentions as $mention) {
            if (!$item->isPermitted2($mention)) {
                return ['fail' => true, 'sub' => false, ['id' => ['mention error']]];
            }
        }

        if ($request->hasFile('file')) {
            Storage::disk('local')->delete('comment/'.$comment->filePathName);
            $file = $request->file('file');
            $filePathName = uniqid();
            Storage::disk('local')->putFileAs('/comment', $file, $filePathName);

            $comment->content = $request['content'];
            $comment->fileName = $file->getClientOriginalName();
            $comment->filePathName = $filePathName;
        }
        else {
            $comment->content = $request['content'];
        }

        $comment->save();
        $comment->refresh();

        $comment->with_user = $comment->withUser()->first();
        broadcast(
            new CommentEvent(['itemId' => $item->id, 'comment' => $comment], $item->card()->teamId ?? $item->card()->classObjectId,
                $item->card()->teamId ? 'MYTEAM' : 'MYCLASS',
                'update')
        )->toOthers();

        $card = $item->card();
        if (!is_null($card->teamId) && in_array($item->type, [1, 2, 5]) && in_array($card->type, [7, 9, 10])) {
            $_card = Card::where('classObjectId', $card->classObjectId)->where('teamId', null)->where('type', $card->type)->first();
            if ($_card) {
                if ($card->myClass()->onTeamAccess) {
                    broadcast(
                        new CommentEvent(['itemId' => $item->id, 'comment' => $comment], $item->card()->classObjectId,
                            'MYCLASS',
                            'update')
                    )->toOthers();
                } else {
                    $item->comment = $comment;

                    broadcast(
                        new SuperEvent(
                            $item->toArray(),
                            $card->classObjectId,
                            $_card->id,
                            'comment.update')
                    )->toOthers();
                }
            }
        }

        $user = Auth::user();
        foreach ($users as $u) {
            $proLog = ProLog::create([
                'userId' => $user->id,
                'targetId' => $u->id,
                'userName' => $user->name.($user->authority == 1 ? ' 학생' : ''),
                'authority' => $user->authority,
                'eventType' => $item->type * 10,
                'eventState' => 30,
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
                'eventState' => 50,
                'eventTitle' => $comment->content,
            ]);

            broadcast(
                new ProLogEvent($proLog->toArray(), $item->userId)
            );
        }

        return [$comment];
    }
}
