<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultingApply extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'consultingId', 'state', 'classObjectId'
    ];

    public function user () {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function consulting () {
        return $this->belongsTo(Consulting::class,'consultingId', 'id');
    }

    public function classApply () {
        return $this->hasOne(ClassApply::class,'classObjectId', 'classObjectId');
    }
}
