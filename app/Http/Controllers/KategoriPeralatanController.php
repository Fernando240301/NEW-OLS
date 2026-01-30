<?php

namespace App\Http\Controllers;

use App\Models\KategoriPeralatan;
use Illuminate\Http\Request;

class KategoriPeralatanController extends Controller
{
        public function index()
    {
        $data = KategoriPeralatan::all();
        return view('kategoriperalatan', compact('data'));
    }
    public function create()
    {
        return view('tambahkategori');
    }
     public function store(Request $request)
    {
    $request->validate([
        'nama' => 'required|string|max:255',
        'alias' => 'required'
    ]);

    KategoriPeralatan::create([
        'nama' => $request->nama,
        'alias' => $request->alias
    ]);

   return redirect()->route('kategoriperalatan.index')->with('success', 'Data berhasil ditambahkan');
    }
    public function destroy($id)
    {
    $item = KategoriPeralatan::findOrFail($id);
    $item->delete();

    return redirect()->route('kategoriperalatan.index')->with('success', 'Data berhasil dihapus');
    }
    public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
        'nama' => 'required|string|max:255',
        'alias' => 'required|string|max:255',
    ]);

    // Cari data
    $item = KategoriPeralatan::findOrFail($id);

    // Update
    $item->update([
        'nama' => $request->nama,
        'alias' => $request->alias,
    ]);

    return redirect()->route('kategoriperalatan.index')
        ->with('success', 'Data berhasil diperbarui');
}
public function edit($id)
{
    $item = KategoriPeralatan::findOrFail($id);
    return view('editkategori', compact('item'));
}

}