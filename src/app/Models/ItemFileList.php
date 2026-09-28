<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemFileList extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemId', 'type', 'fileName', 'pathName', 'imgUrl'
    ];

    public function item ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }
}
