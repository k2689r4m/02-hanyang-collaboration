<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'classObjectId', 'teamId', 'userId', 'write'
    ];
}
