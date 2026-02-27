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

class PpjbnewController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // username tanpa huruf terakhir
        $usernameShort = substr($user->username, 0, -1);

        if (strtolower($user->username) === 'fernando') {

            $ppjbs = Ppjbnew::latest()->paginate(15);
        } else {

            $ppjbs = Ppjbnew::where(function ($q) use ($user, $usernameShort) {

                $q->where('pic', 'like', '%' . $usernameShort . '%')

                    ->orWhereHas('approvals', function ($a) use ($user) {
                        $a->where('user_id', $user->userid);
                    });
            })
                ->latest()
                ->paginate(15);
        }

        return view('finance.ppjb.index', compact('ppjbs'));
    }

    public function create()
    {
        $coas = ChartOfAccount::where('is_postable', 1)
            ->whereDoesntHave('children')
            ->where(function ($q) {
                $q->where('code', 'like', '5%')
                    ->orWhere('code', 'like', '6%');
            })
            ->orderBy('code')
            ->get();

        $user = Auth::user();

        $workflows = DB::table('app_workflow as sik')
            ->leftJoin('pemohon as p', 'sik.client', '=', 'p.pemohonid')
            ->leftJoin('app_workflow as pr', 'sik.nworkflowid', '=', 'pr.workflowid')
            ->where('sik.processname', 'surat_instruksi_kerja_01')
            ->orderByDesc('sik.workflowid')
            ->select(
                'sik.workflowid',
                'sik.workflowdata',
                'sik.processcategory',
                'sik.nworkflowid',

                'p.nama_perusahaan as nama_client',

                'pr.workflowdata as pr_workflowdata',
                'pr.projectname as pr_projectname'
            )
            ->get();

        $projects = [];

        foreach ($workflows as $wf) {

            $sikData = json_decode($wf->workflowdata, true);
            $prData  = json_decode($wf->pr_workflowdata, true);

            if (!$sikData) continue;

            if (
                isset($sikData['user_inspector']) &&
                $sikData['user_inspector'] == $user->userid
            ) {

                $projectNumber = $prData['project_number'] ?? '-';

                $projects[] = [
                    'workflowid' => $wf->workflowid,
                    'no_sik'     => $sikData['no_sik'] ?? '-',
                    'client'     => $wf->nama_client ?? '-',
                    'location'   => $sikData['location_job'] ?? '-',
                    'extend'     => strtolower($wf->processcategory) === 'extend certification',

                    'no_project' => $projectNumber . ' - ' . $wf->pr_projectname,

                    // 🔥 TAMBAHKAN INI
                    'date_start' => $sikData['date_start'] ?? null,
                    'date_end'   => $sikData['date_end'] ?? null,
                ];
            }
        }

        return view('finance.ppjb.create', compact('coas', 'user', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kepada'              => 'required|string',
            'dari'                => 'required|string',
            'jenis_pengajuan'     => 'required|in:project,non_project',
            'tanggal_permohonan'  => 'required|date',
            'details'             => 'required|array|min:1',

            // wajib pilih SIK jika project
            'workflow_id' => 'required_if:jenis_pengajuan,project|nullable|exists:app_workflow,workflowid',

            // tanggal wajib ada
            'tanggal_mulai' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {

            $ppjb = Ppjbnew::create([
                'no_ppjb' => $this->generateNoPpjb(),

                // 🔥 core info
                'jenis_pengajuan' => $request->jenis_pengajuan,
                'workflow_id' => $request->jenis_pengajuan === 'project'
                    ? $request->workflow_id
                    : null,
                'pr_workflow_id' => null,

                'kepada' => $request->kepada,
                'dari' => $request->dari,

                'tanggal_permohonan' => $request->tanggal_permohonan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,

                'pekerjaan' => $request->pekerjaan,
                'pic' => $request->pic_special ?? Auth::user()->fullname,
                'kas_account_id' => $this->getCaAccountId($request->dari),
                'status' => 'draft'
            ]);

            $total = 0;

            foreach ($request->details as $row) {

                if (empty($row['coa_id']) || empty($row['qty']) || empty($row['harga'])) {
                    continue;
                }

                $subtotal = $row['qty'] * $row['harga'];
                $total += $subtotal;

                $ppjb->details()->create([
                    'coa_id' => $row['coa_id'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'] ?? null,
                    'uraian' => $row['uraian'] ?? null,
                    'harga' => $row['harga'],
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

    private function getCaAccountId($dari)
    {
        $dari = strtolower(trim($dari));

        if (str_contains($dari, 'operasional')) {
            $code = '1112-001';
        } else {
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

                if (!str_contains(strtolower($ppjb->pic), strtolower($usernameShort))) {
                    throw new \Exception("Anda bukan PIC untuk PPJB ini.");
                }

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

                /*
            =========================================
            FINAL APPROVAL → CREATE JOURNAL
            =========================================
            */

                $period = AccountingPeriod::where('status', 'open')->first();
                if (!$period) {
                    throw new \Exception("Tidak ada periode open.");
                }

                $journal = Journal::create([
                    'journal_no'     => $this->generateJournalNo(),
                    'journal_date'   => now(),
                    'reference_type' => 'PPJB',
                    'reference_id'   => $ppjb->id,
                    'period_id'      => $period->id,
                    'status'         => 'draft'
                ]);

                // Debit Cash Advance
                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $ppjb->kas_account_id,
                    'debit'      => $ppjb->total,
                    'credit'     => 0,
                    'project_id' => null,
                    'memo'       => 'Cash Advance PPJB ' . $ppjb->no_ppjb
                ]);

                // Credit Kas
                $kas = ChartOfAccount::where('code', '1101-002')->first();
                if (!$kas) {
                    throw new \Exception("COA 1101-002 tidak ditemukan.");
                }

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $kas->id,
                    'debit'      => 0,
                    'credit'     => $ppjb->total,
                    'project_id' => null,
                    'memo'       => 'Cash keluar PPJB ' . $ppjb->no_ppjb
                ]);

                $ppjb->update([
                    'status'     => 'approved',
                    'journal_id' => $journal->id
                ]);
            }
        });

        return redirect()->back()->with('success', 'Approval berhasil diproses.');
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
        if ($ppjb->workflow_id) {

            $workflow = DB::table('app_workflow')
                ->where('workflowid', $ppjb->workflow_id)
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
            ->where(function ($q) {
                $q->where('code', 'like', '5%')
                    ->orWhere('code', 'like', '6%');
            })
            ->orderBy('code')
            ->get();

        /*
    =====================================
    🔥 TAMBAHKAN PROJECT LIST (SAMA SEPERTI CREATE)
    =====================================
    */

        $user = Auth::user();

        $workflows = DB::table('app_workflow as sik')
            ->leftJoin('pemohon as p', 'sik.client', '=', 'p.pemohonid')
            ->leftJoin('app_workflow as pr', 'sik.nworkflowid', '=', 'pr.workflowid')
            ->where('sik.processname', 'surat_instruksi_kerja_01')
            ->orderByDesc('sik.workflowid')
            ->select(
                'sik.workflowid',
                'sik.workflowdata',
                'sik.processcategory',
                'sik.nworkflowid',
                'p.nama_perusahaan as nama_client',
                'pr.workflowdata as pr_workflowdata',
                'pr.projectname as pr_projectname'
            )
            ->get();

        $projects = [];

        foreach ($workflows as $wf) {

            $sikData = json_decode($wf->workflowdata, true);
            $prData  = json_decode($wf->pr_workflowdata, true);

            if (!$sikData) continue;

            if (
                isset($sikData['user_inspector']) &&
                $sikData['user_inspector'] == $user->userid
            ) {

                $projectNumber = $prData['project_number'] ?? '-';

                $projects[] = [
                    'workflowid' => $wf->workflowid,
                    'no_sik'     => $sikData['no_sik'] ?? '-',
                    'client'     => $wf->nama_client ?? '-',
                    'location'   => $sikData['location_job'] ?? '-',
                    'extend'     => strtolower($wf->processcategory) === 'extend certification',
                    'no_project' => $projectNumber . ' - ' . $wf->pr_projectname,
                    'date_start' => $sikData['date_start'] ?? null,
                    'date_end'   => $sikData['date_end'] ?? null,
                ];
            }
        }

        return view('finance.ppjb.edit', compact('ppjb', 'coas', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kepada'              => 'required|string',
            'dari'                => 'required|string',
            'jenis_pengajuan'     => 'required|in:project,non_project',
            'tanggal_permohonan'  => 'required|date',
            'details'             => 'required|array|min:1',
            'workflow_id' => 'required_if:jenis_pengajuan,project|nullable|exists:app_workflow,workflowid',
        ]);

        DB::transaction(function () use ($request, $id) {

            $ppjb = Ppjbnew::findOrFail($id);

            if ($ppjb->status != 'draft') {
                throw new \Exception("PPJB sudah approved dan tidak bisa diedit.");
            }

            // 🔥 Update header
            $ppjb->update([
                'jenis_pengajuan' => $request->jenis_pengajuan,

                'workflow_id' => $request->jenis_pengajuan === 'project'
                    ? $request->workflow_id
                    : null,

                'kepada' => $request->kepada,
                'dari' => $request->dari,

                'tanggal_permohonan' => $request->tanggal_permohonan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,

                'pekerjaan' => $request->pekerjaan,
                'pic' => $request->pic,
                'kas_account_id' => $this->getCaAccountId($request->dari),
            ]);

            // 🔥 Hapus detail lama
            $ppjb->details()->delete();

            $total = 0;

            foreach ($request->details as $row) {

                // skip baris kosong
                if (empty($row['coa_id']) || empty($row['qty']) || empty($row['harga'])) {
                    continue;
                }

                $subtotal = $row['qty'] * $row['harga'];
                $total += $subtotal;

                $ppjb->details()->create([
                    'coa_id' => $row['coa_id'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'] ?? null,
                    'uraian' => $row['uraian'] ?? null,
                    'harga' => $row['harga'],
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

            $period = AccountingPeriod::where('status', 'open')->first();

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

        $tanggalDibutuhkan = Carbon::parse($ppjb->tanggal_dibutuhkan)
            ->translatedFormat('d F Y');

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

        if (str_contains($dari, 'marketing')) {
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

            if (
                $ppjb->status === 'draft' &&
                str_contains(strtolower($ppjb->pic), strtolower($usernameShort))
            ) {
                $allowed = true;
            }

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

            if (
                $lpjb->status === 'draft' &&
                str_contains(strtolower($lpjb->ppjb->pic), strtolower($usernameShort))
            ) {
                $allowed = true;
            }

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
