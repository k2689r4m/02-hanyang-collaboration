<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassList extends Model
{
    use HasFactory;

//    protected $primaryKey = ['suupNo','hakbun'];
    protected $fillable = [
        'userId', 'classObjectId',
        'name',
        'suupTermNm',
        'daehakNm',
        'gwamokNm',
        'hakgwaNm',
        'haksuNo',
        'jeonggonggbnm',
        'suupNo',
        'hpNo',
        'sosokCd',
        'email',
        'hakbun',
        'suupYear',
        'suupTerm',
        'grade',
        'campusCd',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'userId', 'id')->first();
    }

    public function withUser() {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function user_info() {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function class_object() {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId')->with('class_lists', 'team_info', 'my_class', 'class_apply');
    }
    public function userCount() {
        return $this->belongsTo(User::class, 'userId', 'id')->count();
    }

    public function team() {
        $tms = TeamMember::where('userId', $this->userId)->get();
        foreach ($tms as $tm) {
            $team = $tm->team();
            if ($team->classObjectId == $this->classObjectId) {
                return $team;
            }
        }

        return null;
    }

    public function withMyClasses() {
        $classObject = ClassObject::find($this->classObjectId);
        return $this->hasOne(MyClass::class, 'id', 'classObjectId');
    }

    public function classObject()
    {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId')->first();
    }

    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'classObjectId', 'classObjectId')->with('user2');
    }

    public function myClassUser()
    {
        return $this->belongsTo(MyClass::class, 'classObjectId', 'classObjectId');
    }

    public function withClassObject()
    {
        return $this->belongsTo(ClassObject::class, 'id', 'classObjectId')->first();
    }

    public function class_apply()
    {
        return $this->hasOne(ClassApply::class, 'classObjectId', 'classObjectId');
    }

    public function withClassApply($classObjectId) {
        return $this->hasOne(ClassApply::class, 'id', $classObjectId)->first();
    }

    public function applyInfo () {
        return $this->hasOne(ClassApply::class, 'classObjectId', 'classObjectId');
    }
}
