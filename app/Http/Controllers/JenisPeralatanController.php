<?php

namespace App\Http\Controllers;

use App\Models\JenisPeralatan;
use Illuminate\Http\Request;

class JenisPeralatanController extends Controller
{
    public function index()
    {
        $data = JenisPeralatan::all();
        return view('jenis_peralatan.index', compact('data'));
    }
    public function create()
    {
        return view('jenis_peralatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        JenisPeralatan::create([
            'nama' => $request->nama
        ]);

        return redirect()->route('jenis_peralatan.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = JenisPeralatan::findOrFail($id);
        return view('jenis_peralatan.edit', compact('item'));
    }
    public function update(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        // Cari data
        $item = JenisPeralatan::findOrFail($id);

        // Update
        $item->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('jenis_peralatan.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function delete($id)
    {
        $item = JenisPeralatan::findOrFail($id);
        $item->delete();

        return redirect()->route('jenis_peralatan.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
