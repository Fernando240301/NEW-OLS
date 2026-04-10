<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'sys_users';

    protected $primaryKey = 'userid';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'fullname',
        'rolesid'
    ];

    protected $hidden = [
        'password'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'rolesid', 'id');
    }
}
