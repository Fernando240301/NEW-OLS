<?php

namespace App\Helpers;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuHelper
{

    public static function getSidebar()
    {

        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        // menu yang boleh diakses role
        $menuIds = DB::table('role_menus')
            ->where('role_id', $user->rolesid)
            ->pluck('menu_id')
            ->toArray();

        // ambil semua menu yang diperlukan
        $menus = Menu::where(function ($q) use ($menuIds) {

            // menu yang diizinkan
            $q->whereIn('id', $menuIds)

                // parent menu dari menu yang diizinkan
                ->orWhereIn('id', function ($sub) use ($menuIds) {
                    $sub->select('parent_id')
                        ->from('menus')
                        ->whereIn('id', $menuIds);
                })

                // grand parent menu
                ->orWhereIn('id', function ($sub) {
                    $sub->select('parent_id')
                        ->from('menus')
                        ->whereIn('parent_id', function ($sub2) {
                            $sub2->select('id')->from('menus');
                        });
                });
        })
            ->with(['children.children'])
            ->orderBy('order_no')
            ->get();

        // hanya parent menu
        return $menus->whereNull('parent_id')->values();
    }


    public static function buildAdminLteMenu()
    {

        $menus = self::getSidebar();

        $result = [];

        foreach ($menus as $menu) {

            if ($menu->children->count()) {

                $submenu = [];

                foreach ($menu->children as $child) {

                    if ($child->children->count()) {

                        $subsubmenu = [];

                        foreach ($child->children as $sub) {

                            $subsubmenu[] = [
                                'text' => $sub->name,
                                'route' => $sub->route,
                                'icon' => $sub->icon ?? 'far fa-circle',
                            ];
                        }

                        $submenu[] = [
                            'text' => $child->name,
                            'submenu' => $subsubmenu
                        ];
                    } else {

                        $submenu[] = [
                            'text' => $child->name,
                            'route' => $child->route,
                            'icon' => $child->icon ?? 'far fa-circle'
                        ];
                    }
                }

                $result[] = [
                    'text' => $menu->name,
                    'icon' => $menu->icon,
                    'submenu' => $submenu
                ];
            } else {

                $result[] = [
                    'text' => $menu->name,
                    'route' => $menu->route,
                    'icon' => $menu->icon
                ];
            }
        }

        return $result;
    }
}
