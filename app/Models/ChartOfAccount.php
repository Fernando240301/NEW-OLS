<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chartofaccounts';

    protected $fillable = [
        'code',
        'name',
        'account_type_id',
        'account_category_id',
        'parent_id',
        'level',
        'is_active',
        'is_postable'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function accountCategory()
    {
        return $this->belongsTo(AccountCategory::class);
    }

    // Parent (atasnya)
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    // Children (bawahnya)
    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }
}
