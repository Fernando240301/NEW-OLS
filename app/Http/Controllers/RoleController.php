<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $menus = Menu::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('order_no')
            ->get();

        return view('roles.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        if ($request->menus) {
            $role->menus()->sync($request->menus);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dibuat');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        $menus = Menu::whereNull('parent_id')
            ->with('children.children')
            ->orderBy('order_no')
            ->get();

        $roleMenus = $role->menus->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'menus', 'roleMenus'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        if ($request->menus) {
            $role->menus()->sync($request->menus);
        } else {
            $role->menus()->detach();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diupdate');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus');
    }
}
