<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ppjbnew extends Model
{
    protected $fillable = [
        'no_ppjb',
        'workflow_id',
        'pr_workflow_id',
        'jenis_pengajuan',
        'dari',
        'kepada',
        'tanggal_permohonan',
        'tanggal_mulai',
        'tanggal_selesai',
        'pekerjaan',
        'pic',
        'kas_account_id',
        'status',
        'journal_id',
        'total',
    ];

    public function details()
    {
        return $this->hasMany(PpjbDetailnew::class, 'ppjb_id');
    }

    public function lpjbs()
    {
        return $this->hasMany(Lpjb::class, 'ppjb_id');
    }

    public function approvals()
    {
        return $this->hasMany(PpjbnewApproval::class, 'ppjb_id');
    }

    public static function getManagerUsernameByDepartment($dari)
    {
        $dari = strtolower($dari);
        $dari = str_replace(['.', ',', '-'], '', $dari);
        $dari = trim($dari);

        if (str_contains($dari, 'operasional')) {
            return 'OCM';
        }

        if (str_contains($dari, 'marketing')) {
            return 'Deam';
        }

        if (str_contains($dari, 'keuangan')) {
            return 'Fitrif';
        }

        if (str_contains($dari, 'hr') || str_contains($dari, 'ga')) {
            return 'Linahg';
        }

        if (str_contains($dari, 'it')) {
            return 'Beibys';
        }

        return null;
    }

    public function lpjb()
    {
        return $this->hasOne(\App\Models\Lpjb::class, 'ppjb_id');
    }
}
