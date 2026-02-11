<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPJBApproval extends Model
{
    use HasFactory;

    // Ganti sesuai nama tabel sebenarnya
    protected $table = 'ppjb_approvals'; 

    protected $fillable = [
        'ppjb_id',
        'user_id',
        'level',
        'is_approved',
        'approved_at'
    ];

    public $timestamps = true; // kalau ada created_at & updated_at
}
