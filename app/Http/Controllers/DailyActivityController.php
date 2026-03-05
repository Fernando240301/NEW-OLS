<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyActivity;
use Carbon\Carbon;
use App\Models\DailyActivityEvidence;

class DailyActivityController extends Controller
{
   public function index(Request $request)
{
    $tanggal = $request->tanggal ?? now()->toDateString();

    $batas = Carbon::now()->subDays(2)->startOfDay();
    $tanggalDipilih = Carbon::parse($tanggal);

    $bolehInput = $tanggalDipilih->between($batas, now());

    $data = DailyActivity::whereDate('tanggal', $tanggal)
                ->where('user_id', auth()->id())
                ->latest()
                ->paginate(10);

    return view('activity.index', compact(
        'data',
        'tanggal',
        'bolehInput'
    ));
}
public function create(Request $request)
{
    $tanggal = $request->tanggal ?? date('Y-m-d');

    return view('dailyactivity.create', compact('tanggal'));
}
public function store(Request $request)
{
    $data = $request->validate([
        'tanggal' => 'required',
        'jenis_kegiatan' => 'required',
        'project_number' => 'required',
        'uraian' => 'nullable',
        'log_activity' => 'nullable',
        'link' => 'nullable',
        'file_upload.*' => 'nullable|file|max:2048'
    ]);
    $batas = now()->subDays(2)->startOfDay();

    if (Carbon::parse($request->tanggal)->lt($batas)) {
        return back()->with('error', 'Input hanya boleh maksimal 2 hari ke belakang.');
    }

    // lanjut simpan data

    $request->validate([
        'tanggal' => [
            'required',
            'date',
            'after_or_equal:' . now()->subDays(2)->toDateString(),
            'before_or_equal:' . now()->toDateString(),
        ],
    ]);

    // simpan data

    // Minimal 1 evidence wajib
    if (!$request->log_activity && !$request->link && !$request->hasFile('file_upload')) {
        return back()->with('error', 'Minimal satu evidence harus diisi');
    }

$data['user_id'] = auth()->user()->userid;

    // 1️⃣ Simpan aktivitas dulu
    $aktivitas = DailyActivity::create($data);

    // 2️⃣ Simpan log activity jika ada
    if ($request->log_activity) {
        DailyActivityEvidence::create([
            'dailyactivity_id' => $aktivitas->id,
            'log_activity' => $request->log_activity,
        ]);
    }

    // 3️⃣ Simpan link jika ada
    if ($request->link) {
        DailyActivityEvidence::create([
            'dailyactivity_id' => $aktivitas->id,
            'link' => $request->link,
        ]);
    }

    // 4️⃣ Simpan file jika ada
    if ($request->hasFile('file_upload')) {
        foreach ($request->file('file_upload') as $file) {
            $path = $file->store('aktivitas', 'public');

            DailyActivityEvidence::create([
                'dailyactivity_id' => $aktivitas->id,
                'file_path' => $path,
            ]);
        }
    }

    return back()->with('success', 'Aktivitas berhasil ditambahkan');
}
public function events()
{
    $data = DailyActivity::where('user_id', auth()->id())->get();

    $events = [];

    foreach ($data as $item) {
        $events[] = [
            'title' => $item->jenis_kegiatan,
            'start' => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
            'color' => '#28a745',
        ];
    }

    return response()->json($events);
}
public function approve($id)
{
    $activity = DailyActivity::findOrFail($id);

    if (auth()->user()->role !== 'manager') {
        abort(403);
    }

    $activity->update(['status' => 'approved']);

    return back()->with('success', 'Aktivitas Approved');
}

public function reject($id)
{
    $activity = DailyActivity::findOrFail($id);

    if (auth()->user()->role !== 'manager') {
        abort(403);
    }

    $activity->update(['status' => 'rejected']);

    return back()->with('success', 'Aktivitas Rejected');
}
public function verifikasiIndex(Request $request)
{
    $tgl_awal  = $request->tgl_awal ?? now()->subMonth()->toDateString();
    $tgl_akhir = $request->tgl_akhir ?? now()->toDateString();
    $status    = $request->status ?? 'verifikasi';
    $perPage   = $request->per_page ?? 10;

    $query = DailyActivity::with(['user','evidences'])
        ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);

    if ($status) {
        $query->where('status', $status);
    }

    if ($request->search) {
        $query->whereHas('user', function($q) use ($request){
            $q->where('name','like','%'.$request->search.'%');
        });
    }

    $data = $query->latest()->paginate($perPage)->withQueryString();

    return view('verifikasi.dailyactivity.index', compact(
        'data',
        'tgl_awal',
        'tgl_akhir',
        'status',
        'perPage'
    ));
}
    
}
