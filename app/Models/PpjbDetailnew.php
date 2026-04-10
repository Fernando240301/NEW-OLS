<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpjbDetailnew extends Model
{
    protected $fillable = [
        'ppjb_id',
        'coa_id',
        'qty',
        'satuan',
        'uraian',
        'harga',
        'keterangan'
    ];

    public function ppjb()
    {
        return $this->belongsTo(Ppjbnew::class, 'ppjb_id');
    }

    public function coa()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'coa_id');
    }
}
