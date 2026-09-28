<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemId', 'dateTime', 'problemSolvingProcess', 'attendees', 'mainActivities', 'task1', 'task2',
        'discuss1', 'schedule1', 'schedule2', 'schedule3', 'schedule4', 'feedback'
    ];

    public function item ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }
}
