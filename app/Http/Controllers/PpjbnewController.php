<?php

namespace App\Http\Controllers;

use App\Models\Ppjbnew;
use App\Models\PpjbDetailnew;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\AccountingPeriod;
use App\Models\Lpjb;
use App\Models\PpjbnewApproval;
use App\Models\SysUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class PpjbnewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $usernameShort = substr($user->username, 0, -1);

        // =========================
        // PPJB LIST (OPTIMIZED)
        // =========================
        
        if (strtolower($user->username) === 'fernando') {
            $ppjbs = Ppjbnew::with('lpjbs')
                ->orderByDesc('id');
        } else {
            $ppjbs = Ppjbnew::with(['lpjbs', 'approvals'])
                ->where(function ($q) use ($user, $usernameShort) {
                    $q->where('created_by', $user->userid)
                        ->orWhere('pic', 'like', '%' . $usernameShort . '%')
                        ->orWhereHas('approvals', function ($a) use ($user) {
                            $a->where('user_id', $user->userid);
                        });
                })
                ->orderByDesc('id');
        }

        // =========================
        // PRELOAD WORKFLOW (NO N+1)
        // =========================
        $workflowIds = $ppjbs->pluck('workflow_id')->filter();
        $prWorkflowIds = $ppjbs->pluck('pr_workflow_id')->filter();

        $workflows = DB::table('app_workflow')
            ->whereIn('workflowid', $workflowIds->merge($prWorkflowIds))
            ->get()
            ->keyBy('workflowid');

        // =========================
        // REKAP (CACHE)
        // =========================
        $rekap = cache()->remember('rekap_ppjb', 60, function () {

            return DB::table('ppjbnews as p')
                ->leftJoin('lpjbs as l', 'p.id', '=', 'l.ppjb_id')
                ->select(
                    DB::raw('YEAR(p.tanggal_permohonan) as tahun'),
                    DB::raw('MONTH(p.tanggal_permohonan) as bulan'),

                    // =====================
                    // PPJB
                    // =====================
                    DB::raw('COUNT(DISTINCT p.id) as total_ppjb'),
                    DB::raw('SUM(p.total) as total_ppjb_nominal'),

                    // =====================
                    // LPJB
                    // =====================
                    DB::raw('COUNT(DISTINCT l.id) as total_lpjb'),
                    DB::raw('COALESCE(SUM(l.total_realisasi),0) as total_lpjb_nominal'),

                    // =====================
                    // TANPA LPJB (MIGAS)
                    // =====================
                    DB::raw("
                        COUNT(CASE 
                            WHEN p.jenis_pengajuan = 'project_migas' 
                            THEN 1 END
                        ) as total_tanpa_lpjb
                    "),

                    DB::raw("
                        SUM(CASE 
                            WHEN p.jenis_pengajuan = 'project_migas' 
                            THEN p.total ELSE 0 END
                        ) as total_tanpa_lpjb_nominal
                    "),

                    // =====================
                    // SELISIH
                    // =====================
                    DB::raw("
                        SUM(p.total) - 
                        (
                            COALESCE(SUM(l.total_realisasi),0) +
                            SUM(CASE 
                                WHEN p.jenis_pengajuan = 'project_migas' 
                                THEN p.total ELSE 0 END
                            )
                        ) as selisih
                    ")
                )
                ->groupBy('tahun', 'bulan')
                ->orderByDesc('tahun')
                ->orderByDesc('bulan')
                ->get();
        });

        return view('finance.ppjb.index', compact('ppjbs', 'rekap', 'workflows'));
    }

    public function rekapDetail(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $data = Ppjbnew::with('lpjbs')
            ->whereMonth('tanggal_permohonan', $month)
            ->whereYear('tanggal_permohonan', $year)
            ->limit(100) // 🔥 penting biar gak berat
            ->get()
            ->map(function ($p) {

                $lpjb = $p->lpjbs->first();

                // MIGAS = dianggap sama
                if ($p->jenis_pengajuan === 'project_migas') {
                    $lpjb_total = $p->total;
                } else {
                    $lpjb_total = $lpjb ? $lpjb->total_realisasi : 0;
                }

                return [
                    'no_ppjb' => $p->no_ppjb,
                    'tanggal_permohonan' => $p->tanggal_permohonan,
                    'pic' => $p->pic,
                    'total' => $p->total,
                    'status' => $p->status,
                    'jenis_pengajuan' => $p->jenis_pengajuan,

                    'lpjb_total' => $lpjb_total,
                    'selisih' => $p->total - $lpjb_total
                ];
            });

        return response()->json($data);
    }
    
    public function datatables()
    {
        $user = Auth::user();

        // 🔥 ambil fullname dari sys_users
        $fullName = SysUser::where('userid', $user->userid)->value('fullname');

        $query = Ppjbnew::with('lpjbs')
            ->orderByDesc('id');

        // =========================
        // FILTER USER
        // =========================
        $allowedUsers = ['fernando', 'ussif', 'dillaf', 'nisaf', 'fitrif', 'riflif', 'ocm', 'linahg'];

        $isAllowed = in_array(strtolower($user->username), $allowedUsers);

        if (!in_array(strtolower($user->username), $allowedUsers)) {
            $query->where('pic', $fullName);
        }

        return DataTables::of($query)

            ->addColumn('tanggal', function ($p) {
                return \Carbon\Carbon::parse($p->tanggal_permohonan)->format('d-m-Y');
            })

            ->addColumn('project_no', function ($p) {
                return $p->refer_project ?? '-';
            })

            ->addColumn('status_ppjb', function ($p) {
                if ($p->status == 'draft') return '<span class="badge bg-secondary">Draft</span>';
                if ($p->status == 'approved') return '<span class="badge bg-success">Approved</span>';
                return '<span class="badge bg-danger">' . ucfirst($p->status) . '</span>';
            })

            ->addColumn('status_lpjb', function ($p) {

                $lpjb = $p->lpjbs->first();

                if ($p->jenis_pengajuan == 'project_migas') {
                    return '<span class="badge bg-success">Done (Pajak)</span>';
                }

                if (!$lpjb) return '<span class="badge bg-light text-dark">Belum Ada</span>';

                if ($lpjb->status == 'draft') return '<span class="badge bg-secondary">Draft</span>';
                if ($lpjb->status == 'approved') return '<span class="badge bg-success">Done</span>';

                return '<span class="badge bg-warning">' . $lpjb->status . '</span>';
            })

            ->addColumn('sik', function ($p) use ($isAllowed) {

                if (!$isAllowed) return null;

                if (!$p->workflow_id) return '-';

                $workflowIds = json_decode($p->workflow_id, true);

                if (!$workflowIds || !is_array($workflowIds)) return '-';

                // 🔥 AMBIL HANYA SIK (FILTER DB)
                $workflows = DB::table('app_workflow')
                    ->whereIn('workflowid', $workflowIds)
                    ->whereIn('processname', [
                        'surat_instruksi_kerja_01',
                        'surat_instruksi_kerja_02'
                    ])
                    ->pluck('workflowid');

                if ($workflows->isEmpty()) return '-';

                // 🔥 BUAT BUTTON MULTI
                return $workflows->map(function ($id) {

                    $url = route('sik.show', $id);

                    return '<a href="'.$url.'" target="_blank"
                                class="btn btn-sm btn-warning me-1 mb-1"
                                title="Lihat SIK">
                                <i class="fas fa-eye"></i>
                            </a>';

                })->implode('');
            })

            ->addColumn('action_ppjb', function ($p) {

                $btn = '<a href="' . route('ppjb-new.pdf', $p->id) . '" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>';

                if ($p->status == 'draft') {
                    $btn .= '<a href="' . route('ppjb-new.edit', $p->id) . '" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>';
                }

                // 🔥 TAMBAHAN BUTTON APPROVE
                if ($p->status == 'draft') {
                    $btn .= '
                        <form method="POST" action="' . route('ppjb-new.approve', $p->id) . '" style="display:inline;">
                            ' . csrf_field() . '
                            <button class="btn btn-sm btn-success" 
                                onclick="return confirm(\'Approve PPJB?\')">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    ';
                }

                return $btn;
            })

            ->addColumn('action_lpjb', function ($p) {

                $lpjb = $p->lpjbs->first();

                if ($p->jenis_pengajuan == 'project_migas') {
                    return '<span class="badge bg-success">Done (Pajak)</span>';
                }

                if (!$lpjb) {

                    if ($p->status == 'approved' || $p->status == 'paid') {
                        return '<a href="' . route('lpjb.create', $p->id) . '" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-file-invoice"></i>
                                </a>';
                    }

                    return '-';
                }

                $btn = '<a href="' . route('lpjb.pdf', $lpjb->id) . '" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>';

                // 🔥 TAMBAHAN BUTTON APPROVE
                if ($lpjb->status == 'draft') {
                    $btn .= '
                        <form method="POST" action="' . route('lpjb.approve', $lpjb->id) . '" style="display:inline;">
                            ' . csrf_field() . '
                            <button class="btn btn-sm btn-success" 
                                onclick="return confirm(\'Approve LPJB?\')">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    ';
                }

                return $btn;
            })

            ->rawColumns(['status_ppjb', 'status_lpjb', 'action_ppjb', 'action_lpjb', 'sik'])
            ->make(true);
    }

    public function create()
    {
        $coas = ChartOfAccount::where('is_postable', 1)
            ->whereDoesntHave('children')
            
            ->orderBy('code')
            ->get();

        $users = SysUser::where('active', 1)
            ->orderBy('fullname')
            ->get();

        $user = Auth::user();

        // 🔥 ROLE CHECK
        $isSIK = in_array($user->rolesid, [20, 6, 18]);

        $processNames = $isSIK
            ? ['surat_instruksi_kerja_01', 'surat_instruksi_kerja_02']
            : ['project_01'];

        $workflows = DB::table('app_workflow as sik')
            ->leftJoin('pemohon as p', 'sik.client', '=', 'p.pemohonid')
            ->leftJoin('app_workflow as pr', 'sik.nworkflowid', '=', 'pr.workflowid')
            ->whereIn('sik.processname', $processNames)
            ->orderByDesc('sik.workflowid')
            ->select(
                'sik.workflowid',
                'sik.workflowdata',
                'sik.processcategory',
                'sik.processname',
                'sik.nworkflowid',
                'p.nama_perusahaan as nama_client',
                'pr.projectname as pr_projectname'
            )
            ->get();

        $migasProjects = DB::table('app_workflow as pr')
            ->leftJoin('pemohon as p', 'pr.client', '=', 'p.pemohonid')
            ->where('pr.processname', 'verifikasi_permohonan')
            ->orderByDesc('pr.workflowid')
            ->select(
                'pr.workflowid',
                'pr.workflowdata',
                'pr.projectname',
                'p.nama_perusahaan as client'
            )
            ->get();

        $migas = [];

        foreach ($migasProjects as $wf) {

            $data = json_decode($wf->workflowdata, true);

            $migas[] = [
                'workflowid' => $wf->workflowid,
                'project_number' => $data['project_number'] ?? '-',
                'project_name' => $wf->projectname ?? '-',
                'client' => $wf->client ?? '-'
            ];
        }

        $projects = [];

        foreach ($workflows as $wf) {

            $data = json_decode($wf->workflowdata, true);
            if (!$data) continue;

            // =========================
            // AMBIL PROJECT NAME (AMAN)
            // =========================
            $projectName =
                $wf->pr_projectname
                ?? ($data['nama_project'] ?? null)
                ?? '-';

            // =========================
            // 🔥 HANDLE SIK EXTEND (02)
            // =========================
            if (
                $wf->processname === 'surat_instruksi_kerja_02' &&
                !empty($data['no_sik_extend'])
            ) {

                $parent = DB::table('app_workflow')
                    ->where('workflowid', $data['no_sik_extend'])
                    ->first();

                if ($parent) {

                    $parentData = json_decode($parent->workflowdata, true);

                    if ($parentData) {

                        // 🔥 merge parent + child
                        $merged = array_merge($parentData, $data);

                        // 🔥 override khusus (tetap dari SIK 02)
                        $merged['date_start'] = $data['date_start'] ?? null;
                        $merged['date_end']   = $data['date_end'] ?? null;
                        $merged['durasi']     = $data['durasi_extend1'] ?? $parentData['durasi'] ?? null;

                        $data = $merged;
                    }
                }
            }

            /*
            =========================
            SIK
            =========================
            */
            if ($isSIK) {

                if (
                    isset($data['user_inspector']) &&
                    $data['user_inspector'] == $user->userid
                ) {

                    $noProject = '-';

                    if (!empty($data['no_sik'])) {
                        preg_match('/PR-\d+/', $data['no_sik'], $match);
                        $noProject = $match[0] ?? '-';
                    }

                    $noSik = $data['no_sik'] ?? '-';

                    if ($wf->processname === 'surat_instruksi_kerja_02') {
                        $noSik = '[ext] ' . $noSik;
                    }

                    $projects[] = [
                        'workflowid' => $wf->workflowid,
                        'no_sik'     => $noSik,

                        'client'     => $wf->nama_client ?? '-',
                        'location'   => $data['location_job'] ?? '-',
                        'extend'     => strtolower($wf->processcategory) === 'extend certification',

                        'no_project' => $noProject,

                        // 🔥 FIX UTAMA
                        'projectname' => $projectName,

                        'date_start' => $data['date_start'] ?? null,
                        'date_end'   => $data['date_end'] ?? null,
                    ];
                }

            } else {

                /*
                =========================
                NON SIK
                =========================
                */

                $projects[] = [
                    'workflowid' => $wf->workflowid,

                    'no_sik'     => $data['project_number'] ?? '-',

                    'client'     => $wf->nama_client ?? '-',
                    'location'   => '-',
                    'extend'     => false,

                    'no_project' => $data['project_number'] ?? '-',
                    'projectname'=> $projectName,

                    // 🔥 WAJIB ADA (INI FIX ERROR)
                    'date_start' => null,
                    'date_end'   => null,
                ];
            }
        }

        return view('finance.ppjb.create', compact('coas', 'user', 'projects', 'users', 'migas'));
    }

    public function store(Request $request)
    {
        // 🔥 DEBUG (boleh aktifkan kalau masih error)
        // dd($request->all());

        $request->validate([
            'kepada' => 'required|string',
            'dari' => 'required|string',
            'jenis_pengajuan' => 'required|in:project,project_migas,non_project',
            'tanggal_permohonan' => 'required|date',
            'tanggal_mulai' => 'required|date',
            'pekerjaan' => 'required|string',

            'workflow_id' => 'nullable|array',
            'workflow_id.*' => 'nullable|exists:app_workflow,workflowid',

            'details' => 'required|array|min:1',
            'details.*.coa_id' => 'required',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        $workflowIds = $request->jenis_pengajuan === 'non_project'
            ? []
            : array_filter($request->workflow_id ?? []);

        DB::transaction(function () use ($request, $workflowIds) {

            $isProject = $request->jenis_pengajuan === 'project';
            $isMigas   = $request->jenis_pengajuan === 'project_migas';

            $ppjb = Ppjbnew::create([
                'no_ppjb' => $this->generateNoPpjb(),
                'created_by' => Auth::user()->userid,

                'jenis_pengajuan' => $request->jenis_pengajuan,

                'workflow_id' => ($isProject && count($workflowIds))
                    ? json_encode($workflowIds)
                    : null,

                'pr_workflow_id' => ($isMigas && count($workflowIds))
                    ? json_encode($workflowIds)
                    : null,

                'refer_project' => ($isProject || $isMigas)
                    ? $request->project_no
                    : null,

                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,

                'kepada' => $request->kepada,
                'dari' => $request->dari,
                'tanggal_permohonan' => $request->tanggal_permohonan,

                'pekerjaan' => $request->pekerjaan,
                'pic' => $request->pic_special ?? $request->pic ?? Auth::user()->fullname,

                'kas_account_id' => $this->getCaAccountId(
                    $request->dari,
                    $request->jenis_pengajuan
                ),

                'status' => 'draft'
            ]);

            $total = 0;

            foreach ($request->details as $row) {

                if (empty($row['coa_id']) || empty($row['qty']) || empty($row['harga'])) {
                    continue;
                }

                $harga = $row['harga'];

                $coa = ChartOfAccount::find($row['coa_id']);

                if ($coa && str_contains($coa->code, '1115-002')) {
                    $harga *= -1; // simpan langsung negatif
                }

                $subtotal = $row['qty'] * $harga;
                $total += $subtotal;

                $ppjb->details()->create([
                    'coa_id' => $row['coa_id'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'] ?? null,
                    'uraian' => $row['uraian'] ?? null,
                    'harga' => $harga, // 🔥 simpan yg sudah minus
                    'keterangan' => $row['keterangan'] ?? null,
                ]);
            }

            if ($total <= 0) {
                throw new \Exception("Total PPJB tidak boleh nol.");
            }

            $ppjb->update([
                'total' => $total
            ]);
        });

        return redirect()->route('ppjb-new.index')
            ->with('success', 'PPJB berhasil dibuat.');
    }

    private function generateNoPpjb()
    {
        $month = now()->format('m');
        $year  = now()->format('y');

        $prefix = $year . $month;

        $last = \App\Models\Ppjbnew::where('no_ppjb', 'like', $prefix . '%')
            ->orderByDesc('no_ppjb')
            ->first();

        if (!$last) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($last->no_ppjb, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $next;
    }

    private function getCaAccountId($dari, $jenisPengajuan = null)
    {
        $dari = strtolower(trim($dari));

        /*
    ===============================
    PROJECT (SIK)
    ===============================
    */
        if ($jenisPengajuan === 'project') {
            $code = '1112-001'; // CA Inspektor
        }

        /*
    ===============================
    NON PROJECT
    ===============================
    */ elseif ($jenisPengajuan === 'non_project') {

            if (str_contains($dari, 'operasional')) {
                $code = '1112-002'; // CA Operasional
            } else {
                $code = '1112-003'; // CA Lain-lain
            }
        }

        /*
    ===============================
    DEFAULT
    ===============================
    */ else {
            $code = '1112-003';
        }

        $coa = ChartOfAccount::where('code', $code)->first();

        if (!$coa) {
            throw new \Exception("COA {$code} tidak ditemukan.");
        }

        return $coa->id;
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $ppjb = Ppjbnew::findOrFail($id);
            $user = Auth::user();
            $username = $user->username;
            $usernameShort = substr($username, 0, -1);

            /*
        =========================================
        STEP 1 — PIC
        =========================================
        */
            if ($ppjb->status === 'draft') {

                // if ($ppjb->created_by != $user->userid) {
                //     throw new \Exception("Anda bukan PIC untuk PPJB ini.");
                // }

                PpjbnewApproval::create([
                    'ppjb_id'     => $ppjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'PIC',
                    'step_order'  => 1,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $ppjb->update([
                    'status' => 'waiting_manager'
                ]);

                return;
            }

            /*
        =========================================
        STEP 2 — MANAGER
        =========================================
        */
            if ($ppjb->status === 'waiting_manager') {

                $managerUsername = $this->getManagerUsername($ppjb->dari);

                if ($username !== $managerUsername) {
                    throw new \Exception("Anda bukan Manager yang berwenang.");
                }

                PpjbnewApproval::create([
                    'ppjb_id'     => $ppjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'Manager',
                    'step_order'  => 2,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $ppjb->update([
                    'status' => 'waiting_finance'
                ]);

                return;
            }

            /*
        =========================================
        STEP 3 — FINANCE
        =========================================
        */
            if ($ppjb->status === 'waiting_finance') {

                if ($username !== 'Fitrif') {
                    throw new \Exception("Hanya Finance yang bisa approve.");
                }

                PpjbnewApproval::create([
                    'ppjb_id'     => $ppjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'Finance',
                    'step_order'  => 3,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $ppjb->update([
                    'status' => 'waiting_director'
                ]);

                return;
            }

            /*
        =========================================
        STEP 4 — DIRECTOR
        =========================================
        */
            if ($ppjb->status === 'waiting_director') {

                if (!in_array($username, ['Nuzuld', 'Albyc'])) {
                    throw new \Exception("Hanya Direktur yang bisa approve.");
                }

                PpjbnewApproval::create([
                    'ppjb_id'     => $ppjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'Director',
                    'step_order'  => 4,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $ppjb->update([
                    'status' => 'waiting_rpum',
                    'journal_id' => null
                ]);
            }
        });

        return redirect()->back()->with('success', 'Approval berhasil diproses.');
    }
    
    private function getOrCreatePeriod($date)
    {
        $period = AccountingPeriod::where('year', $date->year)
            ->where('month', $date->month)
            ->first();

        if (!$period) {
            $period = AccountingPeriod::create([
                'year' => $date->year,
                'month' => $date->month,
                'start_date' => $date->copy()->startOfMonth(),
                'end_date' => $date->copy()->endOfMonth(),
                'status' => 'open'
            ]);
        }

        if ($period->status === 'closed') {
            throw new \Exception("Accounting period sudah ditutup.");
        }

        return $period;
    }

    private function generateJournalNo()
    {
        $prefix = 'JR-' . now()->format('Ym') . '-';

        $last = Journal::where('journal_no', 'like', $prefix . '%')
            ->orderByDesc('journal_no')
            ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->journal_no, -4);
        $next = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $next;
    }

    public function edit($id)
    {
        $ppjb = Ppjbnew::findOrFail($id);

        if ($ppjb->status != 'draft') {
            abort(403, 'PPJB sudah approved dan tidak bisa diedit.');
        }

        /*
    =====================================
    🔥 AUTO REFRESH TANGGAL DARI SIK
    =====================================
    */
        $workflowIds = json_decode($ppjb->workflow_id ?? $ppjb->pr_workflow_id, true) ?? [];

        if (!empty($workflowIds)) {

            $workflow = DB::table('app_workflow')
                ->whereIn('workflowid', $workflowIds)
                ->first();

            if ($workflow) {

                $sikData = json_decode($workflow->workflowdata, true);

                if ($sikData) {
                    $ppjb->tanggal_mulai   = $sikData['date_start'] ?? $ppjb->tanggal_mulai;
                    $ppjb->tanggal_selesai = $sikData['date_end'] ?? $ppjb->tanggal_selesai;
                }
            }
        }

        $coas = ChartOfAccount::where('is_postable', 1)
                ->whereDoesntHave('children')
                ->orderBy('code')
                ->get();

        /*
    =====================================
    🔥 TAMBAHKAN PROJECT LIST (SAMA SEPERTI CREATE)
    =====================================
    */
    
    $users = SysUser::where('active', 1)
            ->orderBy('fullname')
            ->get();

        $user = Auth::user();

        $isSIK = in_array($user->rolesid, [20, 6, 18]);

        $processNames = $isSIK
            ? ['surat_instruksi_kerja_01', 'surat_instruksi_kerja_02']
            : ['project_01'];

        $workflows = DB::table('app_workflow as sik')
            ->leftJoin('pemohon as p', 'sik.client', '=', 'p.pemohonid')
            ->leftJoin('app_workflow as pr', 'sik.nworkflowid', '=', 'pr.workflowid')
            ->whereIn('sik.processname', $processNames)
            ->orderByDesc('sik.workflowid')
            ->select(
                'sik.workflowid',
                'sik.workflowdata',
                'sik.processcategory',
                'sik.processname',
                'sik.nworkflowid',
                'p.nama_perusahaan as nama_client',
                'pr.workflowdata as pr_workflowdata',
                'pr.projectname as pr_projectname'
            )
            ->get();

        $migasProjects = DB::table('app_workflow as pr')
            ->leftJoin('pemohon as p', 'pr.client', '=', 'p.pemohonid')
            ->where('pr.processname', 'verifikasi_permohonan')
            ->orderByDesc('pr.workflowid')
            ->select(
                'pr.workflowid',
                'pr.workflowdata',
                'pr.projectname',
                'p.nama_perusahaan as client'
            )
            ->get();

        $migas = [];

        foreach ($migasProjects as $wf) {

            $data = json_decode($wf->workflowdata, true);

            $migas[] = [
                'workflowid' => $wf->workflowid,
                'project_number' => $data['project_number'] ?? '-',
                'project_name' => $wf->projectname ?? '-',
                'client' => $wf->client ?? '-'
            ];
        }

        $projects = [];

        $isSIK = in_array($user->rolesid, [20, 6, 18]);

        foreach ($workflows as $wf) {

            $sikData = json_decode($wf->workflowdata, true);
            $prData  = json_decode($wf->pr_workflowdata, true);

            if (!$sikData) continue;

            // =========================
            // 🔥 HANDLE SIK EXTEND (02)
            // =========================
            if (
                $wf->processname === 'surat_instruksi_kerja_02' &&
                !empty($sikData['no_sik_extend'])
            ) {

                $parent = DB::table('app_workflow')
                    ->where('workflowid', $sikData['no_sik_extend'])
                    ->first();

                if ($parent) {

                    $parentData = json_decode($parent->workflowdata, true);

                    if ($parentData) {

                        $merged = array_merge($parentData, $sikData);

                        // override khusus
                        $merged['date_start'] = $sikData['date_start'] ?? null;
                        $merged['date_end']   = $sikData['date_end'] ?? null;
                        $merged['durasi']     = $sikData['durasi_extend1'] ?? $parentData['durasi'] ?? null;

                        $sikData = $merged;
                    }
                }
            }

            $projectName =
                $wf->pr_projectname
                ?? ($prData['project_name'] ?? null)
                ?? '-';

            if ($isSIK) {

                // 🔥 FILTER INSPECTOR
                if (
                    isset($sikData['user_inspector']) &&
                    $sikData['user_inspector'] == $user->userid
                ) {

                $noSik = $sikData['no_sik'] ?? '-';

                if ($wf->processname === 'surat_instruksi_kerja_02') {
                    $noSik = '[ext] ' . $noSik;
                }

                    $projects[] = [
                        'workflowid' => $wf->workflowid,
                        'no_sik'     => $noSik,
                        'client'     => $wf->nama_client ?? '-',
                        'location'   => $sikData['location_job'] ?? '-',
                        'extend'     => strtolower($wf->processcategory) === 'extend certification',

                        'no_project' => $prData['project_number'] ?? '-',
                        'projectname'=> $projectName,

                        'date_start' => $sikData['date_start'] ?? null,
                        'date_end'   => $sikData['date_end'] ?? null,
                    ];
                }

            } else {

                // 🔥 NON SIK → SEMUA MASUK
                $projects[] = [
                    'workflowid' => $wf->workflowid,
                    'no_sik'     => $prData['project_number'] ?? '-',
                    'client'     => $wf->nama_client ?? '-',
                    'location'   => '-',
                    'extend'     => false,

                    'no_project' => $prData['project_number'] ?? '-',
                    'projectname'=> $projectName,

                    'date_start' => null,
                    'date_end'   => null,
                ];
            }
        }

        return view('finance.ppjb.edit', compact('ppjb', 'coas', 'projects', 'users', 'user', 'migas'));
    }

    public function editVerifikasi($id)
    {
        $ppjb = Ppjbnew::findOrFail($id);

        $user = Auth::user();

        // 🔥 cek manager
        $managerUsername = $this->getManagerUsername($ppjb->dari);
        $isManager = $user->username === $managerUsername;

        // 🔥 data sama seperti edit()
        $coas = ChartOfAccount::where('is_postable', 1)
            ->whereDoesntHave('children')
            ->orderBy('code')
            ->get();

        $users = SysUser::where('active', 1)
            ->orderBy('fullname')
            ->get();

        // 👉 kalau mau reuse logic projects, bisa copy dari edit()

        return view('finance.ppjb.edit_verifikasi', compact(
            'ppjb', 'coas', 'users', 'isManager'
        ));
    }

    public function update(Request $request, $id)
    {
        if ($request->mode === 'verifikasi') {
            return $this->updateVerifikasi($request, $id);
        }

        $request->validate([
            'kepada' => 'required|string',
            'dari' => 'required|string',
            'jenis_pengajuan' => 'required|in:project,project_migas,non_project',
            'tanggal_permohonan' => 'required|date',
            'tanggal_mulai' => 'required|date',

            // 🔥 MULTI PROJECT
            'workflow_id' => 'nullable|array',
            'workflow_id.*' => 'exists:app_workflow,workflowid',

            'details' => 'required|array|min:1',
        ]);        

        DB::transaction(function () use ($request, $id) {

            $ppjb = Ppjbnew::findOrFail($id);

            if ($ppjb->status != 'draft') {
                throw new \Exception("PPJB sudah approved dan tidak bisa diedit.");
            }

            $workflowIds = $request->workflow_id ?? [];

            $ppjb->update([
                'jenis_pengajuan' => $request->jenis_pengajuan,

                // 🔥 JSON SIMPAN
                'workflow_id' => $request->jenis_pengajuan === 'project' && count($workflowIds)
                    ? json_encode($workflowIds)
                    : null,

                'pr_workflow_id' => $request->jenis_pengajuan === 'project_migas' && count($workflowIds)
                    ? json_encode($workflowIds)
                    : null,

                'project_no' => $request->project_no,

                'kepada' => $request->kepada,
                'dari' => $request->dari,

                'tanggal_permohonan' => $request->tanggal_permohonan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,

                'pekerjaan' => $request->pekerjaan,
                'pic' => $request->pic_special ?? $request->pic,

                'refer_project' => $request->project_no,

                'kas_account_id' => $this->getCaAccountId(
                    $request->dari,
                    $request->jenis_pengajuan
                ),
            ]);

            // 🔥 RESET DETAIL
            $ppjb->details()->delete();

            $total = 0;

            foreach ($request->details as $row) {

                if (empty($row['coa_id']) || empty($row['qty']) || empty($row['harga'])) {
                    continue;
                }

                $coa = ChartOfAccount::find($row['coa_id']);

                $harga = $row['harga'];

                // 🔥 DETEKSI PPH (COA 1115-002)
                if ($coa && str_contains($coa->code, '1115-002')) {
                    $harga = abs($harga) * -1; // pastikan minus
                }

                $subtotal = $row['qty'] * $harga;
                $total += $subtotal;

                $ppjb->details()->create([
                    'coa_id' => $row['coa_id'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'] ?? null,
                    'uraian' => $row['uraian'] ?? null,
                    'harga' => $harga, // 🔥 simpan yg sudah minus
                    'keterangan' => $row['keterangan'] ?? null,
                ]);
            }

            if ($total <= 0) {
                throw new \Exception("Total PPJB tidak boleh nol.");
            }

            $ppjb->update([
                'total' => $total
            ]);
        });

        return redirect()->route('ppjb-new.index')
            ->with('success', 'PPJB berhasil diupdate.');
    }

    public function updateVerifikasi(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $ppjb = Ppjbnew::findOrFail($id);

            // =========================
            // UPDATE DETAIL
            // =========================
            $ppjb->details()->delete();

            $total = 0;

            foreach ($request->details as $row) {

                if (empty($row['coa_id']) || empty($row['qty']) || empty($row['harga'])) {
                    continue;
                }

                $coa = ChartOfAccount::find($row['coa_id']);
                $harga = $row['harga'];

                if ($coa && str_contains($coa->code, '1115-002')) {
                    $harga = abs($harga) * -1;
                }

                $subtotal = $row['qty'] * $harga;
                $total += $subtotal;

                $ppjb->details()->create([
                    'coa_id' => $row['coa_id'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'] ?? null,
                    'uraian' => $row['uraian'] ?? null,
                    'harga' => $harga,
                    'keterangan' => $row['keterangan'] ?? null,
                ]);
            }

            if ($total <= 0) {
                throw new \Exception("Total tidak boleh nol.");
            }

            $ppjb->update(['total' => $total]);

            // 🔥 PANGGIL APPROVAL FLOW
            $this->approve($ppjb->id);

        });

        return redirect()->route('verifikasi.ppjb')
            ->with('success', 'Verifikasi berhasil.');
    }

    public function revise($id)
    {
        DB::transaction(function () use ($id) {

            $ppjb = Ppjbnew::findOrFail($id);

            if ($ppjb->status != 'approved') {
                throw new \Exception("Hanya PPJB approved yang bisa direvisi.");
            }

            if (!$ppjb->journal_id) {
                throw new \Exception("Journal tidak ditemukan.");
            }

            $journal = Journal::with('details')->findOrFail($ppjb->journal_id);

            $period = $this->getOrCreatePeriod(now());

            if (!$period) {
                throw new \Exception("Tidak ada periode open.");
            }

            // 🔥 Buat Journal Reversal
            $reversal = Journal::create([
                'journal_no'     => $this->generateJournalNo(),
                'journal_date'   => now(),
                'reference_type' => 'PPJB-REV',
                'reference_id'   => $ppjb->id,
                'period_id'      => $period->id,
                'status'         => 'draft'
            ]);

            foreach ($journal->details as $detail) {

                JournalDetail::create([
                    'journal_id' => $reversal->id,
                    'account_id' => $detail->account_id,
                    'debit'      => $detail->credit,
                    'credit'     => $detail->debit,
                    'project_id' => $detail->project_id,
                    'memo'       => 'Reversal PPJB ' . $ppjb->no_ppjb
                ]);
            }

            // 🔥 Update PPJB jadi draft lagi
            $ppjb->update([
                'status' => 'draft',
                'journal_id' => null
            ]);
        });

        return redirect()->back()
            ->with('success', 'PPJB berhasil direvisi dan journal reversal dibuat.');
    }

    public function show($id)
    {
        $ppjb = Ppjbnew::with('details.coa')
            ->findOrFail($id);

        return view('finance.ppjb.show', compact('ppjb'));
    }

    public function pdf($id)
    {
        $ppjb = Ppjbnew::with([
            'details.coa',
            'approvals.user'
        ])->findOrFail($id);

        $tanggalPermohonan = Carbon::parse($ppjb->tanggal_permohonan)
            ->translatedFormat('d F Y');

        $tanggalDibutuhkan =
            Carbon::parse($ppjb->tanggal_mulai)->translatedFormat('d F Y')
            . ' s.d ' .
            Carbon::parse($ppjb->tanggal_selesai)->translatedFormat('d F Y');

        $pdf = Pdf::loadView(
            'finance.ppjb.pdf',
            compact('ppjb', 'tanggalPermohonan', 'tanggalDibutuhkan')
        )->setPaper([0, 0, 600, 1000], 'portrait');

        return $pdf->stream('PPJB_' . $ppjb->no_ppjb . '.pdf');
    }

    private function getManagerUsername($dari)
    {
        // normalisasi
        $dari = strtolower($dari);
        $dari = str_replace(['.', ',', '-'], '', $dari);
        $dari = trim($dari);

        if (str_contains($dari, 'operasional')) {
            return 'OCM';
        }

        if (str_contains($dari, 'marketing') || str_contains($dari, 'hse')) {
            return 'Deam';
        }
        

        if (str_contains($dari, 'keuangan')) {
            return 'Fitrif';
        }

        if (str_contains($dari, 'hr') || str_contains($dari, 'ga')) {
            return 'Linahg';
        }

        if (str_contains($dari, 'it')) {
            return 'Beibys';
        }

        return null;
    }

    public function verifikasi()
    {
        $user = Auth::user();
        $username = $user->username;
        $usernameShort = substr($username, 0, -1);

        $documents = collect();

        /*
    =============================
    PPJB
    =============================
    */
        $ppjbs = Ppjbnew::whereIn('status', [
            'draft',
            'waiting_manager',
            'waiting_finance',
            'waiting_director'
        ])->get();

        foreach ($ppjbs as $ppjb) {

            $allowed = false;

            // if (
            //     $ppjb->status === 'draft' &&
            //     str_contains(strtolower($ppjb->pic), strtolower($usernameShort))
            // ) {
            //     $allowed = true;
            // }

            if (
                $ppjb->status === 'waiting_manager' &&
                Ppjbnew::getManagerUsernameByDepartment($ppjb->dari) === $username
            ) {
                $allowed = true;
            }

            if ($ppjb->status === 'waiting_finance' && $username === 'Fitrif') {
                $allowed = true;
            }

            if (
                $ppjb->status === 'waiting_director' &&
                in_array($username, ['Nuzuld', 'Albyc'])
            ) {
                $allowed = true;
            }

            if ($allowed) {
                $documents->push([
                    'id'          => $ppjb->id,
                    'type'        => 'PPJB',
                    'number'      => $ppjb->no_ppjb,
                    'ref'         => null,
                    'pic'         => $ppjb->pic,
                    'project_no'  => $ppjb->project_no ?? '-',
                    'status'      => $ppjb->status,
                    'route'       => route('ppjb-new.approve', $ppjb->id),
                    'pdf'         => route('ppjb-new.pdf', $ppjb->id),
                    'edit'        => route('ppjb-new.edit', $ppjb->id),
                ]);
            }
        }

        /*
    =============================
    LPJB
    =============================
    */
        $lpjbs = Lpjb::with('ppjb')
            ->whereIn('status', [
                'draft',
                'waiting_pcc',
                'waiting_manager',
                'waiting_finance',
                'waiting_director'
            ])->get();

        foreach ($lpjbs as $lpjb) {

            $allowed = false;

            // if (
            //     $lpjb->status === 'draft' &&
            //     str_contains(strtolower($lpjb->ppjb->pic), strtolower($usernameShort))
            // ) {
            //     $allowed = true;
            // }

            if ($lpjb->status === 'waiting_pcc' && $username === 'Ussif') {
                $allowed = true;
            }

            if (
                $lpjb->status === 'waiting_manager' &&
                Ppjbnew::getManagerUsernameByDepartment($lpjb->ppjb->dari) === $username
            ) {
                $allowed = true;
            }

            if ($lpjb->status === 'waiting_finance' && $username === 'Fitrif') {
                $allowed = true;
            }

            if (
                $lpjb->status === 'waiting_director' &&
                in_array($username, ['Nuzuld', 'Albyc'])
            ) {
                $allowed = true;
            }

            if ($allowed) {
                $documents->push([
                    'id'          => $lpjb->id,
                    'type'        => 'LPJB',
                    'number'      => $lpjb->no_lpjb,
                    'ref'         => $lpjb->ppjb->no_ppjb,
                    'pic'         => $lpjb->ppjb->pic,
                    'project_no'  => $lpjb->ppjb->project_no ?? '-',
                    'status'      => $lpjb->status,
                    'route'       => route('lpjb.approve', $lpjb->id),
                    'pdf'         => route('lpjb.pdf', $lpjb->id),
                    'edit'        => route('lpjb.edit', $lpjb->id),
                ]);
            }
        }

        return view('finance.ppjb.verifikasi', compact('documents'));
    }
}
