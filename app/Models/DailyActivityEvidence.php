<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyActivityEvidence extends Model
{
    // Nama tabel sesuai DB
    protected $table = 'dailyactivityevidences';

    protected $fillable = [
        'dailyactivity_id', 'log_activity', 'link', 'file_path'
    ];

    public function activity()
    {
        // Nama foreign key sesuai kolom DB
        return $this->belongsTo(DailyActivity::class, 'dailyactivity_id');
    }
}
