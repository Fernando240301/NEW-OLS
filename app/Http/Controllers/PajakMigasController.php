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
            ->where('tax_processed', 0)
            ->orderBy('tanggal_permohonan')
            ->get();

        foreach ($ppjbs as $ppjb) {

            $akumulasi = Ppjbnew::where('pic', $ppjb->pic)
                ->where('tax_processed', 1)
                ->sum('total');

            $ppjb->pph21_preview = $this->hitungPph21($akumulasi, $ppjb->total);
        }

        $pics = Ppjbnew::where('jenis_pengajuan', 'project_migas')
            ->where('status', 'approved')
            ->where('tax_processed', 0)
            ->select(
                'pic',
                DB::raw('MONTH(tanggal_permohonan) as bulan'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('pic', 'bulan')
            ->orderBy('bulan')
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

            $space = $max - $current;

            $kena = min($space, $sisa);

            $pajak += $kena * $rate;

            $sisa -= $kena;
            $current += $kena;

            if ($sisa <= 0) {
                break;
            }
        }

        return $pajak;
    }

    public function detail($pic)
    {

        $bulan = request('bulan');

        $ppjbs = Ppjbnew::where('pic', $pic)
            ->where('tax_processed', 0)
            ->whereMonth('tanggal_permohonan', $bulan)
            ->orderBy('tanggal_permohonan')
            ->get(['id', 'no_ppjb', 'total']);

        foreach ($ppjbs as $p) {
            $p->pdf_url = route('ppjb-new.pdf', $p->id);
        }

        $akumulasi = Ppjbnew::where('pic', $pic)
            ->where('tax_processed', 1)
            ->sum('total');

        $simulasi = $this->simulasiPph21($ppjbs, $akumulasi);

        $total = $ppjbs->sum('total');

        $pph21 = collect($simulasi)->sum('pajak');

        return response()->json([
            'ppjbs' => $ppjbs,
            'simulasi' => $simulasi,
            'total' => $total,
            'pph21' => $pph21
        ]);
    }

    public function processPic(Request $request)
    {

        $ppjbs = Ppjbnew::where('pic', $request->pic)
            ->whereMonth('tanggal_permohonan', $request->bulan)
            ->where('tax_processed', 0)
            ->get();

        foreach ($ppjbs as $ppjb) {

            $ppjb->update([
                'tax_processed' => 1
            ]);
        }

        return back()->with('success', 'Pajak berhasil diproses');
    }

    public function process(Request $request)
    {

        $ppjb = Ppjbnew::findOrFail($request->ppjb_id);

        $fee = $ppjb->total;

        $akumulasi = Ppjbnew::where('pic', $ppjb->pic)
            ->where('tax_processed', 1)
            ->sum('total');

        $pph21 = $this->hitungPph21($akumulasi, $fee);

        $ppjb->update([
            'tax_processed' => 1
        ]);

        return back()->with('success', 'PPH21 berhasil diproses');
    }

    private function simulasiPph21($ppjbs, $akumulasiAwal)
    {

        $layers = [
            [0, 60000000, 0.05],
            [60000000, 250000000, 0.15],
            [250000000, 500000000, 0.25],
            [500000000, PHP_INT_MAX, 0.35]
        ];

        $akumulasi = $akumulasiAwal;

        $rows = [];

        foreach ($ppjbs as $ppjb) {

            $fee = $ppjb->total;

            $startAkumulasi = $akumulasi;

            $pajak = 0;
            $sisa = $fee;
            $current = $akumulasi;
            $tarifText = '';

            foreach ($layers as $layer) {

                $max = $layer[1];
                $rate = $layer[2];

                if ($current >= $max) {
                    continue;
                }

                $space = $max - $current;

                $kena = min($space, $sisa);

                if ($kena > 0) {

                    $pajak += $kena * $rate;

                    $tarifText .= ($rate * 100) . '% & ';

                    $sisa -= $kena;

                    $current += $kena;
                }

                if ($sisa <= 0) {
                    break;
                }
            }

            $akumulasi += $fee;

            $tarifText = rtrim($tarifText, ' & ');

            $rows[] = [
                'no_ppjb' => $ppjb->no_ppjb,
                'fee' => $fee,
                'akumulasi' => $akumulasi,
                'tarif' => $tarifText,
                'pajak' => $pajak
            ];
        }

        return $rows;
    }
}
