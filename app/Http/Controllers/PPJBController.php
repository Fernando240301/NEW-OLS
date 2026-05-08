<?php

namespace App\Http\Controllers;

use App\Models\PPJB;
use App\Models\PPJBApproval;
use App\Models\JenisPeralatan;
use app\Models\PPJBDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    ]);

    // Set approvers (hardcoded contoh)
    $approvers = [
        1 => 100201, // Manager UserID
        2 => 100305, // QA UserID
        3 => 100001, // Direktur UserID
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
        $request->validate([
            'judul' => 'required|string|max:255',
            'klient' => 'required|string|max:255',
            'id_peralatan' => 'required|exists:jenis_peralatan,id',
            'catatan' => 'required|string',
        ]);

        $item = PPJB::findOrFail($id);
        $item->update($request->all());

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
        $ppjb = PPJB::findOrFail($id);

        $pdf = Pdf::loadView('ppjb.pdf', compact('ppjb'))
            ->setPaper('A4', 'portrait');

        // tampil di browser (preview)
        return $pdf->stream('PPJB-'.$ppjb->nosurat.'.pdf');
    }
public function approve($id)
{
    $ppjb = PPJB::with('approvals')->findOrFail($id);

    Gate::authorize('approve-ppjb', $ppjb);

    $approval = $ppjb->currentApproval();

    $approval->update([
        'is_approved' => true,
        'approved_at' => now()
    ]);

    // cek next level
    $next = $ppjb->approvals()
        ->where('is_approved', false)
        ->orderBy('level')
        ->first();

    if ($next) {
        $ppjb->update([
            'approval_level' => $next->level,
            'status' => 'MENUNGGU APPROVAL LEVEL ' . $next->level
        ]);
    } else {
        $ppjb->update([
            'status' => 'APPROVED FINAL',
            'approved_at' => now(),
            'approved_by' => Auth::user()->userid
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


}


