<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
