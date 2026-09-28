<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'year', 'semester'
    ];

    protected $casts = [
        'cardsNum' => 'object',
    ];

    public function user ()
    {
        return $this->belongsTo(User::class, 'userId', 'id')->first();
    }

    public function cards () {
        return $this->hasMany(Card::class, 'myPageId', 'id')->with('withItems')->get();
    }

    public function withCards () {
        return $this->hasMany(Card::class, 'myPageId', 'id');
    }

    public function items () {
        return $this->hasMany(Item::class, 'cardId', 'id');
    }
}
