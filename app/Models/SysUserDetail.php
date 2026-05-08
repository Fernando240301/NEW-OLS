<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysUser;
class SysUserDetail extends Model
{
    protected $table = 'sys_users_detail'; // sesuaikan nama tabel kamu
    protected $primaryKey = 'userid';
    public $timestamps = false;

    protected $fillable = [
        'userid',
        'nama',
        'nip',
        'jenis_kelamin',
        'no_telp',
        'npwp',
        'academic',
        'jurusan',
        'univeersity'
    ];
     public function user()
    {
        return $this->belongsTo(SysUser::class, 'userid', 'userid');
    }
}