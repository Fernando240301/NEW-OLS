<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PPJBDetail extends Model
{
    protected $table = 'ppjb_details';

    protected $fillable = [
        'ppjb_id',
        'qty',
        'satuan',
        'uraian',
        'harga',
        'total',
        'keterangan',
    ];

    public function ppjb()
    {
        return $this->belongsTo(PPJB::class, 'ppjb_id', 'id');
    }
}
