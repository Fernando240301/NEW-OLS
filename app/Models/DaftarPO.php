<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\JenisPeralatan;



class DaftarPO extends Model
{
    protected $table = 'daftarpo';
    public $timestamps = false;
   protected $fillable = [
    'no_surat',
    'namapengaju',
    'project',
    'to',
    'adress',
    'date',
    'attention',
    'shipto',
    'shipdate',
    'description',
    'qty',
    'unit',
    'harga',
    'file_penawaran',
    'status_daftarpo',
    'approval_level',
];

    public function approvals()
{
    return $this->hasMany(POApproval::class, 'po_id');
}

public function details()
{
    return $this->hasMany(PODetail::class, 'po_id');
}
public function jenis()
{
    return $this->belongsTo(JenisPeralatan::class, 'jenis_id');
}
 public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function SysUser()
    {
        return $this->belongsTo(SysUser::class, 'createuser', 'userid');
    }
public function canApprove()
{
    if (!auth()->check()) {
        return false;
    }

    return $this->approvals()
        ->where('user_id', auth()->user()->userid)
        ->where('is_approved', false)
        ->exists();
}



}
