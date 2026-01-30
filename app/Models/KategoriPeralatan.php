<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPeralatan extends Model
{
    use HasFactory;

    protected $table = 'kategori_peralatan'; // nama tabel di database
    protected $fillable = ['nama','alias']; // sesuaikan field
    public $timestamps = false; // <--- matikan timestamps
}