<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    use HasFactory;

    protected $table = 'penawaran';

    protected $fillable = [
        'nosurat',
        'judul',
        'namaclient',
        'pic',
        'picmit',
        'tanggal',
        'status',
        'approved_by',
        'approved_at',
        'barcode',
        'harga',
        'surat',
        'pdf',
        'approved_word'
    ];

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
        return $this->belongsTo(SysUser::class, 'createuser', 'userid');
    }

    public function picMitUser()
    {
        return $this->belongsTo(SysUser::class, 'picmit', 'userid');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    protected $appends = ['can_approve'];

public function getCanApproveAttribute()
{
    if (!$this->surat) {
        return false;
    }

    if ($this->barcode) {
        return false;
    }

    return true;
}

}
