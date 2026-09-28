<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId', 'itemId', 'content', 'fileName', 'filePathName',
    ];

    public function withUser() {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function isDeletable($userId) {
        if ($this->item()->card()->isOwned($userId) || $this->userId == $userId) {
            return true;
        }
        return false;
    }

    public function item ()
    {
        return $this->belongsTo(Item::class, 'itemId', 'id')->first();
    }
}
