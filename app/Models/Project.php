<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'app_workflow'; // nama tabel di database
    protected $fillable = ['nama', 'alias']; // sesuaikan field
    public $timestamps = false; // <--- matikan timestamps
}
