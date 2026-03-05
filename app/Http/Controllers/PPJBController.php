<?php

namespace App\Http\Controllers;

use App\Models\PPJB;
use App\Models\PPJBApproval;
use App\Models\JenisPeralatan;
use App\Models\PPJBDetail;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

use Carbon\Carbon;


class PPJBController extends Controller
{
    public function index()
    {
        $data = PPJB::with(['jenis', 'client', 'SysUser','Approvals'])->get();
        return view('ppjb.index', compact('data'));
    }

    public function create()
    {
        $jenis = JenisPeralatan::all();
        return view('ppjb.create', compact('jenis'));
    }

    public function store(Request $request)
{
    $request->validate([
        'dari' => 'required|string|max:255',
        'tanggal_permohonan' => 'required|date',
        'tanggal_dibutuhkan' => 'required|date',
        'project' => 'required|string',
        'pekerjaan' => 'required|string',
        'PIC' => 'required|string',
        'lokasi_project' => 'required|string',
        'transport' => 'required|string',
    ]);

    // Buat PPJB baru
    $ppjb = PPJB::create([
    'nosurat' => $this->generateNoSurat(),
    'dari' => $request->dari,
    'tanggal_permohonan' => $request->tanggal_permohonan,
    'tanggal_dibutuhkan' => $request->tanggal_dibutuhkan,
    'project' => $request->project,
    'pekerjaan' => $request->pekerjaan,
    'PIC' => $request->PIC,
    'lokasi_project' => $request->lokasi_project,
    'transport' => $request->transport,

    // ✅ TAMBAHKAN INI
    'status' => 'MENUNGGU APPROVAL LEVEL ',
    'approval_level' => 1,
]);


    // Set approvers (hardcoded contoh)
    $approvers = [
        1 => 100227, // Manager UserID (pak rony)
        3 => 100261, // Direktur UserID(mas alby)
        4 => 100219, // Direktur Utama (pak nuzul)
    ];

    foreach ($approvers as $level => $userId) {
        PPJBApproval::create([
            'ppjb_id' => $ppjb->id,
            'user_id' => $userId,
            'level' => $level,
            'is_approved' => false,
            'approved_at' => null,
        ]);
    }

    return redirect()->route('ppjb.index')
        ->with('success', 'PPJB berhasil disimpan dan approver sudah diatur');
}


    public function edit($id)
    {
        $item = PPJB::findOrFail($id);
        $jenis = JenisPeralatan::all();
        return view('ppjb.edit', compact('item', 'jenis'));
    }

   public function update(Request $request, $id)
{
    $item = PPJB::findOrFail($id);

    $item->update($request->except('details'));

    // Hapus detail lama
    PPJBDetail::where('ppjb_id', $item->id)->delete();

    // Simpan ulang detail
    if ($request->details) {
        foreach ($request->details as $detail) {
            PPJBDetail::create([
                'ppjb_id' => $item->id,
                'qty' => $detail['qty'],
                'satuan' => $detail['satuan'],
                'uraian' => $detail['uraian'],
                'harga' => $detail['harga'],
                'total' => $detail['qty'] * $detail['harga'],
                'keterangan' => $detail['keterangan'],
            ]);
        }
    }

    return redirect()->route('ppjb.index')
        ->with('success', 'PPJB berhasil diperbarui');
}


    public function destroy($id)
    {
        PPJB::findOrFail($id)->delete();

        return redirect()->route('ppjb.index')
            ->with('success', 'PPJB berhasil dihapus');
    }
    public function generateNoSurat()
    {
    $now = Carbon::now();
    $prefix = $now->format('ym'); // contoh: 2602

    $last = PPJB::where('nosurat', 'like', $prefix . '%')
        ->orderBy('nosurat', 'desc')
        ->first();

    $lastNumber = 0;

    if ($last) {
        // ambil 4 digit terakhir sebelum .MT
        $lastNumber = (int) substr($last->nosurat, 4, 4);
    }

    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

    return $prefix . $newNumber . '.MT';
    }
    
    public function preview($id)
{
    $ppjb = PPJB::with([
        'details',
        'approvals.user'
    ])->findOrFail($id);

    return view('ppjb.pdf', compact('ppjb'));
}

public function approve($id)
{
    $ppjb = PPJB::with(['details','approvals.user'])->findOrFail($id);
    $approval = $ppjb->approvals()
        ->where('user_id', auth()->user()->userid)
        ->where('is_approved', false)
        ->first();

    if (!$approval) {
        return back()->with('error', 'Tidak ada approval untuk user ini');
    }

    $approval->update([
        'is_approved' => true,
        'approved_at' => now()
    ]);

    // Cek next approval
    $next = $ppjb->approvals()
        ->where('is_approved', false)
        ->orderBy('level')
        ->first();

    if ($next) {

        $ppjb->update([
            'approval_level' => $next->level,
            'status_ppjb' => 'MENUNGGU APPROVAL LEVEL ' . $next->level,
        ]);

    } else {

        $ppjb->update([
            'status_ppjb' => 'APPROVED FINAL',
            'approval_level' => 0
        ]);
    }

    return back()->with('success', 'PPJB berhasil di-approve');
}


public function reject(Request $request, $id)
{
    $ppjb = PPJB::findOrFail($id);

    $ppjb->update([
        'status' => 'DITOLAK',
        'approval_level' => 0
    ]);

    return back()->with('error', 'PPJB ditolak');
}
 public function verifikasiIndex()
{
    $data = PPJB::with(['jenis', 'client', 'SysUser','Approvals'])->get();
    return view('verifikasi.ppjb.index', compact('data'));
}



}


