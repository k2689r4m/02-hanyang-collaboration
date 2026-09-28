<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MyClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'classObjectId'
    ];

    protected $casts = [
        'cardsNum' => 'array',
        'onClassTalk' => 'boolean',
        'onTeamTalk' => 'boolean',
        'onOrientation' => 'boolean',
        'onReflectionLog' => 'boolean',
        'onEvaluation' => 'boolean',
        'onTeamActivity' => 'boolean',
        'onProblemAnalysis' => 'boolean',
        'onTeamAccess' => 'boolean',
        'onTeamOrientation' => 'boolean',
        'onSetting' => 'boolean',
    ];

    public function classObject() {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId')->first();
    }

    public function classObject2 () {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId');
    }

    public function withClassObject() {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId');
    }

    public function withClassApply() {
        return $this->hasOne(ClassApply::class, 'classObjectId', 'classObjectId');
    }

    public function class_object() {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId')->with('class_lists', 'team_info', 'my_class', 'class_apply');
    }

    public function classLists() {
        return ClassList::where('classObjectId', $this->classObjectId)
            ->with(['withUser' => function ($query) {
            $query->select('id', 'name');
        }])->get();
    }

    public function classLists2() {
        return ClassList::where('classObjectId', $this->classObjectId)
            ->with(['withUser' => function ($query) {
            $query->select('id', 'name', 'email');
        }])->get();
    }

    public function classLists3() {
        return $this->hasMany(ClassList::class, 'classObjectId', 'classObjectId');
    }

    public function members() {
        return $this->hasMany(ClassList::class, 'classObjectId', 'classObjectId')->get();
    }

    public function cards () {
        return $this->hasMany(Card::class, 'classObjectId', 'classObjectId')->get();
    }

    public function withCards () {
        return $this->hasMany(Card::class, 'classObjectId', 'classObjectId');
    }

    public function exclusiveCards () {
        return $this->hasMany(Card::class, 'classObjectId', 'classObjectId')->with('withItems')->whereNull('teamId')->get();
    }

    public function isMember ($userId) {
        return $this->hasMany(ClassList::class, 'classObjectId', 'classObjectId')->where('userId', Auth::id())->first() ? true : false;
    }

    public function isMember2 ($userId) {
        return $this->hasMany(ClassList::class, 'classObjectId', 'classObjectId')->where('userId', $userId)->first() ? true : false;
    }

    public function teams () {
        return $this->hasMany(Team::class ,'classObjectId', 'classObjectId')->get();
    }

    public function teams2 () {
        return $this->hasMany(Team::class ,'classObjectId', 'classObjectId');
    }

    public function user(){
        return $this->belongsTo(User::class, 'userId', 'id')->first();
    }

    public function user2(){
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function isPermitted () {
        if (Auth::user()->authority == 5) {
            $expert = Expert::where('classObjectId', $this->classObjectId)->where('userId', Auth::id())->first();
            if ($expert) {
                return true;
            }
            else {
                return false;
            }
        }
        if (Auth::user()->authority == 6) {
            $manager = ClassManager::where('classObjectId', $this->classObjectId)->where('userId', Auth::id())->first();
            if ($manager) {
                return true;
            }
            else {
                return false;
            }
        }

        return  $this->userId == Auth::id() || $this->hasMany(ClassList::class, 'classObjectId', 'classObjectId')->where('userId', Auth::id())->first() ? true : false;
    }

    public function withUser () {
        return $this->belongsTo(User::class, 'userId', 'id');
    }



    public function classApply () {
        return $this->hasOne(ClassApply::class, 'classObjectId', 'classObjectId');
    }

    public function classMembers () {
        return $this->hasMany(ClassList::class, 'classObjectId', 'classObjectId')->select('id', 'classObjectId')->whereNotNull('userId');
    }
    public function classTeams () {
        return $this->hasMany(Team::class, 'classObjectId', 'classObjectId')->select('id', 'classObjectId');
    }
    public function classTeams2 () {
        return $this->hasMany(Team::class, 'classObjectId', 'classObjectId')->select('id', 'classObjectId')->with('teamMembers2');
    }
    public function prt () {
        return $this->hasMany(Card::class, 'classObjectId', 'classObjectId')->with(['items2' => function ($query) {
            $query->whereIn('type', [1, 2, 5]);
        }])->whereHas('items2')->select('id', 'classObjectId', 'userId');
    }
    public function today () {
        $today = date('Y-m-d 00:00:00', strtotime('now'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now'));
        return $this->hasMany(Calendar::class, 'classObjectId', 'classObjectId')->whereBetween('dateTime', [$today, $dayend])->select('id', 'classObjectId', 'dateTime', 'content')->latest()->take(2);
    }
    public function nextDay () {
        $today = date('Y-m-d 00:00:00', strtotime('now + 1 DAY'));
        $dayend = date('Y-m-d 23:59:59', strtotime('now + 1 DAY'));
        return $this->hasMany(Calendar::class, 'classObjectId', 'classObjectId')->whereBetween('dateTime', [$today, $dayend])->select('id', 'classObjectId', 'dateTime', 'content')->latest()->take(2);
    }
}
