<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BasicApply extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'basicId', 'state'
    ];

    public function user () {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function basic () {
        return $this->belongsTo(Basic::class, 'basicId', 'id');
    }
//    public function basic_with () {
//        return $this->belongsTo(User::class, 'userId', 'id')->with('user');
//    }
}
