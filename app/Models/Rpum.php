<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rpum extends Model
{
    protected $fillable = [
        'ppjb_id',
        'tanggal_transfer',
        'jumlah',
        'bukti_transfer',
        'verified_by',
        'verified_at'
    ];

    public function ppjb()
    {
        return $this->belongsTo(Ppjbnew::class, 'ppjb_id');
    }
}