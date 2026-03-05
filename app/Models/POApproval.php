<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SysUser;
class POApproval extends Model
{
    protected $table = 'po_approvals';
    protected $fillable = [
        'po_id',
        'user_id',
        'level',
        'is_approved',
        'approved_at'
    ];

    public function user()
{
    return $this->belongsTo(SysUser::class, 'user_id', 'userid');
}

    public function po()
    {
        return $this->belongsTo(DaftarPO::class, 'po_id');
    }
}

