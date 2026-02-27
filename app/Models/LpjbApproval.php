<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpjbApproval extends Model
{
    protected $fillable = [
        'lpjb_id',
        'user_id',
        'role',
        'step_order',
        'status',
        'note',
        'approved_at'
    ];

    public function lpjb()
    {
        return $this->belongsTo(Lpjb::class);
    }

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'user_id', 'userid');
    }
}
