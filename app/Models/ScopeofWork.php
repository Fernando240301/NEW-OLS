<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RefJenisPeralatan;
use App\Models\KategoriPeralatan;
use App\Models\RefTipePeralatan;

class ScopeofWork extends Model
{
    protected $table = 'lov_jenis_peralatan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'workflowid',
        'lokasi',
        'item',
        'jenis',
        'tipe',
        'kategori',
        'jumlah',
        'harga',
    ];

    /* ===============================
     | RELATION
     =============================== */

    // 🔗 Jenis Peralatan
    public function jenisRel()
    {
        return $this->belongsTo(
            RefJenisPeralatan::class,
            'jenis', // FK di lov_jenis_peralatan
            'id'     // PK di ref_jenis_peralatan
        );
    }

    // 🔗 Tipe Peralatan (opsional kalau mau dipakai)
    public function tipeRel()
    {
        return $this->belongsTo(
            RefTipePeralatan::class,
            'tipe',
            'id'
        );
    }

    // 🔗 Kategori Peralatan
    public function kategoriRel()
    {
        return $this->belongsTo(
            KategoriPeralatan::class,
            'kategori',
            'id'
        );
    }
}
