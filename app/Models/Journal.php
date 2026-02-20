<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'journal_no',
        'journal_date',
        'reference_type',
        'reference_id',
        'period_id',
        'status',
        'reversal_of',
        'posted_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }

    public function reversal()
    {
        return $this->belongsTo(Journal::class, 'reversal_of');
    }
}
