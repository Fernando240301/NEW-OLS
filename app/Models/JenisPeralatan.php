<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPeralatan extends Model
{
    protected $table = 'jenis_peralatan';
    protected $fillable = [
        'nama_peralatan',
    ];
}

