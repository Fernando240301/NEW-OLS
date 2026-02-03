<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefTipePeralatan extends Model
{
    protected $table = 'ref_tipe_peralatan';

    protected $fillable = [
        'nama',
        'jenis',
        'jns_ijin',
    ];
}
