<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemId', 'studentId', 'name', 'content1', 'content2', 'content3', 'content4'
    ];

    public function item ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }
}
