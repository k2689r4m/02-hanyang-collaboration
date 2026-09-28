<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'teamId'
    ];

    public function user ()
    {
        return $this->belongsTo(User::class, 'userId', 'id')->first();
    }

    //teamMember에서 teamMember의 id값으로 userId값을 빠른 접근 가능하도록 함수 설정
    public function withUser ()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function user_info ()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function team ()
    {
        return $this->belongsTo(Team::class, 'teamId', 'id')->first();
    }

    public function withTeam ()
    {
        return $this->belongsTo(Team::class, 'teamId', 'id');
    }
}
