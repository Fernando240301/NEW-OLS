<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Prospect;
use App\Models\JenisPeralatan;
use App\Models\User;
use App\Models\SysUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        'judul'         => 'required|string|max:255',
        'klient'        => 'required|string|max:255',
        'id_peralatan'  => 'required|exists:ref_jenis_peralatan,id',
        'catatan'       => 'nullable|string',
        'status'        => 'required|string|max:50',
        'tanggal'       => 'required|date',
        'sales'         => 'required|string|max:100',
        'files.*'       => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ]);

    DB::beginTransaction();

    try {
        $filesPath = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filesPath[] = $file->store('prospect', 'public');
            }
        }

        Prospect::create([
            'judul'   => $request->judul,
            'klient'  => $request->klient,
            'alat'    => $request->id_peralatan,
            'catatan' => $request->catatan,
            'status'  => $request->status,
            'tanggal' => $request->tanggal,
            'sales'   => $request->sales,
            'createdate' => now(),
            'createuser' => Auth::id(),
            'file'    => $filesPath ?: null,
        ]);

        DB::commit();

        return redirect()->route('prospect.index')
            ->with('success', 'Data prospect berhasil disimpan');

    } catch (\Throwable $e) {
        DB::rollBack();

        foreach ($filesPath as $file) {
            Storage::disk('public')->delete($file);
        }

        // DEBUG sementara (kalau masih error)
        // dd($e->getMessage());

        return back()->withInput()
            ->with('error', 'Gagal menyimpan data');
    }
}


 public function edit($id)
{
    $item = Prospect::findOrFail($id); // ⬅️ SATU DATA
    $jenis = JenisPeralatan::all();
    return view('prospect.edit', compact('jenis', 'item'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'judul'        => 'required|string|max:255',
        'klient'       => 'required|string|max:255',
        'id_peralatan' => 'required|exists:ref_jenis_peralatan,id',
        'catatan'      => 'required|string',
        'status'       => 'required|string',
        'tanggal'      => 'required|date',
        'sales'        => 'required|string',

        // 🔥 VALIDASI FILE YANG BENAR
        'file'   => 'nullable|array',
        'file.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
    ]);

    $item = Prospect::findOrFail($id);

    // ambil file lama
    $filesPath = $item->file ?? [];

    // kalau upload file baru
    if ($request->hasFile('file')) {

        // hapus file lama (opsional, tapi recommended)
        foreach ($filesPath as $oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

        $filesPath = [];
        foreach ($request->file('file') as $file) {
            $filesPath[] = $file->store('prospect', 'public');
        }
    }

    $item->update([
        'judul'   => $request->judul,
        'klient'  => $request->klient,
        'alat'    => $request->id_peralatan,
        'catatan' => $request->catatan,
        'status'  => $request->status,
        'tanggal' => $request->tanggal,
        'sales'   => $request->sales,
        'file'    => $filesPath, // 🔥 INI PENTING
    ]);

    return redirect()
        ->route('prospect.index')
        ->with('success', 'Data berhasil diperbaharui!');
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
