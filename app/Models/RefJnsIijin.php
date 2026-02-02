<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefJnsIjin extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'ref_jns_ijin';

    // Primary key bukan id
    protected $primaryKey = 'jns_ijin';

    // Primary key bukan auto increment
    public $incrementing = false;

    // Karena char/string
    protected $keyType = 'string';

    // Tidak pakai timestamps (karena tabel kamu tidak punya created_at, updated_at)
    public $timestamps = false;

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'jns_ijin',
        'nama_jns_ijin',
        'kode',
        'tipe',
        'masa_tahun',
        'masa_bulan',
        'retribusi',
        'bidang',
        'aktif',
        'order_index',
        'oss',
    ];
}
