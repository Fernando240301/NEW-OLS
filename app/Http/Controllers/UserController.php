<?php

namespace App\Http\Controllers;

use App\Models\SysUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $users = SysUser::with('role')->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:sys_users',
            'fullname' => 'required',
            'password' => 'required',
            'rolesid' => 'required'
        ]);

        SysUser::create([
            'username' => $request->username,
            'fullname' => $request->fullname,
            'password' => Hash::make($request->password),
            'rolesid' => $request->rolesid,
            'telepon' => $request->telepon,
            'kantor' => $request->kantor,
            'active' => 1
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }

    public function edit($id)
    {
        $user = SysUser::findOrFail($id);

        $roles = Role::pluck('name', 'id');

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {

        $user = SysUser::findOrFail($id);

        $user->update([
            'fullname' => $request->fullname,
            'rolesid' => $request->rolesid,
            'telepon' => $request->telepon,
            'kantor' => $request->kantor,
            'active' => $request->active
        ]);

        if ($request->password) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        SysUser::destroy($id);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
