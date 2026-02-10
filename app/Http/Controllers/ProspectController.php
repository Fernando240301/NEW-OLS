<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Prospect;
use App\Models\JenisPeralatan;
use App\Models\User;
use App\Models\SysUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProspectController extends Controller
{
    public function index()
    {
        $data = Prospect::with(['jenis', 'client','SysUser'])->get();
        return view('prospect.index', compact('data'));
    }

    public function create()
    {
        $jenis = JenisPeralatan::all();
        return view('prospect.create', compact('jenis'));
    }

  public function store(Request $request)
{
    $request->validate([
        'judul' => 'required|string|max:255',
        'klient' => 'required|string|max:255',
        'id_peralatan' => 'required|exists:jenis_peralatan,id',
        'catatan' => 'required|string',
        'status' => 'required|string',
        'sales' => 'required|string',
    ]);

    $userId = Auth::id();  // Ambil ID login
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }

    Prospect::create([
        'judul' => $request->judul,
        'klient' => $request->klient,
        'alat' => $request->id_peralatan,
        'catatan' => $request->catatan,
        'status' => $request->status,
        'sales' => $request->sales,
        'createuser' => $userId,  // otomatis dari login
        'createdate' => now(),
    ]);

    return redirect()->route('prospect.index')->with('success', 'Data berhasil disimpan!');
}
 public function edit($id)
{
    $item = Prospect::findOrFail($id); // ⬅️ SATU DATA
    $jenis = JenisPeralatan::all();
    return view('prospect.edit', compact('jenis', 'item'));
}
public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
        'judul' => 'required|string|max:255',
        'klient' => 'required|string|max:255',
        'id_peralatan' => 'required|exists:jenis_peralatan,id',
        'catatan' => 'required|string',
        'status' => 'required|string',
        'sales' => 'required|string',
    ]);
    // Cari data
    $item = Prospect::findOrFail($id);

    $userId = Auth::id();  // Ambil ID login
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }
    // Update
    $item->update([
    'judul' => $request->judul,
        'klient' => $request->klient,
        'alat' => $request->id_peralatan,
        'catatan' => $request->catatan,
        'status' => $request->status,
        'sales' => $request->sales,
        'createuser' => $userId,  // otomatis dari login
        'createdate' => now(),
    ]);

    return redirect()->route('prospect.index')->with('success', 'Data berhasil diperbaharui!');
}
public function delete($id)
    {
        $deleted = DB::table('prospect')
            ->where('id', $id)
            ->delete();

        if (!$deleted) {
            return redirect()
                ->route('prospect.index')
                ->with('error', 'Data client tidak ditemukan');
        }

        return redirect()
            ->route('prospect.index')
            ->with('success', 'Data client berhasil dihapus');
    }

}
