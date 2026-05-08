<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'menu_key',
        'menu_type',
        'icon',
        'route',
        'url',
        'parent_id',
        'order_no',
        'is_active'
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->where('is_active', 1)
            ->orderBy('order_no');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menus');
    }
}
