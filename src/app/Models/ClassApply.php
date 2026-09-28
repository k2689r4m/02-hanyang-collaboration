<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class ClassApply extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'itemId',

        'code',

        'state',
        'mode',
        'type',
        'grade',
        'size1',
        'size2',
        'proSize1',
        'proSize2',
        'proSize3',
        'daehak',
        'department',
        'major',
        'special',
        'special2',
        'special3',
        'special4',
        'korName',
        'engName',
        'gradesPoint',
        'lecturePoint',
        'exercisePoint',
        'description',
        'meca',
        'agency',
        'expert',
        'role1',
        'role2',
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
        'expected8',
        'aplName',
        'aplSign',
        'aplOrg',
        'aplTel',
        'aplPhone',
        'aplEmail',
        'applicant',
        'agree1',
        'duration',
        'duration2',
//        'conName',
//        'conPer',
        'contribute',
        'conDescription',
        'agree2',
        'agree3',
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
    ];

    protected $casts = [
        'planDetail' => 'array',
        'contribute' => 'array',
        'applicant' => 'array',
    ];

    public function classObject()
    {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId');
    }

    public function classObject2()
    {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId')->orderBy('gnjSosokNm', 'desc');
    }
    public function classObject3()
    {
        return $this->hasOne(ClassObject::class, 'id', 'classObjectId')->orderBy('gwamokNm', 'desc');
    }

    public function classTeams2 () {
        return $this->hasMany(Team::class, 'classObjectId', 'classObjectId')->select('id', 'classObjectId')->with('teamMembers2');
    }

    public function item ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }

    public function itemUser ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id');
    }

    public function problemCount(){
        return $this->hasMany(Card::class, 'classObjectId', 'classObjectId')->count();
    }


    public function classList(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'classObjectId')->first();
    }
    
    public function classListCount(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'classObjectId')->count();
    }

    public function classListCount2(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'classObjectId')->whereNotNull('userId')->count();
    }

    public function classListCount3(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'classObjectId')->whereNotNull('userId');
    }

    public function classListUserCount(){
        return $this->hasOne(ClassList::class, 'classObjectId', 'classObjectId')->with(['user_info' => function ($query){
            $query->select('id','name');
        }])->count();
    }

    public function teamCount(){
        return $this->hasMany(Team::class, 'classObjectId', 'classObjectId')->count();
    }


    public function prt () {
        return $this->hasMany(Card::class, 'classObjectId', 'id')->with(['items2' => function ($query) {
            $query->whereIn('type', [1, 2, 5]);
        }])->whereHas('items2')->select('id', 'classObjectId');
    }
    public function teams(){
        return $this->hasMany(Team::class, 'classObjectId', 'id')->get();
    }

    public function cards () {
        return $this->hasMany(Card::class, 'classObjectId', 'classObjectId');
    }

    public function itemIdToCard()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }

    public function consultingApply () {
        return $this->hasOne(ConsultingApply::class,'classObjectId', 'classObjectId');
    }



}
