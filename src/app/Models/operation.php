<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemId',
        'semester',
        'college',
        'lectureName',
        'grade',
        'division',
        'grades',
        'professor',
        'size',
        'icpblType',
        'summary',
        'classGoal',
        'method',
        'basicPlan',
        'title',
        'role',
        'scenario',
        'process',
        'outputType1',
        'outputType2',
        'outputType3',
        'outputType4',
        'outputType5',
        'outputType6',
        'outputType7',
        'outputType8',
        'finalOutput',
        'mainStudent',
        'sTitle',
        'sName',
        'sRole1',
        'sRole2',
        'sLink',
        'sFeedback',
        'sOpinion',
        'pr1',
        'pr2',
        'pr3',
        'pr4',
        'pr5',
        'pr6',
    ];

    protected $casts = [
        'process' => 'array',
    ];
}
