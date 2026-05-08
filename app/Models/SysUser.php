<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\SysUserDetail;
use App\Models\DailyActivity;
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

    public function role()
    {
        return $this->belongsTo(Role::class, 'rolesid', 'id');
    }
    public function detail()
    {
        return $this->hasOne(SysUserDetail::class, 'userid', 'userid');
    }

    public function dailyActivities()
    {
        return $this->hasMany(DailyActivity::class, 'user_id', 'userid');
    }
    public function user()
{
    return $this->belongsTo(SysUser::class, 'user_id', 'userid');
}
    
}
