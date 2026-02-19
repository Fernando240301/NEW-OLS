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
        'is_postable',
        'is_system',
        'normal_balance',
        'opening_balance'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Parent (atasnya)
    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getIsHeaderAttribute()
    {
        if (!str_contains($this->code, '-')) {
            return true;
        }

        if (str_ends_with($this->code, '-000')) {
            return true;
        }

        return $this->children()->exists();
    }
}
