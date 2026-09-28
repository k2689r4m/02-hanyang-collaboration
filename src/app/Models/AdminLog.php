<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId','userName','authority','eventTitle','eventType',
    ];

    public function user () {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
