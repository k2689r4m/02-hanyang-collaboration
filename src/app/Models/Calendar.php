<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'classObjectId', 'teamId', 'dateTime', 'content'
    ];



    public function classObject ()
    {
        return $this->belongsTo(ClassObject::class, 'classObjectId', 'id');
    }

    public function classApply(){
        return $this->belongsTo(ClassApply::class, 'classObjectId', 'classObjectId');
    }
}
