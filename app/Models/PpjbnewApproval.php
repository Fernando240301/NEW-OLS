<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpjbnewApproval extends Model
{
    protected $table = 'ppjbnew_approvals';

    protected $fillable = [
        'ppjb_id',
        'user_id',
        'role',
        'step_order',
        'status',
        'note',
        'approved_at'
    ];

    public function ppjb()
    {
        return $this->belongsTo(Ppjbnew::class, 'ppjb_id');
    }

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'user_id', 'userid');
    }
}
