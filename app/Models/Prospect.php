<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;
    protected $table = 'prospect';
    protected $fillable = [
    'judul',
    'klient',
    'alat',
    'catatan',
    'status',
    'tanggal',
    'sales',
    'file',
    'createuser',
    'createdate'
    ];


protected $casts = [
    'file' => 'array',
    'tanggal' => 'date',
];


     public $timestamps = false; // Matikan automatic created_at / updated_at
    
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function jenis()
{
    return $this->belongsTo(JenisPeralatan::class, 'alat', 'id');
}


    public function market()
    {
        return $this->belongsTo(Market::class, 'pemohonid');
    }
    public function SysUser()
{
    // 'createuser' adalah nama kolom foreign key di tabel prospect
    // 'userid' adalah primary key di tabel sys_users
    return $this->belongsTo(SysUser::class, 'createuser', 'userid');
}
}
