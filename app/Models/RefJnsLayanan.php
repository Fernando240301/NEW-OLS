<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefJnsLayanan extends Model
{
    use HasFactory;

    protected $table = 'ref_jns_layanan'; // nama tabel di database
    protected $fillable = ['nama_layanan','alias']; // sesuaikan field
    public $timestamps = false; // <--- matikan timestamps
}