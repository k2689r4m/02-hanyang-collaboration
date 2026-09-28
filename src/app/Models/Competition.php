<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CompetitionApply;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'year', 'month', 'day', 'title'
    ];

    public function competitionApplies () {
        return $this->hasMany(CompetitionApply::class, 'competitionId', 'id')->with('user');
    }

    public function applies () {
        return $this->hasMany(CompetitionApply::class, 'competitionId', 'id')->with('user');
    }

    public function is_applied () {
        return $this->hasMany(CompetitionApply::class, 'competitionId', 'id')->where('userId', Auth::id())->first();
    }
}
