<?php

namespace App\Http\Controllers;

use App\Models\Ppjbnew;
use App\Models\PpjbDetailnew;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\AccountingPeriod;

class PpjbnewController extends Controller
{
    public function index()
    {
        $ppjbs = Ppjbnew::latest()->paginate(15);
        return view('finance.ppjb.index', compact('ppjbs'));
    }

    public function create()
    {
        $coas = \App\Models\ChartOfAccount::where('is_postable', true)
            ->orderBy('code')
            ->get();

        return view('finance.ppjb.create', compact('coas'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $ppjb = Ppjbnew::create([
                'no_ppjb' => $this->generateNoPpjb(),
                'kepada' => $request->kepada,
                'dari' => $request->dari,
                'refer_project' => $request->refer_project,
                'tanggal_permohonan' => $request->tanggal_permohonan,
                'tanggal_dibutuhkan' => $request->tanggal_dibutuhkan,
                'project_no' => $request->project_no,
                'pekerjaan' => $request->pekerjaan,
                'pic' => $request->pic,
                'kas_account_id' => $this->getCaAccountId($request->dari), // 🔥 INI YANG WAJIB
                'status' => 'draft'
            ]);

            $total = 0;

            foreach ($request->details as $row) {

                $subtotal = $row['qty'] * $row['harga'];
                $total += $subtotal;

                $ppjb->details()->create([
                    'coa_id' => $row['coa_id'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'],
                    'uraian' => $row['uraian'],
                    'harga' => $row['harga'],
                    'keterangan' => $row['keterangan'],
                ]);
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

            if ($ppjb->status != 'draft') {
                throw new \Exception("PPJB sudah diproses.");
            }

            $period = AccountingPeriod::where('status', 'open')->first();

            if (!$period) {
                throw new \Exception("Tidak ada periode open.");
            }

            // 1️⃣ Create Journal
            $journal = Journal::create([
                'journal_no'     => $this->generateJournalNo(),
                'journal_date'   => now(),
                'reference_type' => 'PPJB',
                'reference_id'   => $ppjb->id,
                'period_id'      => $period->id,
                'status'         => 'draft'
            ]);

            // 2️⃣ Debit Cash Advance
            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $ppjb->kas_account_id,
                'debit'      => $ppjb->total,
                'credit'     => 0,
                'project_id' => null,
                'memo'       => 'Cash Advance PPJB ' . $ppjb->no_ppjb
            ]);

            // 3️⃣ Credit Cash (1111-000)
            $kas = ChartOfAccount::where('code','1101-002')->first();

            if (!$kas) {
                throw new \Exception("COA 1111-002 tidak ditemukan.");
            }

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $kas->id,
                'debit'      => 0,
                'credit'     => $ppjb->total,
                'project_id' => null,
                'memo'       => 'Cash keluar PPJB ' . $ppjb->no_ppjb
            ]);

            // 4️⃣ Update PPJB
            $ppjb->update([
                'status' => 'approved',
                'journal_id' => $journal->id
            ]);
        });

        return redirect()->back()->with('success', 'PPJB berhasil di-approve & journal dibuat.');
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
}
