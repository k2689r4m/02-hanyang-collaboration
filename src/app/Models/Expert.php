<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;

    protected $fillable = [
        'classObjectId', 'write', 'userId'
    ];

    public function user_info ()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function my_class ()
    {
        return $this->hasOne(MyClass::class, 'classObjectId', 'classObjectId');
    }
}
