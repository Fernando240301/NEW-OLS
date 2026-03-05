<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ppjbnew;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Auth;
use App\Models\Lpjb;
use App\Models\LpjbDetail;
use App\Models\JournalDetail;
use App\Models\Journal;
use App\Models\AccountingPeriod;
use App\Models\LpjbApproval;
use App\Models\SysUser;
use App\Http\Controllers\PpjbnewController;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LpjbController extends Controller
{
    public function create($ppjbId)
    {
        $ppjb = Ppjbnew::with('details.coa', 'lpjb')
            ->findOrFail($ppjbId);

        if ($ppjb->status != 'approved') {
            abort(403, 'PPJB harus approved.');
        }

        if ($ppjb->lpjb) {
            abort(403, 'LPJB sudah dibuat untuk PPJB ini.');
        }

        $coas = ChartOfAccount::orderBy('code')->get();

        return view('finance.lpjb.create', compact('ppjb', 'coas'));
    }

    public function store(Request $request, $ppjbId)
    {
        DB::transaction(function () use ($request, $ppjbId) {

            $ppjb = Ppjbnew::findOrFail($ppjbId);

            // =========================
            // 1️⃣ Buat Header LPBJ
            // =========================
            $lpjb = Lpjb::create([
                'no_lpjb'        => $this->generateNoLpjb(),
                'ppjb_id'        => $ppjb->id,
                'tanggal'        => $request->tanggal,
                'total_budget'   => $ppjb->total,
                'total_realisasi' => 0,
                'selisih'        => 0,
                'status'         => 'draft'
            ]);

            $totalRealisasi = 0;
            $totalSelisih   = 0;

            // =========================
            // 2️⃣ Simpan Detail
            // =========================
            foreach ($request->details as $i => $row) {

                $realQty   = $row['real_qty'] ?? 0;
                $realHarga = $row['real_harga'] ?? 0;

                $realSubtotal = $realQty * $realHarga;

                $budgetQty   = $row['budget_qty'] ?? 0;
                $budgetHarga = $row['budget_harga'] ?? 0;

                $budgetSubtotal = $budgetQty * $budgetHarga;

                $selisih = $budgetSubtotal - $realSubtotal;

                $buktiPath = null;

                if (isset($row['bukti_file']) && $row['bukti_file']) {
                    $buktiPath = $row['bukti_file']->store('lpjb', 'public');
                }

                $lpjb->details()->create([
                    'ppjb_detail_id'  => $row['ppjb_detail_id'] ?? null, // TAMBAH
                    'coa_id'          => $row['coa_id'],
                    'uraian'          => $row['uraian'] ?? null,
                    'satuan'          => $row['satuan'] ?? null, // TAMBAH
                    'budget_qty'      => $budgetQty,
                    'budget_harga'    => $budgetHarga,
                    'budget_subtotal' => $budgetSubtotal,
                    'real_qty'        => $realQty,
                    'real_harga'      => $realHarga,
                    'real_subtotal'   => $realSubtotal,
                    'bukti_file'      => $buktiPath,
                ]);

                $totalRealisasi += $realSubtotal;
                $totalSelisih   += $selisih;
            }

            // =========================
            // 3️⃣ Update Total Header
            // =========================
            $lpjb->update([
                'total_realisasi' => $totalRealisasi,
                'selisih'         => $totalSelisih
            ]);
        });

        return redirect()
            ->route('ppjb-new.index')
            ->with('success', 'LPBJ berhasil disimpan.');
    }

    private function generateNoLpjb()
    {
        $prefix = 'LPBJ-' . now()->format('ym');

        $last = Lpjb::where('no_lpjb', 'like', $prefix . '%')
            ->orderByDesc('no_lpjb')
            ->first();

        if (!$last) {
            return $prefix . '001';
        }

        $lastNumber = (int) substr($last->no_lpjb, -3);
        $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $prefix . $next;
    }

    public function show($id)
    {
        $lpjb = Lpjb::with('details.coa', 'ppjb')
            ->findOrFail($id);

        return view('finance.lpjb.show', compact('lpjb'));
    }

    public function edit($id)
    {
        $lpjb = Lpjb::with('details.coa', 'ppjb')
            ->findOrFail($id);

        if ($lpjb->status != 'draft') {
            abort(403, 'LPBJ sudah approved dan tidak bisa diedit.');
        }

        $coas = ChartOfAccount::orderBy('code')->get();

        return view('finance.lpjb.edit', compact('lpjb', 'coas'));
    }

    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $lpjb = Lpjb::with('details')->findOrFail($id);

            if ($lpjb->status != 'draft') {
                throw new \Exception('LPBJ sudah approved.');
            }

            $lpjb->details()->delete();

            $totalRealisasi = 0;
            $totalSelisih   = 0;

            foreach ($request->details as $row) {

                $realQty   = $row['real_qty'] ?? 0;
                $realHarga = $row['real_harga'] ?? 0;

                $realSubtotal = $realQty * $realHarga;

                $budgetQty   = $row['budget_qty'] ?? 0;
                $budgetHarga = $row['budget_harga'] ?? 0;

                $budgetSubtotal = $budgetQty * $budgetHarga;

                $selisih = $budgetSubtotal - $realSubtotal;

                $lpjb->details()->create([
                    'ppjb_detail_id'  => $row['ppjb_detail_id'] ?? null,
                    'coa_id'          => $row['coa_id'],
                    'uraian'          => $row['uraian'] ?? null,
                    'satuan'          => $row['satuan'] ?? null,
                    'budget_qty'      => $budgetQty,
                    'budget_harga'    => $budgetHarga,
                    'budget_subtotal' => $budgetSubtotal,
                    'real_qty'        => $realQty,
                    'real_harga'      => $realHarga,
                    'real_subtotal'   => $realSubtotal,
                ]);

                $totalRealisasi += $realSubtotal;
                $totalSelisih   += $selisih;
            }

            $lpjb->update([
                'total_realisasi' => $totalRealisasi,
                'selisih'         => $totalSelisih
            ]);
        });

        return redirect()
            ->route('ppjb-new.index')
            ->with('success', 'LPBJ berhasil diupdate.');
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $lpjb = Lpjb::with('details', 'ppjb', 'approvals')
                ->findOrFail($id);

            $user = Auth::user();
            $username = $user->username;
            $usernameShort = substr($username, 0, -1);

            /*
        =====================
        STEP 1 — PIC
        =====================
        */
            if ($lpjb->status === 'draft') {

                if (!str_contains(strtolower($lpjb->ppjb->pic), strtolower($usernameShort))) {
                    throw new \Exception("Anda bukan PIC.");
                }

                if ($lpjb->hasApproved('PIC')) {
                    throw new \Exception("PIC sudah approve.");
                }

                LpjbApproval::create([
                    'lpjb_id'     => $lpjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'PIC',
                    'step_order'  => 1,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $lpjb->update(['status' => 'waiting_pcc']);
                return;
            }

            /*
        =====================
        STEP 2 — PCC
        =====================
        */
            if ($lpjb->status === 'waiting_pcc') {

                if ($username !== 'Ussif') {
                    throw new \Exception("Hanya PCC.");
                }

                if ($lpjb->hasApproved('PCC')) {
                    throw new \Exception("PCC sudah approve.");
                }

                LpjbApproval::create([
                    'lpjb_id'     => $lpjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'PCC',
                    'step_order'  => 2,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $lpjb->update(['status' => 'waiting_manager']);
                return;
            }

            /*
        =====================
        STEP 3 — MANAGER
        =====================
        */
            if ($lpjb->status === 'waiting_manager') {

                $managerUsername = Ppjbnew::getManagerUsernameByDepartment(
                    $lpjb->ppjb->dari
                );

                if ($username !== $managerUsername) {
                    throw new \Exception("Bukan Manager terkait.");
                }

                if ($lpjb->hasApproved('Manager')) {
                    throw new \Exception("Manager sudah approve.");
                }

                LpjbApproval::create([
                    'lpjb_id'     => $lpjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'Manager',
                    'step_order'  => 3,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $lpjb->update(['status' => 'waiting_finance']);
                return;
            }

            /*
        =====================
        STEP 4 — FINANCE
        =====================
        */
            if ($lpjb->status === 'waiting_finance') {

                if ($username !== 'Fitrif') {
                    throw new \Exception("Hanya Manager Keuangan.");
                }

                if ($lpjb->hasApproved('Finance')) {
                    throw new \Exception("Finance sudah approve.");
                }

                LpjbApproval::create([
                    'lpjb_id'     => $lpjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'Finance',
                    'step_order'  => 4,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                $lpjb->update(['status' => 'waiting_director']);
                return;
            }

            /*
        =====================
        STEP 5 — DIRECTOR
        =====================
        */
            if ($lpjb->status === 'waiting_director') {

                if (!in_array($username, ['Nuzuld', 'Albyc'])) {
                    throw new \Exception("Hanya Direktur.");
                }

                if ($lpjb->hasApproved('Director')) {
                    throw new \Exception("Direktur sudah approve.");
                }

                LpjbApproval::create([
                    'lpjb_id'     => $lpjb->id,
                    'user_id'     => $user->userid,
                    'role'        => 'Director',
                    'step_order'  => 5,
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);

                // FINAL JOURNAL
                $this->createJournalFromLpjb($lpjb);

                $lpjb->update([
                    'status' => 'approved'
                ]);

                $lpjb->ppjb->update([
                    'status' => 'closed'
                ]);
            }
        });

        return back()->with('success', 'LPJB berhasil diproses.');
    }

    public function revise($id)
    {
        $lpjb = Lpjb::findOrFail($id);

        if ($lpjb->status != 'approved') {
            throw new \Exception('LPBJ belum approved.');
        }

        $lpjb->update([
            'status' => 'draft'
        ]);

        return redirect()
            ->route('ppjb-new.index')
            ->with('success', 'LPBJ berhasil direvisi.');
    }

    public function pdf($id)
    {
        $lpjb = Lpjb::with([
            'ppjb',
            'details.coa',
            'details.ppjbDetail',
            'approvals.user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'finance.lpjb.pdf',
            compact('lpjb')
        )->setPaper('A4', 'portrait');

        return $pdf->stream($lpjb->no_lpjb . '.pdf');
    }

    private function createJournalFromLpjb($lpjb)
    {
        $period = AccountingPeriod::where('status', 'open')->first();

        if (!$period) {
            throw new \Exception('Tidak ada periode open.');
        }

        $journal = Journal::create([
            'journal_no'     => 'JR-' . now()->format('Ym') . '-' . rand(1000, 9999),
            'journal_date'   => now(),
            'reference_type' => 'LPBJ',
            'reference_id'   => $lpjb->id,
            'period_id'      => $period->id,
            'status'         => 'draft'
        ]);

        $totalRealisasi = 0;

        /*
    =========================================
    1️⃣ DEBIT SEMUA BIAYA REALISASI
    =========================================
    */
        foreach ($lpjb->details as $detail) {

            if ($detail->real_subtotal > 0) {

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $detail->coa_id,
                    'debit'      => $detail->real_subtotal,
                    'credit'     => 0,
                    'memo'       => 'Biaya LPBJ ' . $lpjb->no_lpjb
                ]);

                $totalRealisasi += $detail->real_subtotal;
            }
        }

        $advanceAmount = $lpjb->ppjb->total;
        $cashAdvanceAccount = $lpjb->ppjb->kas_account_id;

        /*
    =========================================
    2️⃣ JIKA REALISASI <= ADVANCE
    =========================================
    */
        if ($totalRealisasi <= $advanceAmount) {

            // Credit CA sebesar realisasi
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $cashAdvanceAccount,
                'debit'      => 0,
                'credit'     => $totalRealisasi,
                'memo'       => 'Pengurangan CA LPBJ' . $lpjb->no_lpjb
            ]);

            // Jika ada sisa advance
            if ($totalRealisasi < $advanceAmount) {

                $selisih = $advanceAmount - $totalRealisasi;

                $kas = ChartOfAccount::where('code', '1101-002')->first();

                if (!$kas) {
                    throw new \Exception("COA 1101-002 tidak ditemukan.");
                }

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $kas->id,
                    'debit'      => $selisih,
                    'credit'     => 0,
                    'memo'       => 'Pengembalian sisa Cash Advance' . $lpjb->no_lpjb
                ]);
            }
        }

        /*
    =========================================
    3️⃣ JIKA REALISASI > ADVANCE
    =========================================
    */ else {

            // Credit CA sebesar advance
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $cashAdvanceAccount,
                'debit'      => 0,
                'credit'     => $advanceAmount,
                'memo'       => 'Pengurangan CA LPBJ' . $lpjb->no_lpjb
            ]);

            $selisih = $totalRealisasi - $advanceAmount;

            $hutang = ChartOfAccount::where('code', '2101-998')->first();

            if (!$hutang) {
                throw new \Exception("COA 2101-998 tidak ditemukan.");
            }

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $hutang->id,
                'debit'      => 0,
                'credit'     => $selisih,
                'memo'       => 'Hutang kekurangan LPBJ' . $lpjb->no_lpjb
            ]);
        }

        /*
    =========================================
    4️⃣ SAFETY CHECK — HARUS BALANCE
    =========================================
    */
        $totalDebit  = $journal->details()->sum('debit');
        $totalCredit = $journal->details()->sum('credit');

        if ($totalDebit != $totalCredit) {
            throw new \Exception("Journal LPJB tidak balance! Debit: {$totalDebit} | Credit: {$totalCredit}");
        }

        // Simpan journal_id ke LPJB
        $lpjb->update([
            'journal_id' => $journal->id
        ]);
    }
}
