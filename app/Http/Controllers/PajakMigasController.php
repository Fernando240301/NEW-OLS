<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ppjbnew;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\AccountingPeriod;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class PajakMigasController extends Controller
{

    public function index()
    {

        $ppjbs = Ppjbnew::where('jenis_pengajuan', 'project_migas')
            ->where('status', 'approved')
            ->where('tax_processed', false)
            ->latest()
            ->get();

        return view('finance.pajak.migas.index', compact('ppjbs'));
    }


    public function process(Request $request)
    {

        DB::transaction(function () use ($request) {

            $ppjb = Ppjbnew::findOrFail($request->ppjb_id);

            $period = AccountingPeriod::where('status', 'open')->first();

            if (!$period) {
                throw new \Exception("Tidak ada periode open.");
            }

            $pph21 = (float) $request->input('pph21', 0);
            $pph23 = (float) $request->input('pph23', 0);
            $pph29 = (float) $request->input('pph29', 0);

            $totalTax = $pph21 + $pph23 + $pph29;

            if ($totalTax == 0) {
                return;
            }

            $journal = Journal::create([
                'journal_no' => 'TAX-' . now()->timestamp,
                'journal_date' => now(),
                'reference_type' => 'PAJAK MIGAS',
                'reference_id' => $ppjb->id,
                'period_id' => $period->id,
                'status' => 'posted'
            ]);

            /*
            ======================
            DEBIT BIAYA PAJAK
            ======================
            */

            $coaBiayaPajak = ChartOfAccount::where('code', '6101-001-08-02')->first();

            if ($coaBiayaPajak) {

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $coaBiayaPajak->id,
                    'debit' => $totalTax,
                    'credit' => 0,
                    'memo' => 'Biaya Pajak PPJB ' . $ppjb->no_ppjb
                ]);
            }


            /*
            ======================
            PPH21
            ======================
            */

            if ($pph21 > 0) {

                $coa = ChartOfAccount::where('code', '2104-001')->first();

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $coa->id,
                    'debit' => 0,
                    'credit' => $pph21,
                    'memo' => 'PPh21 PPJB ' . $ppjb->no_ppjb
                ]);
            }


            /*
            ======================
            PPH23
            ======================
            */

            if ($pph23 > 0) {

                $coa = ChartOfAccount::where('code', '2104-002')->first();

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $coa->id,
                    'debit' => 0,
                    'credit' => $pph23,
                    'memo' => 'PPh23 PPJB ' . $ppjb->no_ppjb
                ]);
            }


            /*
            ======================
            PPH29
            ======================
            */

            if ($pph29 > 0) {

                $coa = ChartOfAccount::where('code', '2104-005')->first();

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $coa->id,
                    'debit' => 0,
                    'credit' => $pph29,
                    'memo' => 'PPh29 PPJB ' . $ppjb->no_ppjb
                ]);
            }


            /*
            ======================
            UPDATE PPJB
            ======================
            */

            Ppjbnew::where('id', $ppjb->id)
                ->update([
                    'tax_processed' => 1
                ]);
        });

        return back()->with('success', 'Pajak berhasil diproses');
    }
}
