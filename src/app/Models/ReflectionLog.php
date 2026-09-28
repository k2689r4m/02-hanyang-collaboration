<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReflectionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemId', 'studentId', 'name', 'content1', 'content2', 'content3', 'content4', 'content5', 'content6', 'content7'
    ];

    public function item ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }
}
