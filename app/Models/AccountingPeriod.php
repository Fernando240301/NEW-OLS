<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    protected $fillable = [
        'year',
        'month',
        'start_date',
        'end_date',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function journals()
    {
        return $this->hasMany(Journal::class, 'period_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function getLabelAttribute()
    {
        return $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }
}
