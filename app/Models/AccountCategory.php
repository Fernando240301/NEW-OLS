<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    protected $fillable = [
        'account_type_id',
        'code',
        'name',
    ];

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function accounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'account_category_id');
    }
}
