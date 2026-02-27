<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpjbDetail extends Model
{
    protected $table = 'lpjb_details';

    protected $fillable = [
        'lpjb_id',
        'ppjb_detail_id', // ← tambahkan ini
        'coa_id',
        'uraian',
        'satuan',
        'budget_qty',
        'budget_harga',
        'budget_subtotal',
        'real_qty',
        'real_harga',
        'real_subtotal',
        'bukti_file'
    ];

    public function lpjb()
    {
        return $this->belongsTo(Lpjb::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_id');
    }

    public function ppjbDetail()
    {
        return $this->belongsTo(PpjbDetailnew::class, 'ppjb_detail_id');
    }
}
