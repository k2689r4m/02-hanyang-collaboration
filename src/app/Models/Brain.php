<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brain extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'itemId', 'content', 'color'
    ];

    public function withUser () {
        return $this->belongsTo(User::class, 'userId', 'id')->select('id', 'name');
    }

    public function item () {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }
}
