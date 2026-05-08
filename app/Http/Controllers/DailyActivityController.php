<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyActivity;
use App\Models\SysUserDetail;
use App\Models\Project;
use Carbon\Carbon;

class DailyActivityController extends Controller
{
public function index()
{
    $user = auth()->user();

    $activities = $user->dailyActivities()
        ->orderBy('activity_date', 'desc')
        ->get();

    $data = Project::where('tipe', 'pr')
            ->orderBy('workflowid', 'desc')
            ->get();

    // ✅ TAMBAHAN: KEGIATAN BERDASARKAN ROLE
    $kegiatan = [];

    if ($user->rolesid == 10) { // contoh: IT
        $kegiatan = [
            'MM1' => 'Monitor rekanan seiap bulan',
            'MM2' => 'Audit Eksternal',
            'MM3' => 'Audit Internal',
            'MM5' => 'Meeting Eksternal(client)',
            'MM6' => 'Meeting Internal',
            'MM7' => 'Menghubungi client repeat order',
            'MM8' => 'Susun marketing plan Per triwulan',
            'MM9' => 'Susun marketing plan Per tahun',
            'MM10' => 'Simpan seluruh file diserver',
            'MM11' => 'Meriview kontrak dan performance Bon',
            'MM12' => 'Rapat Negosiasi',
            'MM13' => 'Rapat klarifikasi dokumen',
            'MM14' => 'Rapat buka sampul (open bid)',
            'MM15' => 'Rapat klarifikasi dokumen',
            'MM16' => 'Evaluasi & final check dokumen tender',
            'MM17' => 'Mengevaluasi dokumen CMS',
            'MM18' => 'susun procedure MIT dan Sub contractor',
            'MM19' => 'Urus semua surat dukungan/konsorsium',
            'MM20' => 'Menghubungi partner / konsors / subcon',
            'MM21' => 'Membuat metodelogi & Schedule',
            'MM22' => 'Penyusunan dokumen administrasi',
            'MM23' => 'Evaluasi Harga akhir',
            'MM24' => 'Minta Approval Direktur / SE',
            'MM25' => 'Membuat rincian biaya di form lelang',
            'MM26' => 'Membuat BBM dan R Harga Penawaran',
            'MM27' => 'Susun solusi penting hasil prebid',
            'MM28' => 'Mengikuti pre bid meeting',
            'MM29' => 'Meminta approval dokumen kerjasama',
            'MM30' => 'Menghubungi relasi/client',        
            'MM31' => 'Membuat question ke Client/Anwizing',
            'MM32' => 'Share dokumen lelang yang berat ke SE',
            'MM33' => 'Analisa dokumen Lelang',
            'MM34' => 'Menyusun dokumen PQ',
            'MM35' => 'Merespon RFQ dan daftar lelang',
            'MM36' => 'Mengevaluasi informasi lelang / rfq / pq',
            'MM37' => 'Buka CVID Client, SKK & UPMS',
            'MM38' => 'Buka tender.com',
            'MM39' => 'Buka Record & note atau WA market',
            'MM40' => 'Buka email masuk',
            'MM41' => 'Melakukan dan mengevaluasi survey kepuasan pelanggan',
            'MM42' => 'Cuti Tahunan',
            'MM43' => 'Izin / Sakit',
            'MM' => 'Aktifitas marketing sehari-hari',
        ];
    }elseif ($user->rolesid == 23) { // contoh: IT
        $kegiatan = [
            'MM1' => 'Monitor rekanan seiap bulan',
            'MM2' => 'Audit Eksternal',
            'MM3' => 'Audit Internal',
            'MM5' => 'Meeting Eksternal(client)',
            'MM6' => 'Meeting Internal',
            'MM7' => 'Menghubungi client repeat order',
            'MM8' => 'Susun marketing plan Per triwulan',
            'MM9' => 'Susun marketing plan Per tahun',
            'MM10' => 'Simpan seluruh file diserver',
            'MM11' => 'Meriview kontrak dan performance Bon',
            'MM12' => 'Rapat Negosiasi',
            'MM13' => 'Rapat klarifikasi dokumen',
            'MM14' => 'Rapat buka sampul (open bid)',
            'MM15' => 'Rapat klarifikasi dokumen',
            'MM16' => 'Evaluasi & final check dokumen tender',
            'MM17' => 'Mengevaluasi dokumen CMS',
            'MM18' => 'susun procedure MIT dan Sub contractor',
            'MM19' => 'Urus semua surat dukungan/konsorsium',
            'MM20' => 'Menghubungi partner / konsors / subcon',
            'MM21' => 'Membuat metodelogi & Schedule',
            'MM22' => 'Penyusunan dokumen administrasi',
            'MM23' => 'Evaluasi Harga akhir',
            'MM24' => 'Minta Approval Direktur / SE',
            'MM25' => 'Membuat rincian biaya di form lelang',
            'MM26' => 'Membuat BBM dan R Harga Penawaran',
            'MM27' => 'Susun solusi penting hasil prebid',
            'MM28' => 'Mengikuti pre bid meeting',
            'MM29' => 'Meminta approval dokumen kerjasama',
            'MM30' => 'Menghubungi relasi/client',        
            'MM31' => 'Membuat question ke Client/Anwizing',
            'MM32' => 'Share dokumen lelang yang berat ke SE',
            'MM33' => 'Analisa dokumen Lelang',
            'MM34' => 'Menyusun dokumen PQ',
            'MM35' => 'Merespon RFQ dan daftar lelang',
            'MM36' => 'Mengevaluasi informasi lelang / rfq / pq',
            'MM37' => 'Buka CVID Client, SKK & UPMS',
            'MM38' => 'Buka tender.com',
            'MM39' => 'Buka Record & note atau WA market',
            'MM40' => 'Buka email masuk',
            'MM41' => 'Melakukan dan mengevaluasi survey kepuasan pelanggan',
            'MM42' => 'Cuti Tahunan',
            'MM43' => 'Izin / Sakit',
            'MM' => 'Aktifitas marketing sehari-hari',
        ];
     }elseif ($user->rolesid == 45) { // manager
        $kegiatan = [
            'MGR1' => 'Menyelesaikan masalah file yang tidak bisa dibuka karena ukuran file; Karena kesalahan nama sehingga tidak bisa dibuka',
            'MGR2' => 'Melakukan Backup data lama ke server',
            'MGR3' => 'Memindahkan file ke unit yang benar',
            'MGR4' => 'Memindahkan File ke PR yang benar',
            'MGR5' => 'Menghapus file dropbox yang sudah dipindahkan ke Unit',
            'MGR6' => 'Mengisi Data Existing ke input data tiap unit',
            'MGR7' => 'Menginput Data yang sudah diupload inspektur di dropbox dan file kerja atau file unit',
            'MGR8' => 'Membuat Record WFS, WFO, WFH tiap Inspektur per tanggal 20',
            'MGR9' => 'Cek Timesheet Inspektor sesuai SIK',
            'MGR10' => 'Cek BASTP setiap unit',
            'MGR11' => 'Cek Seluruh BAP Migas tiap Unit ',
            'MGR12' => 'Pastikan tiap unit sesuai Template ',
            'MGR13' => 'Cek Daily Activity tiap Inspektur ',
            'MGR14' => 'Cek Unit File tiap Inspector sesuai SIK ',
            'MGR15' => 'Cek SIK tiap project ',
            'MGR16' => 'Cek File Kerja terkait Input Data ',
            'MGR17' => 'Cek dropbox tiap project ',
            'MGR18' => 'Solving IT Error (bila ditemukan error)',
            'MGR19' => 'Melengkapi data project / dokumen di Monitoring Inspector',
            'MGR20' => 'Cuti Tahunan',
            'MGR21' => 'Izin / Sakit',

        ];
    } else {
        $kegiatan = [
            'GEN1' => 'Aktivitas umum'
        ];
    }

    return view('dailyactivity.index', compact(
        'user',
        'activities',
        'data',
        'kegiatan' // 🔥 kirim ke blade
    ));
}
    public function create()
{
    $user = auth()->user();

    if ($user->rolesid == 10) {
        $kegiatan = [
            'ITS1' => 'Menyelesaikan masalah file tidak bisa dibuka',
            'ITS2' => 'Backup data ke server',
            'ITS3' => 'Memindahkan file ke unit',
            'ITS5' => 'Memindahkan file ke PR',
            'ITS6' => 'Menghapus file Dropbox'
        ];
    } elseif ($user->rolesid == 45) {
        $kegiatan = [
            'MGR1' => 'Approval activity',
            'MGR2' => 'Monitoring team',
            'MGR3' => 'Meeting dengan client'
        ];
    } else {
        $kegiatan = [
            'GEN1' => 'Aktivitas umum'
        ];
    }

    return view('dailyactivity.create', compact('kegiatan'));
}

 public function store(Request $request)
{
    $request->validate([
        'activity_date' => 'required|date',
        'jenis_kegiatan' => 'required',
    ]);

    // 🚫 VALIDASI MAKSIMAL 2 HARI KE BELAKANG
    $activityDate = Carbon::parse($request->activity_date);
    $today = Carbon::today();

    if ($activityDate->lt($today->copy()->subDays(2))) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak bisa input aktivitas lebih dari 2 hari ke belakang'
        ], 422);
    }

    $files = [];

    if ($request->hasFile('file_upload')) {
        foreach ($request->file('file_upload') as $file) {
            if ($file) {
                $path = $file->store('evidence', 'public');
                $files[] = $path;
            }
        }
    }

    DailyActivity::create([
        'user_id' => auth()->id(),
        'activity_date' => $request->activity_date,
        'jenis_kegiatan' => $request->jenis_kegiatan,
        'project_number' => $request->project_number,
        'uraian' => $request->uraian,
        'link' => $request->link,
        'evidence' => !empty($files) ? json_encode($files) : null,
        'status' => 'Pending'
    ]);

    return response()->json(['success' => true]);
}

    public function show($id)
    {
        $activity = DailyActivity::findOrFail($id);
        return view('dailyactivity.show', compact('activity'));
    }
    public function events()
{
    $events = auth()->user()
        ->dailyActivities()
        ->get()
        ->map(function ($item) {
            return [
                'title' => 'Activity',
                'start' => $item->activity_date,
            ];
        });

    return response()->json($events);
}

    public function edit($id)
    {
        $activity = DailyActivity::findOrFail($id);
        return view('dailyactivity.edit', compact('activity'));
    }

    public function update(Request $request, $id)
    {
        $activity = DailyActivity::findOrFail($id);
        $activity->update($request->all());

        return redirect()->route('dailyactivity.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $activity = DailyActivity::findOrFail($id);
        $activity->delete();

        return redirect()->route('dailyactivity.index')
            ->with('success', 'Data berhasil dihapus');
    }
    public function filter(Request $request)
{
    $date = $request->date;

    $data = auth()->user()
        ->dailyActivities()
        ->whereDate('activity_date', $date)
        ->orderBy('activity_date', 'asc')
        ->get();

    return response()->json($data);
}
public function approvalPage()
{
    if(auth()->user()->rolesid != 45){
        abort(403);
    }

    return view('dailyactivity.approve');
}

public function approvalData(Request $request)
{
    $query = DailyActivity::with(['user','project']);

    // ❌ HAPUS FIXED FILTER PENDING
    // $query->where('status', 'Pending');

    // ✅ FILTER STATUS DINAMIS
    if ($request->status) {
        $query->where('status', $request->status);
    }

    // DATE FILTER
    if ($request->start) {
        $query->whereDate('activity_date', '>=', $request->start);
    }

    if ($request->end) {
        $query->whereDate('activity_date', '<=', $request->end);
    }

    // SEARCH
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('uraian', 'like', '%'.$request->search.'%')
              ->orWhere('jenis_kegiatan', 'like', '%'.$request->search.'%')
              ->orWhere('project_number', 'like', '%'.$request->search.'%');
        });
    }

    $data = $query->orderBy('activity_date', 'desc')->get()->map(function($item){

        return [
            'id' => $item->id,
            'activity_date' => $item->activity_date,
            'jenis_kegiatan' => $item->jenis_kegiatan,
            'uraian' => $item->uraian,
            'status' => $item->status,
            'project_number' => $item->project->codeid ?? '-',
            'project_name'   => $item->project->projectname ?? '-',
            'username' => optional($item->user)->name ?? '-',
            'divisi' => optional($item->user)->rolesid ?? '-',

            'evidence' => $item->evidence,
            'link' => $item->link,
        ];
    });

    return response()->json($data);
}
public function reject(Request $request, $id)
{
    $data = DailyActivity::findOrFail($id);

    $data->status = 'Rejected';
    $data->rejected_by = auth()->id();
    $data->rejected_at = now();
    $data->reject_reason = $request->reason;
    $data->save();

    return response()->json(['success' => true]);
}
public function approve($id)
{
    $data = DailyActivity::findOrFail($id);

    $data->status = 'Approved';
    $data->approved_by = auth()->id();
    $data->approved_at = now();
    $data->save();

    return response()->json(['success' => true]);
}
public function search(Request $request)
{
    $search = $request->search;

    return Project::where('codeid', 'like', "%$search%")
        ->limit(50)
        ->get();
}
}