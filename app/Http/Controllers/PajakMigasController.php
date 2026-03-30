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
        $ppjbs = Ppjbnew::where('status', 'approved')
            ->whereNull('tax_journal_id')
            ->where(function ($q) {

                // 🔵 MIGAS → tetap tampil (tanpa filter LPJB)
                $q->where('jenis_pengajuan', 'project_migas')

                    // 🟢 NON MIGAS → hanya yg belum LPJB
                    ->orWhere(function ($q2) {
                        $q2->where('jenis_pengajuan', '!=', 'project_migas')
                            ->whereDoesntHave('lpjbs');
                    });
            })
            ->orderBy('tanggal_permohonan')
            ->get();

        foreach ($ppjbs as $ppjb) {

            $akumulasi = Ppjbnew::where('pic', $ppjb->pic)
                ->whereNotNull('tax_journal_id')
                ->sum('total');

            $ppjb->pph21_preview = $this->hitungPph21($akumulasi, $ppjb->total);
        }

        $pics = Ppjbnew::where('status', 'approved')
            ->whereNull('tax_journal_id')
            ->where(function ($q) {

                $q->where('jenis_pengajuan', 'project_migas')

                    ->orWhere(function ($q2) {
                        $q2->where('jenis_pengajuan', '!=', 'project_migas')
                            ->whereDoesntHave('lpjbs');
                    });
            })
            ->select(
                'pic',
                DB::raw('MONTH(tanggal_permohonan) as bulan'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('pic', 'bulan')
            ->orderBy('bulan')
            ->get();

        foreach ($pics as $pic) {

            $akumulasi = Ppjbnew::where('pic', $pic->pic)
                ->whereNotNull('tax_journal_id')
                ->sum('total');

            $pic->pph21 = $this->hitungPph21($akumulasi, $pic->total);
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
            ->whereNull('tax_journal_id')
            ->whereMonth('tanggal_permohonan', $bulan)
            ->where(function ($q) {

                $q->where('jenis_pengajuan', 'project_migas')

                    ->orWhere(function ($q2) {
                        $q2->where('jenis_pengajuan', '!=', 'project_migas')
                            ->whereDoesntHave('lpjbs');
                    });
            })
            ->orderBy('tanggal_permohonan')
            ->get(['id', 'no_ppjb', 'total']);

        foreach ($ppjbs as $p) {
            $p->pdf_url = route('ppjb-new.pdf', $p->id);
        }

        $akumulasi = Ppjbnew::where('pic', $pic)
            ->whereNotNull('tax_journal_id')
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
        DB::transaction(function () use ($request) {

            $ppjbs = Ppjbnew::where('pic', $request->pic)
                ->whereMonth('tanggal_permohonan', $request->bulan)
                ->whereNull('tax_journal_id')
                ->where(function ($q) {
                    $q->where('jenis_pengajuan', 'project_migas')
                        ->orWhere(function ($q2) {
                            $q2->where('jenis_pengajuan', '!=', 'project_migas')
                                ->whereDoesntHave('lpjbs');
                        });
                })
                ->orderBy('tanggal_permohonan')
                ->get();

            if ($ppjbs->isEmpty()) {
                throw new \Exception("Tidak ada data untuk diproses.");
            }

            // 🔥 AKUMULASI SEBELUMNYA
            $akumulasi = Ppjbnew::where('pic', $request->pic)
                ->whereNotNull('tax_journal_id')
                ->sum('total');

            $totalPajak = 0;

            foreach ($ppjbs as $ppjb) {

                $pph21 = $this->hitungPph21($akumulasi, $ppjb->total);

                $totalPajak += $pph21;

                $akumulasi += $ppjb->total;
            }

            // 🔥 BUAT JOURNAL
            $journal = $this->createJournalPph21(
                $request->pic,
                $request->bulan,
                $totalPajak
            );

            // 🔥 UPDATE PPJB (FIX DI SINI)
            foreach ($ppjbs as $ppjb) {
                $ppjb->update([
                    'tax_processed' => 1,
                    'tax_journal_id' => $journal->id // ✅ BENAR
                ]);
            }
        });

        return back()->with('success', 'Pajak & Journal berhasil diproses');
    }

    public function process(Request $request)
    {
        DB::transaction(function () use ($request) {

            $ppjb = Ppjbnew::findOrFail($request->ppjb_id);

            $fee = $ppjb->total;

            $akumulasi = Ppjbnew::where('pic', $ppjb->pic)
                ->whereNotNull('tax_journal_id')
                ->sum('total');

            $pph21 = $this->hitungPph21($akumulasi, $fee);

            $journal = $this->createJournalPph21(
                $ppjb->pic,
                date('m', strtotime($ppjb->tanggal_permohonan)),
                $pph21
            );

            $ppjb->update([
                'tax_processed' => 1,
                'tax_journal_id' => $journal->id // ✅
            ]);
        });

        return back()->with('success', 'PPH21 & Journal berhasil diproses');
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

    private function createJournalPph21($pic, $bulan, $pph21)
    {
        $period = AccountingPeriod::where('status', 'open')->first();

        if (!$period) {
            throw new \Exception("Tidak ada periode open.");
        }

        // ✅ COA SESUAI REKOMENDASI
        $beban = ChartOfAccount::where('code', '6101-001-08-02')->first(); // Biaya Pajak
        $hutang = ChartOfAccount::where('code', '2104-001')->first(); // Hutang PPh21

        if (!$beban || !$hutang) {
            throw new \Exception("COA Pajak belum ditemukan.");
        }

        $journal = Journal::create([
            'journal_no'     => $this->generateJournalNo(),
            'journal_date'   => now(),
            'reference_type' => 'PPH21',
            'reference_id'   => null,
            'period_id'      => $period->id,
            'status'         => 'draft'
        ]);

        // 🔵 DEBIT → BIAYA PAJAK
        JournalDetail::create([
            'journal_id' => $journal->id,
            'account_id' => $beban->id,
            'debit'      => $pph21,
            'credit'     => 0,
            'memo'       => "PPH21 PIC {$pic} bulan {$bulan}"
        ]);

        // 🔴 CREDIT → HUTANG PAJAK
        JournalDetail::create([
            'journal_id' => $journal->id,
            'account_id' => $hutang->id,
            'debit'      => 0,
            'credit'     => $pph21,
            'memo'       => "PPH21 PIC {$pic} bulan {$bulan}"
        ]);

        return $journal;
    }

    private function generateJournalNo()
    {
        $prefix = 'JR-PPH21-' . now()->format('Ym') . '-';

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
