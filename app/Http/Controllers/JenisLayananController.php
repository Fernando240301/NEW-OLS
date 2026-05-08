<?php

namespace App\Http\Controllers;

use App\Models\RefJnsLayanan;
use Illuminate\Http\Request;

class JenisLayananController extends Controller
{
    public function index()
    {
        $data = RefJnsLayanan::all();
        return view('jenis_layanan.index', compact('data'));
    }
    public function create()
    {
        return view('jenis_layanan.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255'
        ]);

        RefJnsLayanan::create([
            'nama_layanan' => $request->nama_layanan,
            'alias' => $request->alias
        ]);

        return redirect()->route('jenis_layanan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = RefJnsLayanan::findOrFail($id);
        return view('jenis_layanan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'nama_layanan' => 'required|string|max:255'
        ]);

        // Cari data
        $item = RefJnsLayanan::findOrFail($id);

        // Update
        $item->update([
            'nama_layanan' => $request->nama_layanan,
            'alias' => $request->alias,
        ]);

        return redirect()->route('jenis_layanan.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $item = RefJnsLayanan::findOrFail($id);
        $item->delete();

        return redirect()->route('jenis_layanan.index')->with('success', 'Data berhasil dihapus');
    }
}
