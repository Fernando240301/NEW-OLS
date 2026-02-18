<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'normal_balance'
    ];

    public function categories()
    {
        return $this->hasMany(AccountCategory::class);
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class);
    }
}
