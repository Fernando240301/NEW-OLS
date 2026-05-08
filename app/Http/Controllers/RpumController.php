<?php

namespace App\Http\Controllers;

use App\Exports\RpumExport;
use App\Models\Ppjbnew;
use App\Models\Rpum;
use App\Models\Journal;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\AccountingPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class RpumController extends Controller
{
    public function index()
    {
        // 🔥 FIX: rpum → rpums
        $data = Ppjbnew::with('rpums')
            ->whereIn('status', ['waiting_rpum', 'partial', 'paid'])
            ->orderByDesc('id')
            ->get();

        return view('finance.rpum.index', compact('data'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'tanggal_transfer' => 'required|date',
            'jumlah' => 'required|numeric|min:1',
            'jenis_pembayaran' => 'required|in:CA,Biaya',
            'bukti_transfer' => 'required|file|mimes:jpg,png,pdf|max:2048'
        ]);

        DB::transaction(function () use ($request, $id) {

            $ppjb = Ppjbnew::with(['rpums', 'details'])->findOrFail($id);

            // =====================
            // VALIDASI JENIS BAYAR (TIDAK BOLEH CAMPUR)
            // =====================
            $existing = $ppjb->rpums()->first();

            if ($existing && $existing->jenis_pembayaran !== $request->jenis_pembayaran) {
                throw new \Exception("Jenis pembayaran harus konsisten (tidak boleh campur CA & Biaya).");
            }

            // =====================
            // HITUNG SISA
            // =====================
            $totalPaid = $ppjb->rpums()->sum('jumlah');
            $sisa = $ppjb->total - $totalPaid;

            if ($request->jumlah > $sisa) {
                throw new \Exception("Pembayaran melebihi sisa tagihan.");
            }

            // =====================
            // UPLOAD FILE
            // =====================
            $filePath = $request->file('bukti_transfer')
                ? $request->file('bukti_transfer')->store('rpum', 'public')
                : null;

            // =====================
            // SIMPAN RPUM
            // =====================
            Rpum::create([
                'ppjb_id' => $ppjb->id,
                'tanggal_transfer' => $request->tanggal_transfer,
                'jumlah' => $request->jumlah,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'bukti_transfer' => $filePath,
                'verified_by' => auth()->user()->userid,
                'verified_at' => now()
            ]);

            // =====================
            // BUAT PERIOD
            // =====================
            $period = AccountingPeriod::firstOrCreate(
                [
                    'year' => date('Y', strtotime($request->tanggal_transfer)),
                    'month' => date('m', strtotime($request->tanggal_transfer)),
                ],
                [
                    'start_date' => date('Y-m-01', strtotime($request->tanggal_transfer)),
                    'end_date' => date('Y-m-t', strtotime($request->tanggal_transfer)),
                    'status' => 'open'
                ]
            );

            if ($period->status === 'closed') {
                throw new \Exception("Accounting period sudah ditutup.");
            }

            // =====================
            // BUAT JOURNAL
            // =====================
            $journal = Journal::create([
                'journal_no'     => 'JR-' . now()->format('YmHis'),
                'journal_date'   => $request->tanggal_transfer,
                'reference_type' => 'RPUM',
                'reference_id'   => $ppjb->id,
                'period_id'      => $period->id,
                'status'         => 'draft'
            ]);

            /*
            =====================================
            CASE 1: CA
            =====================================
            */
            if ($request->jenis_pembayaran === 'CA') {

                JournalDetail::create([
                    'journal_id' => $journal->id,
                    'account_id' => $ppjb->kas_account_id, // ✅ tetap dari PPJB
                    'debit'      => $request->jumlah,
                    'credit'     => 0,
                    'memo'       => 'Cash Advance ' . $ppjb->no_ppjb
                ]);
            }

            /*
            =====================================
            CASE 2: BIAYA
            =====================================
            */
            else {

                foreach ($ppjb->details as $detail) {

                    // 🔥 PROPORSIONAL (WAJIB untuk partial)
                    $ratio = $request->jumlah / $ppjb->total;

                    $subtotal = ($detail->qty * $detail->harga) * $ratio;

                    JournalDetail::create([
                        'journal_id' => $journal->id,
                        'account_id' => $detail->coa_id,
                        'debit'      => $subtotal,
                        'credit'     => 0,
                        'memo'       => 'Biaya ' . $ppjb->no_ppjb
                    ]);
                }
            }

            // =====================
            // CREDIT KAS
            // =====================
            $kas = ChartOfAccount::where('code', '1101-002')->first();

            if (!$kas) {
                throw new \Exception("COA Kas (1101-002) tidak ditemukan.");
            }

            JournalDetail::create([
                'journal_id' => $journal->id,
                'account_id' => $kas->id,
                'debit'      => 0,
                'credit'     => $request->jumlah,
                'memo'       => 'Pembayaran RPUM ' . $ppjb->no_ppjb
            ]);

            // =====================
            // UPDATE STATUS
            // =====================
            $totalPaid += $request->jumlah;

            $status = $totalPaid >= $ppjb->total ? 'paid' : 'partial';

            $ppjb->update([
                'status' => $status,
                'payment_type' => $ppjb->payment_type ?? $request->jenis_pembayaran // optional
            ]);
        });

        return back()->with('success', 'Pembayaran berhasil disimpan & journal dibuat.');
    }

    public function datatables()
    {
        $query = Ppjbnew::with('rpums')
            ->whereIn('status', ['waiting_rpum', 'partial', 'paid'])
            ->orderByRaw('status = "paid"') // paid di bawah
            ->orderByDesc('id');

        return DataTables::of($query)

            ->addColumn('total_format', function ($p) {
                return number_format($p->total, 0, ',', '.');
            })

            ->addColumn('action', function ($p) {

                $previewPPJB = '
                    <button class="btn btn-sm btn-outline-primary btn-preview"
                        data-url="' . route('ppjb-new.pdf', $p->id) . '"
                        title="Preview PPJB">
                        <i class="fas fa-eye"></i>
                    </button> &nbsp;
                ';

                // 🔥 tombol history pembayaran
                $btnHistory = '';

                if ($p->rpums->count() > 0) {
                    $btnHistory = '
                        <button class="btn btn-sm btn-outline-secondary btn-history"
                            data-id="' . $p->id . '"
                            title="Lihat Pembayaran">
                            <i class="fas fa-folder-open"></i> (' . $p->rpums->count() . ')
                        </button> &nbsp;
                    ';
                }

                // 🔥 STATUS LUNAS
                if ($p->status === 'paid') {
                    return '
                        <div class="d-flex align-items-center gap-2">
                            ' . $previewPPJB . '
                            ' . $btnHistory . '
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check"></i> Lunas
                            </span>
                        </div> &nbsp;
                    ';
                }

                // 🔥 BELUM / PARTIAL
                return '
                    <div class="d-flex align-items-center gap-2">
                        ' . $previewPPJB . '
                        ' . $btnHistory . '

                        <button class="btn btn-sm btn-primary btn-rpum px-3"
                            data-id="' . $p->id . '"
                            data-total="' . $p->total . '">
                            <i class="fas fa-wallet"></i> Bayar
                        </button>
                    </div> &nbsp;
                ';
            })

            ->addColumn('project_no', function ($p) {
                return $p->refer_project ?? '-';
            })

            ->addColumn('payment_info', function ($p) {

                $paid = $p->rpums->sum('jumlah');
                $sisa = $p->total - $paid;

                return '
                    <div style="font-size:12px">
                        Bayar: <b>' . number_format($paid,0,',','.') . '</b><br>
                        Sisa: <b>' . number_format($sisa,0,',','.') . '</b>
                    </div> &nbsp;
                ';
            })

            ->addColumn('description', function ($p) {
                return $p->pekerjaan ?? '-';
            })

            ->addColumn('pic', function ($p) {
                return $p->pic ?? '-';
            })

            ->rawColumns(['action', 'payment_info', 'project_no'])
            ->make(true);
    }

    public function history($id)
    {
        $data = Rpum::where('ppjb_id', $id)
            ->orderByDesc('tanggal_transfer')
            ->get();

        return response()->json($data);
    }

    public function export()
    {
        return Excel::download(new RpumExport, 'RPUM.xlsx');
    }
}