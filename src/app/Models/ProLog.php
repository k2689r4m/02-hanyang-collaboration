<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId','targetId','userName','authority','eventType','eventState','eventTitle', 'title'
    ];

    public function user () {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
