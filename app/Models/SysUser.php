<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SysUser extends Authenticatable
{
    protected $table = 'sys_users';
    protected $primaryKey = 'userid';
    protected $keyType = 'int';        // atau 'string' sesuai tipe kolom
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'fullname',
        'active'
    ];

    protected $hidden = [
        'password',
    ];

    public function getNameAttribute()
    {
        return $this->fullname;
    }

    public function adminlte_profile_url()
    {
        return url('/activity-log');
    }
}
