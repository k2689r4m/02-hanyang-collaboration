<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Kyslik\ColumnSortable\Sortable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use Sortable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'contact',
        'basicTarget',
        'consultingTarget',
        'code',
        'authority',
        'email_verified_at',
        'uuid',
        'social',
        'jikwiGb',
        'jaejikYn',
        'daepyoUserGbYn',
        'daehakNm',
        'jikjongGb',
        'gaeinNo',
        'sinbunGbNm',
        'sosokNm',
        'iphakYear',
        'userNm',
        'sinbunGb',
        'userGb',
        'sinbunGbEnm',
        'sosokCd',
        'sosokEnm',
        'userGbNm',
        'sosokId',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $sortable = [
        'id',
        'name',
        'email',
        'authority',
        'social',
        'daehakNm',
        'contact',
        'created_at',
    ];

    public function myClasses ()
    {
        return $this->hasMany(MyClass::class, 'userId', 'id')->get();
    }

    public function team ()
    {
        return $this->hasMany(TeamMember::class, 'userId', 'id');
    }

    public function actLog() {
        return $this->hasMany(ActLog::class, 'userId', 'id');
    }
}
