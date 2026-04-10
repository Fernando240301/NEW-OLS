<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class AdminLteUserFilter implements FilterInterface
{
    public function transform($item)
    {
        // kalau tidak ada rule users → tampilkan
        if (!isset($item['users'])) {
            return $item;
        }

        // kalau belum login → sembunyikan
        if (!Auth::check()) {
            return false;
        }

        $username = strtolower(Auth::user()->username);

            if (is_array($item['users'])) {
                $allowed = array_map('strtolower', $item['users']);

                if (!in_array($username, $allowed)) {
                    return false;
                }
            } else {
            if ($username != $item['users']) {
                return false;
            }
        }

        return $item;
    }
}