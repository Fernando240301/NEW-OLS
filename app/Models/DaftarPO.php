<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarPO extends Model
{
    protected $table = 'daftar_po';

    protected $fillable = [
         'no_po',
        'nama_pengaju',
        'pr_number',
        'to',
        'address',
        'date',
        'attention',
        'ship_to',
        'ship_date',
        'description',
        'qty',
        'unit',
        'unit_price',
        'dokumen_penawaran'
    ];

    // Optional: format tanggal otomatis
    protected $casts = [
        'date' => 'date',
        'ship_date' => 'date',
    ];
}