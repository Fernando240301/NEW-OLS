<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lpjb extends Model
{
    protected $table = 'lpjbs';

    protected $fillable = [
        'no_lpjb',
        'ppjb_id',
        'tanggal',
        'total_budget',
        'total_realisasi',
        'selisih',
        'status',
        'journal_id'
    ];

    // Relasi ke PPJB
    public function ppjb()
    {
        return $this->belongsTo(Ppjbnew::class, 'ppjb_id');
    }

    // Relasi ke detail
    public function details()
    {
        return $this->hasMany(LpjbDetail::class);
    }

    public function approvals()
    {
        return $this->hasMany(LpjbApproval::class);
    }

    public function hasApproved($role)
    {
        return $this->approvals()
            ->where('role', $role)
            ->where('status', 'approved')
            ->exists();
    }
}
