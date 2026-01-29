<?php

namespace App\Http\Controllers;

use App\Models\TypePeralatan;
use Illuminate\Http\Request;

class TypePeralatanController extends Controller
{
    public function index()
    {
        $data = TypePeralatan::all();
        return view('jenisperalatan', compact('data'));
    }
    public function create()
    {
        return view('tambahperalatan');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_peralatan' => 'required|string|max:255',
    ]);

    JenisPeralatan::create([
        'nama_peralatan' => $request->nama_peralatan
    ]);

   return redirect()->route('jenisperalatan')->with('success', 'Data berhasil ditambahkan');
}
public function edit($id)
{
    $item = JenisPeralatan::findOrFail($id);
    return view('editperalatan', compact('item'));
}
public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
        'nama_peralatan' => 'required|string|max:255',
    ]);

    // Cari data
    $item = JenisPeralatan::findOrFail($id);

    // Update
    $item->update([
        'nama_peralatan' => $request->nama_peralatan,
    ]);

    return redirect()->route('jenisperalatan')
        ->with('success', 'Data berhasil diperbarui');
}

public function destroy($id)
{
    $item = JenisPeralatan::findOrFail($id);
    $item->delete();

    return redirect()->route('jenisperalatan')
        ->with('success', 'Data berhasil dihapus');
}

}