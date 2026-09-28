<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'classObjectId'
    ];

    protected $casts = [
        'cardsNum' => 'array',
    ];

    public function teamMembers () {
        return $this->hasMany(TeamMember::class, 'teamId', 'id')->get();
    }

    public function teamMaster () {
        return $this->hasMany(TeamMaster::class, 'teamId', 'id')->first();
    }

    public function teamMembers2 () {
        return $this->hasMany(TeamMember::class, 'teamId', 'id');
    }
    
    public function team_members () {
        return $this->hasMany(TeamMember::class, 'teamId', 'id')
            ->with(['user_info' => function ($query) {
                $query->select('id', 'name');
            }])
            ;
    }

    public function cards () {
        return $this->hasMany(Card::class, 'teamId', 'id')->with('withItems')->get();
    }

    public function withCards () {
        return $this->hasMany(Card::class, 'teamId', 'id', 'classObjectId', 'classObjectId');
    }

    public function isMember ($userId) {
        return $this->hasMany(TeamMember::class, 'teamId', 'id')->where('userId', Auth::id())->first() ? true : false;
    }

    public function isMember2 ($userId) {
        return $this->hasMany(TeamMember::class, 'teamId', 'id')->where('userId', $userId)->first() ? true : false;
    }

    public function myClass () {
//        return MyClass::where('classObjectId', $this->classObjectId)->first();
        return $this->belongsTo(MyClass::class, 'classObjectId', 'classObjectId')->first();
    }

    public function is_member () {
        return $this->hasMany(TeamMember::class, 'teamId', 'id')->where('userId', Auth::id());
    }

    public function isPermitted () {
        if (Auth::user()->authority == 5) {
            if (Expert::where('classObjectId', $this->classObjectId)->where('userId', Auth::id())->first()) {
                return true;
            }
            else {
                return false;
            }
        }
        if (Auth::user()->authority == 6) {
            if (ClassManager::where('classObjectId', $this->classObjectId)->where('userId', Auth::id())->first()) {
                return true;
            }
            else {
                return false;
            }
        }
        return $this->myClass()->userId == Auth::id() || $this->hasMany(TeamMember::class, 'teamId', 'id')->where('userId', Auth::id())->first() ? true : false;
    }
}
