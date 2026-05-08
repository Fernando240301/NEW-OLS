<?php

namespace App\Http\Controllers;

use App\Models\KategoriPeralatan;
use Illuminate\Http\Request;

class KategoriPeralatanController extends Controller
{
    public function index()
    {
        $data = KategoriPeralatan::all();
        return view('kategori_peralatan.index', compact('data'));
    }
    public function create()
    {
        return view('kategori_peralatan.create');
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

        return redirect()->route('kategori_peralatan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = KategoriPeralatan::findOrFail($id);
        return view('kategori_peralatan.edit', compact('item'));
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

        return redirect()->route('kategori_peralatan.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $item = KategoriPeralatan::findOrFail($id);
        $item->delete();

        return redirect()->route('kategori_peralatan.index')->with('success', 'Data berhasil dihapus');
    }
}
