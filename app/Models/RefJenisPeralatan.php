<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefJenisPeralatan extends Model
{
    protected $table = 'ref_jenis_peralatan';

    protected $fillable = [
        'nama',
        'catatan',
    ];
}
