<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DaftarPO;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
class DaftarPOController extends Controller
{
    // Method index sudah ada
    public function index()
    {
        $data = DaftarPO::all();
        return view('daftarpo.index', compact('data'));
    }

    // Tambahkan method create
    public function create()
    {
        return view('daftarpo.create');
    }

    // Tambahkan method store untuk menyimpan data
   public function store(Request $request)
{
    $request->validate([
        'pr_number'    => 'required|string|unique:daftar_po,pr_number',
        'nama_pengaju' => 'required|string|max:100',
        'to'           => 'required|string|max:100',
        'address'      => 'required|string|max:255',
        'date'         => 'required|date',
        'ship_to'      => 'required|string|max:100',
        'ship_date'    => 'required|date',
        'description'  => 'required|string',
        'qty'          => 'required|numeric',
        'unit'         => 'required|string|max:50',
        'unit_price'   => 'required|numeric',
        'dokumen_penawaran' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
    ]);

    $data = $request->all();

    // ✅ AUTO GENERATE NO PO
    $data['no_po'] = $this->generateNoPO();

    if ($request->hasFile('dokumen_penawaran')) {
        $file = $request->file('dokumen_penawaran');
        $filename = time().'_'.$file->getClientOriginalName();

        $file->storeAs('dokumen_po', $filename, 'public');

        $data['dokumen_penawaran'] = $filename;
    }

    DaftarPO::create($data);

    return redirect()->route('daftarpo.index')->with('success', 'PO berhasil ditambahkan!');
}

    public function edit($id)
{
    $po = DaftarPO::findOrFail($id);
    return view('daftarpo.edit', compact('po'));
}

public function update(Request $request, $id)
{
    $po = DaftarPO::findOrFail($id);
    $po->update($request->all());
    return redirect()->route('daftarpo.index')->with('success', 'PO berhasil diperbarui!');
}

public function destroy($id)
{
    $po = DaftarPO::findOrFail($id);
    $po->delete();
    return redirect()->route('daftarpo.index')->with('success', 'PO berhasil dihapus!');
}
public function uploadDocument(Request $request, $id)
{
    $po = DaftarPO::findOrFail($id);

    $request->validate([
        'dokumen_penawaran' => 'required|file|mimes:pdf,doc,docx|max:5120',
    ]);

    if ($request->hasFile('dokumen_penawaran')) {

        $file = $request->file('dokumen_penawaran');
        $filename = time().'_'.$file->getClientOriginalName();

        // ✅ SIMPAN FILE (INI YANG PENTING)
        $file->storeAs('dokumen_po', $filename, 'public');

        // simpan ke database
        $po->dokumen_penawaran = $filename;
        $po->save();
    }

    return redirect()->route('daftarpo.index')->with('success', 'Dokumen berhasil diupload!');
}
function generateNoPO()
{
    $today = Carbon::now();

    $tahun = $today->format('y');
    $bulan = $today->format('m');

    $last = DaftarPO::whereYear('created_at', $today->year)
        ->whereMonth('created_at', $today->month)
        ->orderBy('id', 'desc')
        ->first();

    if ($last) {
        $lastNumber = (int) substr($last->no_po, -6);
        $urut = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $urut = '000001';
    }

    return "SPB{$tahun}{$bulan}{$urut}";
}
public function preview($id)
{
    $po = DaftarPO::findOrFail($id);
    return view('daftarpo.preview', compact('po'));
}
public function approval($id, $role, $action)
{
    $po = DaftarPO::findOrFail($id);
    $user = auth()->user();

    // mapping rolesid
    $roleMap = [
        'marketing' => 10,
        'finance' => 7,
        'direktur' => 12,
    ];

    if (!isset($roleMap[$role]) || $user->rolesid != $roleMap[$role]) {
        return back()->with('error', 'Tidak punya akses!');
    }

    if ($role == 'marketing') {
        $po->marketing_manager = $user->fullname;
        $po->marketing_status = $action;
    } elseif ($role == 'finance') {
        $po->finance_manager = $user->fullname;
        $po->finance_status = $action;
    } elseif ($role == 'direktur') {
        $po->direktur_utama = $user->fullname;
        $po->direktur_status = $action;
    }

    $po->save();

    return back()->with('success', ucfirst($action).' berhasil!');
}
public function generatePDF($id)
{
    $po = DaftarPO::findOrFail($id);

    $pdf = Pdf::loadView('daftarpo.pdf', compact('po'))
              ->setPaper('A4', 'portrait');

    return $pdf->stream('PO_'.$po->no_po.'.pdf');
}
}
