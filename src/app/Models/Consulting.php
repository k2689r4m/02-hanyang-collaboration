<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\ConsultingApply;
use App\Models\User;

class Consulting extends Model
{
    use HasFactory;

    protected $fillable = [
        'startDateTime',
        'endDateTime',
        'consultantId', 'title',
    ];

    public function applies () {
        return $this->hasMany(ConsultingApply::class, 'consultingId', 'id')->with('user');
    }

    public function admissionApplies () {
        return $this->hasMany(ConsultingApply::class, 'consultingId', 'id')->where('state', 1)->with('user')->with('consulting');
    }

    public function consultant () {
        return $this->belongsTo(User::class, 'consultantId', 'id');
    }

    public function applier() {
        return $this->hasMany(ConsultingApply::class, 'consultingId', 'id')->where('state', 1);
    }

    public function isAccepted() {
        return $this->hasMany(ConsultingApply::class, 'consultingId', 'id')->where('userId', Auth::id());
    }

    public function consulting () {
        return $this->belongsTo(Consulting::class, 'consultingId', 'id');
    }
}
