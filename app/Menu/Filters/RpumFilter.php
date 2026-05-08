<?php

namespace App\Menu\Filters;

use Illuminate\Support\Facades\Auth;

class RpumFilter
{
    public function transform($item)
    {
        if (isset($item['key']) && $item['key'] === 'rpum') {

            $user = Auth::user();

            if (!$user || strtolower($user->username) !== 'fernando') {
                return false; // ❌ hide menu
            }
        }

        return $item;
    }
}