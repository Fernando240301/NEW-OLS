<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ppjbnew extends Model
{
    protected $fillable = [
        'no_ppjb',
        'kepada',
        'dari',
        'refer_project',
        'tanggal_permohonan',
        'tanggal_dibutuhkan',
        'project_no',
        'pekerjaan',
        'pic',
        'kas_account_id',
        'status',
        'total'
    ];

    public function details()
    {
        return $this->hasMany(PpjbDetailnew::class, 'ppjb_id');
    }
}
