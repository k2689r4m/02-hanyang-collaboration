<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'content', 'adminAnswer'
    ];

    public function getPage()
    {
        return faq::where('id', '<=', $this->id)->count();
    }
}
