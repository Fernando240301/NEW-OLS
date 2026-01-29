<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypePeralatan extends Model
{
    protected $table = 'type_peralatan';
    protected $fillable = [
        'type','id_peralatan'
    ];
}

