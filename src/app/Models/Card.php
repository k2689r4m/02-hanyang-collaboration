<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Expression;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'myPageId', 'userId', 'classObjectId', 'type', 'teamId', 'itemsNum'
    ];

    protected $casts = [
        'itemsNum' => 'object',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'cardId', 'id')->first();
    }

    public function createItem() {
        return $this->hasMany(Item::class, 'cardId', 'id');
    }

    public function myPage ()
    {
        return $this->belongsTo(MyPage::class, 'myPageId', 'id')->first();
    }

    public function withMyPage ()
    {
        return $this->belongsTo(MyPage::class, 'myPageId', 'id');
    }

    public function items () {
        return $this->hasMany(Item::class, 'cardId', 'id')->get();
    }

    public function items2 () {
        return $this->hasMany(Item::class, 'cardId', 'id')->select('id', 'cardId', 'type', 'userId');
    }

    public function items3 () {
        return $this->hasMany(Item::class, 'cardId', 'id');
    }

    public function ItemLists () {
        return $this->hasMany(Item::class, 'cardId', 'id');
//        return $this->hasMany(Item::class, 'itemsNum', 'id');
    }

    public function withItemsWithProblems () {
        return $this->hasMany(Item::class, 'cardId', 'id')->with('withProblemAnalysis');
    }

    public function withItemsWithReflections () {
        return $this->hasMany(Item::class, 'cardId', 'id')->with('withReflectionLog');
    }

    public function withItemsWithTeamActivity () {
        return $this->hasMany(Item::class, 'cardId', 'id')->with('withTeamActivity');
    }

    public function classApplyItems () {
        return $this->hasMany(Item::class, 'cardId', 'id')->where('type', 4)->orderBy('id', 'desc')->with('classApplyObject');
    }

    public function withItems () {
//        if (
//            $this->type == 10
//        ) {
//            return $this->hasMany(Item::class, 'type', 'type')->with('withImages');
//        }
//        else if ($this->type == 7) {
//            return $this->hasMany(Item::class, 'type', 'type')->with('withImages');
//        }
//        else if ($this->type == 0) {
//            return $this->hasMany(Item::class, 'cardId', 'id')->with('withImages');
//        }
//        else {
            return $this->hasMany(Item::class, 'cardId', 'id')
                ->with('withImages', 'withProblemAnalysis', 'withReflectionLog', 'withClassApply', 'withComments', 'withTeamActivity', 'withOperationResult', 'withBrains', 'withBasicApply', 'withConsultingApply');
//        }
    }

    public function team () {
        return $this->belongsTo(Team::class, 'teamId', 'id')->first();
    }

    public function withTeam () {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }

    public function teamMember(){
        return $this->belongsTo(TeamMember::class, 'teamId', 'teamId')->first();
    }

    public function myClass(){
        return $this->belongsTo(MyClass::class, 'classObjectId', 'classObjectId')->first();
    }

    public function messages () {
        return $this->hasMany(Talk::class, 'cardId', 'id')->orderBy('id', 'desc')->take(100)->get();
    }

    public function messagesAll () {
        return $this->hasMany(Talk::class, 'cardId', 'id')->get();
    }

    public function isPermitted ($userId, $applyId = null) {
        //description
        //
        //1. $userId 삭제 예정 (Auth facade 사용 예정)

        //마이페이지 소속 카드일 경우
        if (!is_null($this->myPageId)) {
            $myPage = $this->myPage();
            if (!$myPage) {
                return false;
            }
            else if ($myPage->userId != Auth::id()) {
                if (Auth::user()->authority == 4 && !is_null($applyId)) {
                    $userId = $myPage->userId;

                    $user = User::find($userId);
                    if (!$user) {
                        return false;
                    }
                    else if ($user->authority != 2) {
                        return false;
                    }

                    $consultingApply = ConsultingApply::find($applyId);
                    if (!$consultingApply) {
                        return false;
                    }

                    if ($consultingApply->userId != $myPage->userId) {
                        return false;
                    }

                    $consulting = $consultingApply->consulting()->first();
                    if (!$consulting) {
                        return false;
                    }

                    if ($consulting->consultantId != Auth::id()) {
                        return false;
                    }

                    if (strtotime($consulting->startDateTime) > strtotime('now')) {
                        return false;
                    }

                    if (strtotime($consulting->endDateTime) < strtotime('now')) {
                        return false;
                    }

                    return true;
                }
                return false;
            }
            return true;
        }
        //클래스 소속의 카드일 경우
        else if (!is_null($this->classObjectId)) {
            $myClass = $this->myClass();

            if (!$myClass) {
                return false;
            }

            //교수
            if ($myClass->userId == Auth::id()) {
                return true;
            }
            if (Auth::user()->authority == 5) {
                $expert = Expert::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first();
                if ($expert) {
                    return true;
                }
                else {
                    return false;
                }
            }
            if (Auth::user()->authority == 6) {
                $manager = ClassManager::where('classObjectId', $myClass->classObjectId)->where('userId', Auth::id())->first();
                if ($manager) {
                    return true;
                }
                else {
                    return false;
                }
            }

            //학생
            //팀 소속 카드 일 때
            if (!is_null($this->teamId)) {
                //팀의 멤버인지 확인
                return $this->team()->isMember(Auth::id());
            }
            //팀 소속이 아닌 클래스 소속의 카드 일 때
            else {
                //클래스의 멤버인지 확인
                return $myClass->isMember(Auth::id());
            }
        }
    }

    public function isOwned ($userId) {
        if (!is_null($this->myPageId)) {
            $myPage = $this->myPage();
            if (!$myPage || $myPage->userId != Auth::id()) {
                return false;
            }
            return true;
        }
        else if (!is_null($this->classObjectId)) {
            $myClass = $this->myClass();
            if (!$myClass) {
                return false;
            }

            //교수
            if ($myClass->userId == $userId) {
                return true;
            }

            return false;
        }
    }

    public function clusteredCards () {
        if ($this) {

        }

        if (!is_null($this->teamId)) {

        }
        else if (!is_null($this->classObjectId)) {

        }
    }

    public function isPermitted2 ($userId, $applyId = null) {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        //description
        //
        //1. $userId 삭제 예정 (Auth facade 사용 예정)

        //마이페이지 소속 카드일 경우
        if (!is_null($this->myPageId)) {
            $myPage = $this->myPage();
            if (!$myPage) {
                return false;
            }
            else if ($myPage->userId != $userId) {
                if ($user->authority == 4 && !is_null($applyId)) {
                    $userId = $myPage->userId;

                    if ($user->authority != 2) {
                        return false;
                    }

                    $consultingApply = ConsultingApply::find($applyId);
                    if (!$consultingApply) {
                        return false;
                    }

                    if ($consultingApply->userId != $myPage->userId) {
                        return false;
                    }

                    $consulting = $consultingApply->consulting()->first();
                    if (!$consulting) {
                        return false;
                    }

                    if ($consulting->consultantId != $userId) {
                        return false;
                    }

                    if (strtotime($consulting->startDateTime) > strtotime('now')) {
                        return false;
                    }

                    if (strtotime($consulting->endDateTime) < strtotime('now')) {
                        return false;
                    }

                    return true;
                }
                return false;
            }
            return true;
        }
        //클래스 소속의 카드일 경우
        else if (!is_null($this->classObjectId)) {
            $myClass = $this->myClass();

            if (!$myClass) {
                return false;
            }

            //교수
            if ($myClass->userId == $userId) {
                return true;
            }
            if ($user->authority == 5) {
                if (Expert::where('classObjectId', $this->classObjectId)->where('userId', $userId)->first()) {
                    return true;
                }
                else {
                    return false;
                }
            }
            if ($user->authority == 6) {
                if (ClassManager::where('classObjectId', $this->classObjectId)->where('userId', $userId)->first()) {
                    return true;
                }
                else {
                    return false;
                }
            }

            //학생
            //팀 소속 카드 일 때
            if (!is_null($this->teamId)) {
                //팀의 멤버인지 확인
                return $this->team()->isMember2($userId);
            }
            //팀 소속이 아닌 클래스 소속의 카드 일 때
            else {
                //클래스의 멤버인지 확인
                return $myClass->isMember2($userId);
            }
        }
    }
}
