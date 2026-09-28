<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\BasicApply;
use App\Models\User;

class Basic extends Model
{
    use HasFactory;

    protected $fillable = [
        'startDateTime',
        'endDateTime',
        'consultantId', 'title', 'maxMemberCount'
    ];

    public function applies () {
        return $this->hasMany(BasicApply::class, 'basicId', 'id')->with('user');
    }

    public function admissionApplies () {
        return $this->hasMany(BasicApply::class, 'basicId', 'id')->where('state', 1)->with('user')->with('basic');
    }

    public function consultant () {
        return $this->belongsTo(User::class, 'consultantId', 'id')->select('id', 'name');
    }

    public function applier() {
        return $this->hasMany(BasicApply::class, 'basicId', 'id')->where('state', 1);
    }

    public function isAccepted() {
        return $this->hasMany(BasicApply::class, 'basicId', 'id')->where('userId', Auth::id());
    }
}
