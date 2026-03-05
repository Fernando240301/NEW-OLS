<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivity extends Model
{
    protected $table = 'dailyactivities';

    protected $fillable = [
        'user_id','tanggal','jenis_kegiatan','project_number','uraian','status'
    ];

    public $timestamps = false;

    public function evidences()
{
    return $this->hasMany(DailyActivityEvidence::class, 'dailyactivity_id');
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
