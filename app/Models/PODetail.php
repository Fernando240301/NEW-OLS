<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PODetail extends Model
{
    protected $table = 'po_details';
    protected $fillable = [
        'po_id',
        'item_name',
        'qty',
        'unit',
        'price'
    ];

    public function po()
    {
        return $this->belongsTo(DaftarPO::class, 'po_id');
    }
}

