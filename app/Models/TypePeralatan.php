<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypePeralatan extends Model
{
    use HasFactory;
    protected $table = 'type_peralatan';
    protected $fillable = ['type', 'id_peralatan'];
     public $timestamps = false; // Matikan automatic created_at / updated_at
    
    public function jenis()
    {
        return $this->belongsTo(JenisPeralatan::class, 'id_peralatan', 'id');
    }
}

