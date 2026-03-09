<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent')->orderBy('order_no')->get();

        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->pluck('name', 'id');

        return view('menus.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'menu_key' => 'required|unique:menus,menu_key'
        ]);

        Menu::create($request->all());

        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil dibuat');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);

        $parents = Menu::whereNull('parent_id')
            ->pluck('name', 'id');

        return view('menus.edit', compact('menu', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'menu_key' => 'required|unique:menus,menu_key,' . $id,
        ]);

        $menu->update($request->all());

        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil diupdate');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->children()->count() > 0) {
            return back()->with('error', 'Menu memiliki submenu');
        }

        $menu->delete();

        return redirect()->route('menus.index');
    }
}
