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

        foreach ($pics as $pic) {

            $pic->pph21 = $this->hitungPph21(0, $pic->total);
        }

        return view('finance.pajak.migas.index', compact('ppjbs', 'pics'));
    }

    private function hitungPph21($akumulasi, $fee)
    {

        $layers = [
            [0, 60000000, 0.05],
            [60000000, 250000000, 0.15],
            [250000000, 500000000, 0.25],
            [500000000, PHP_INT_MAX, 0.35]
        ];

        $pajak = 0;
        $sisa = $fee;
        $current = $akumulasi;

        foreach ($layers as $layer) {

            $max = $layer[1];
            $rate = $layer[2];

            if ($current >= $max) {
                continue;
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
