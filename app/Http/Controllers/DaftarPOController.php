<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DaftarPO;
use App\Models\SysUser;
use App\Models\POApproval;
use App\Models\JenisPeralatan;
use App\Models\Penawaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;



class DaftarPOController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index()
{
        $data = DaftarPO::all();
        return view('daftarpo.index', compact('data'));
}

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenis = DaftarPO::all();
        return view('daftarpo.create', compact('jenis'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'namapengaju' => 'required|string|max:255',
        'harga' => 'required|numeric|min:0',
        'adress' => 'required|string|max:255',
        'project' => 'required|string|max:255',
        'to' => 'required|string|max:255',
        'date' => 'required|date',
        'attention' => 'required|string|max:255',
        'shipto' => 'required|string|max:255',
        'shipdate' => 'required|date',
        'description' => 'required|string|max:255',
        'qty' => 'required|string|max:255',
        'unit' => 'required|string|max:255',
        'file_penawaran' => 'nullable|file|mimes:pdf,doc,docx,xlsx|max:2048'
    ]);

    // Upload file dulu
    $path = null;
    if ($request->hasFile('file_penawaran')) {
        $path = $request->file('file_penawaran')
            ->store('penawaran', 'public');
    }

    $daftarpo = DaftarPO::create([
        'no_surat' => $this->generateNoSurat(),
        'namapengaju' => $request->namapengaju,
        'project' => $request->project,
        'to' => $request->to,
        'adress' => $request->adress,
        'date' => $request->date,
        'attention' => $request->attention,
        'shipto' => $request->shipto,
        'shipdate' => $request->shipdate,
        'description' => $request->description,
        'qty' => $request->qty,
        'unit' => $request->unit,
        'harga' => $request->harga,
        'file_penawaran' => $path,
        'status_daftarpo' => 'MENUNGGU APPROVAL LEVEL 1',
        'approval_level' => 1,
    ]);



    // Set approvers (hardcoded contoh)
    $approvers = [
    1 => ['user_id' => 100261, 'type' => 'Submitted'],
    3 => ['user_id' => 100393, 'type' => 'Known'],
    4 => ['user_id' => 100219, 'type' => 'Director'],
];

foreach ($approvers as $level => $data) {
    POApproval::create([
        'po_id' => $daftarpo->id,
        'user_id' => $data['user_id'],
        'level' => $level,
        'approval_type' => $data['type'], // 🔥 INI YANG PENTING
        'is_approved' => false,
        'approved_at' => null,
    ]);
}

    return redirect()->route('daftarpo.index')
        ->with('success', 'DaftarPO berhasil disimpan dan approver sudah diatur');
}


    public function edit($id)
    {
        $item = DaftarPO::findOrFail($id);
        $jenis = JenisPeralatan::all();
        return view('daftarpo.edit', compact('item', 'jenis'));
    }

   public function update(Request $request, $id)
{
    $item = DaftarPO::findOrFail($id);

    $item->update($request->except('details'));

    // Hapus detail lama
    PODetail::where('po_id', $item->id)->delete();

    // Simpan ulang detail

    return redirect()->route('daftarpo.index')
        ->with('success', 'daftarpo berhasil diperbarui');
}


    public function destroy($id)
    {
        daftarpo::findOrFail($id)->delete();

        return redirect()->route('daftarpo.index')
            ->with('success', 'DAFTARPO berhasil dihapus');
    }
    public function generateNoSurat()
    {
    $now = Carbon::now();
    $prefix = $now->format('ym'); // contoh: 2602

    $last = DaftarPO::where('no_surat', 'like', $prefix . '%')
        ->orderBy('no_surat', 'desc')
        ->first();

    $lastNumber = 0;

    if ($last) {
        // ambil 4 digit terakhir sebelum .MT
        $lastNumber = (int) substr($last->no_surat, 4, 4);
    }

    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

    return $prefix . $newNumber . '.MT';
    }
    
public function preview($id)
{
    $daftarpo = DaftarPO::with([
        'details',
        'approvals.user'
    ])->findOrFail($id);

    // Logo
    $logoPath = public_path('images/logo.png');
    $base64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;

    // Generate signature
    foreach ($daftarpo->approvals as $approval) {
        if ($approval->is_approved) {

            $path = public_path('images/signatures/' . $approval->user->userid . '.png');

            if (file_exists($path)) {
                $approval->signature =
                    'data:image/png;base64,' .
                    base64_encode(file_get_contents($path));
            } else {
                $approval->signature = null;
            }
        } else {
            $approval->signature = null;
        }
    }

    $pdf = Pdf::loadView('daftarpo.pdf', compact('daftarpo','base64'))
        ->setPaper('A4','portrait');

    return $pdf->download('PO-'.$daftarpo->no_surat.'.pdf');
}

public function approve($id)
{
    $daftarpo = DaftarPO::with(['details','approvals.user'])->findOrFail($id);
    $approval = $daftarpo->approvals()
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
    $next = $daftarpo->approvals()
        ->where('is_approved', false)
        ->orderBy('level')
        ->first();

    if ($next) {

        $daftarpo->update([
            'approval_level' => $next->level,
            'status_daftarpo' => 'MENUNGGU APPROVAL LEVEL ' . $next->level,
        ]);

    } else {

        $daftarpo->update([
            'status_daftarpo' => 'APPROVED FINAL',
            'approval_level' => 0
        ]);
    }

    return back()->with('success', 'PPJB berhasil di-approve');
}


public function reject(Request $request, $id)
{
    $daftarpo = DaftarPO::findOrFail($id);

    $daftarpo->update([
        'status' => 'DITOLAK',
        'approval_level' => 0
    ]);

    return back()->with('error', 'PPJB ditolak');
}
 public function verifikasiIndex()
{
    $data = DaftarPO::with(['jenis', 'client', 'SysUser','Approvals'])->get();
    return view('verifikasi.daftarpo.index', compact('data'));
}
public function uploadPenawaran(Request $request, $id)
{
    $request->validate([
        'file_penawaran' => 'required|mimes:pdf|max:20480', // 20MB
    ]);

    $po = DaftarPO::findOrFail($id);

    // Hapus file lama jika ada
    if ($po->file_penawaran && Storage::disk('public')->exists($po->file_penawaran)) {
        Storage::disk('public')->delete($po->file_penawaran);
    }

    // Upload file baru
    $path = $request->file('file_penawaran')
        ->store('penawaran', 'public');

    // Simpan ke database
    $po->file_penawaran = $path;
    $po->save();

    return redirect()->back()->with('success', 'File berhasil diupdate');
}





}