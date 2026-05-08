<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PPJBApproval; // pastikan ada
use App\Models\PPJBDetail;
class PPJB extends Model
{   
    const LEVEL_MANAGER = 1;
    const LEVEL_QA = 2;
    const LEVEL_DIREKTUR = 3;
    use HasFactory;
    protected $table = 'PPJB';
  protected $fillable = [
   'nosurat', 'dari', 'tanggal_permohonan', 'tanggal_dibutuhkan', 'project', 'pekerjaan', 'PIC', 'lokasi_project', 'transport',
   'status_ppjb','approval_level'
];

     public $timestamps = false; // Matikan automatic created_at / updated_at
    
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function jenis()
    {
        return $this->belongsTo(JenisPeralatan::class, 'jenis_id');
    }
    public function market()
    {
        return $this->belongsTo(Market::class, 'pemohonid');
    }
    public function SysUser()
{
    // 'createuser' adalah nama kolom foreign key di tabel PPJB
    // 'userid' adalah primary key di tabel sys_users
    return $this->belongsTo(SysUser::class, 'createuser', 'userid');

}
  public function details()
    {
        return $this->hasMany(PPJBDetail::class, 'ppjb_id', 'id');
    }
    public function approvals()
    {
        return $this->hasMany(PPJBApproval::class, 'ppjb_id', 'id');
    }

}
