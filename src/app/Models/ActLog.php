<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId','userName','authority','eventType','eventState', 'eventTitle', 'teamId', 'teamName', 'classObjectId', 'classObjectName',
    ];

    public function user () {
        return $this->belongsTo(User::class, 'userId', 'id');
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
}
