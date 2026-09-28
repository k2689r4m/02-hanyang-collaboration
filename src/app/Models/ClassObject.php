<?php

namespace App\Models;

use http\Client\Curl\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClassObject extends Model
{
//    public function classObject() {
//        return $this->hasOne(MyClass::class, 'id', 'classObjectId')->first();
//    }
    use HasFactory;
//    protected $primaryKey = 'suupNo';
    protected $fillable = [
        'name',
        'suupTermNm',
        'pyegangYn',
        'suupTimes',
        'sincheongInwon',
        'hakjeom',
        'gnjHakgwaNm',
        'isuGrade',
        'suupYear',
        'suupTerm',
        'daepyoGangsaNm',
        'gnjSosokNm',
        'daepyoGangsaHp',
        'daepyoGangsaNo',
        'gwamokNm',
        'daepyoGangsaHakgwa',
        'suupTypeGb',
        'onlineGb',
        'haksuNo',
        'suupNo',
        'daepyoGangsaDaehak',
        'gnjDaehakNm',
        'gyogangsa',
        'teuksuSuupInfo',
        'isuGbNm',
        'daepyoGangsaEmail',
        'daepyoGangsaJikjong',
        'ganguiRoom',
        'campusCd',
    ];

    // $classLists = $classObject->classLists()->paginate(1);

    public function classLists() {
        return $this->hasMany(ClassList::class, 'classObjectId', 'id')->get();
    }

    public function withClassLists() {
        return $this->hasMany(ClassList::class, 'classObjectId', 'id');
    }

    public function class_lists() {
        return $this->hasMany(ClassList::class, 'classObjectId', 'id')->with(['user_info' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    public function teams(){
        return $this->hasMany(Team::class, 'classObjectId', 'id')->get();
    }

    public function withTeams(){
        return $this->hasMany(Team::class, 'classObjectId', 'id');
    }

    public function team_info(){
        if (Auth::user()->authority == 2) {
            return $this->hasMany(Team::class, 'classObjectId', 'id')->with(['team_members' => function ($query) {
                $query->where('userId', Auth::id());
            }]);
        }
        else if (Auth::user()->authority == 5) {
            if (Expert::where('classObjectId', $this->classObjectId)->where('userId', Auth::id())->first()) {
                return $this->hasMany(Team::class, 'classObjectId', 'id')->with(['team_members' => function ($query) {
                    $query->where('userId', $this->userId);
                }]);
            }
            else {
                return $this->hasMany(Team::class, 'classObjectId', 'id')->with(['team_members' => function ($query) {
                    $query->where('userId', Auth::id());
                }]);
            }
        }
        else if (Auth::user()->authority == 6) {
            if (ClassManager::where('classObjectId', $this->classObjectId)->where('userId', Auth::id())->first()) {
                return $this->hasMany(Team::class, 'classObjectId', 'id')->with(['team_members' => function ($query) {
                    $query->where('userId', $this->userId);
                }]);
            }
            else {
                return $this->hasMany(Team::class, 'classObjectId', 'id')->with(['team_members' => function ($query) {
                    $query->where('userId', Auth::id());
                }]);
            }
        }
        else {
            return $this->hasMany(Team::class, 'classObjectId', 'id')->has('is_member')->with(['team_members' => function ($query) {
                $query->where('userId', Auth::id());
            }]);
        }
    }

    public function myClass(){
        return $this->hasOne(MyClass::class, 'classObjectId', 'id');
    }

    public function my_class(){
        return $this->hasOne(MyClass::class, 'classObjectId', 'id');
    }

    public function isOwner(){
        return $this->hasOne(MyClass::class, 'classObjectId', 'id')->first()->userId == Auth::id();
    }

    public function classApply(){
        return $this->belongTo(ClassObject::class, 'classObjectId', 'id');
    }
    public function class_apply(){
        return $this->hasOne(ClassApply::class, 'classObjectId', 'id');
    }
    public function withClassApply(){
        return $this->hasOne(ClassObject::class, 'classObjectId', 'id');
    }

    public function withMyClass(){
//        return $this->hasOne(MyClass::class, 'classObjectId', 'id')->has(User::class, '')
    }

    public function cards(){
        return $this->hasMany(Card::class, 'classObjectId', 'id')->get();
    }

    //최적화 필요
    public function problems () {
        $test = $this->hasMany(Card::class, 'classObjectId', 'id')->with('withItemsWithProblems')->get();
        $sum = 0;
        $b = 0;
        foreach ($test as $_test) {
            if ($_test->withItemsWithProblems->count() > 0) {
                foreach ($_test->withItemsWithProblems as $__test) {
                    if ($__test->withProblemAnalysis) {
                        $sum += $__test->withProblemAnalysis->count();
                        $b = $__test->withProblemAnalysis->count();
                    }
                }
            }
        }
        return $b;
    }

    public function teamActCount(){
        $test = $this->hasMany(Card::class, 'classObjectId', 'id')->with('withItemsWithTeamActivity')->get();
        $sum = 0;
        $b = 0;
        foreach ($test as $_test) {
            if ($_test->withItemsWithTeamActivity->count() > 0) {
                foreach ($_test->withItemsWithTeamActivity as $__test) {
                    if ($__test->withTeamActivity) {
                        $sum += $__test->withTeamActivity->count();
                        $b = $__test->withTeamActivity->count();
                    }
                }
            }
        }
        return $b;
    }

    public function reflectionCount(){
        $test = $this->hasMany(Card::class, 'classObjectId', 'id')->with('withItemsWithReflections')->get();
        $sum = 0;
        $b = 0;
        foreach ($test as $_test) {
            if ($_test->withItemsWithReflections->count() > 0) {
                foreach ($_test->withItemsWithReflections as $__test) {
                    if ($__test->withReflectionLog) {
                        $sum += $__test->withReflectionLog->count();
                        $b = $__test->withReflectionLog->count();
                    }
                }
            }
        }
        return $b;
    }

    public function prt () {
        return $this->hasMany(Card::class, 'classObjectId', 'id')->with(['items2' => function ($query) {
            $query->whereIn('type', [1, 2, 5]);
        }])->whereHas('items2')->select('id', 'classObjectId');
    }

    public function consultingApply(){
        return $this->hasMany(ConsultingApply::class, 'id' ,'classObjectId')->first();
    }




    public function classListCount2(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'id')->whereNotNull('userId')->count();
    }

    public function classListUserCount(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'id')->with(['user_info' => function ($query){
            $query->select('id','name');
        }])->count();
    }

    public function teamCount(){
        return $this->hasMany(Team::class, 'classObjectId', 'id')->select('id', 'classObjectId')->count();
    }

//    public function teamMem() {
//        return $this->hasMany(Team::class, 'classObjectId', 'id')->select('id', 'classObjectId')->with('teamMembers2');
//    }




}
