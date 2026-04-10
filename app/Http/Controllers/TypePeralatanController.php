<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypePeralatan;
use App\Models\JenisPeralatan;


class TypePeralatanController extends Controller
{
    public function index()
    {
          $data = TypePeralatan::with('jenis')->get(); // relasi
          $jenis = JenisPeralatan::all(); // untuk dropdown
        return view('typeperalatan', compact('data', 'jenis'));
    }
     public function create()
    {
        $data = TypePeralatan::all(); // data untuk dropdown jika diperlukan
        $jenis = JenisPeralatan::all(); // untuk dropdown
        return view('tambahtype', compact('data', 'jenis'));
        
    }



      public function store(Request $request)
    {
        TypePeralatan::create([
            'type' => $request->type,
            'id_peralatan' => $request->id_peralatan,
        ]);
        return redirect()->back()->with('success', 'Type peralatan berhasil ditambahkan!');
    }
    
 public function edit($id)
{
    $item = TypePeralatan::findOrFail($id);
    $data = TypePeralatan::with('jenis')->get(); // semua data untuk tabel bawah
    $jenis = JenisPeralatan::all(); // dropdown
    return view('edittype', compact('item', 'data', 'jenis'));
}
public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
    'type' => 'required|string|max:255',
    'id_peralatan' => 'required|exists:jenis_peralatan,id'
    ]);    
    // Cari data
    $item = TypePeralatan::findOrFail($id);

    // Update
    $item->update([
    'type' => $request->type,
    'id_peralatan' => $request->id_peralatan
    ]);

    return redirect()->route('typeperalatan')
        ->with('success', 'Data berhasil diperbarui');
}

public function destroy($id)
{
    $item = TypePeralatan::findOrFail($id);
    $item->delete();

    return redirect()->route('typeperalatan')
    ->with('success', 'Data berhasil dihapus');

}

}
