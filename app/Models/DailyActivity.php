<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DailyActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_date',
        'jenis_kegiatan',
        'project_number',
        'uraian',
        'link',
        'evidence',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'reject_reason'
    ];

    public function user()
    {
        return $this->belongsTo(SysUser::class, 'user_id', 'userid');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
public function project()
{
    return $this->belongsTo(Project::class, 'project_number', 'codeid');
}
}