<?php

namespace App\Http\Controllers;

use App\Models\AccountingPeriod;
use App\Services\PeriodClosingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;
use App\Models\GlEntry;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = AccountingPeriod::orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('finance.periods.index', compact('periods'));
    }

    public function close(AccountingPeriod $period)
    {
        app(PeriodClosingService::class)->close($period);

        return back()->with('success', 'Period berhasil ditutup.');
    }

    public function show(AccountingPeriod $period)
    {
        // Range tanggal
        $start = $period->start_date;
        $end   = $period->end_date;

        // ===============================
        // Hitung Revenue & Expense
        // ===============================

        $totalRevenue = \App\Models\GlEntry::whereBetween('entry_date', [$start, $end])
            ->whereHas('account.category', function ($q) {
                $q->where('name', 'Pendapatan')
                    ->orWhere('name', 'Pendapatan Lain');
            })
            ->sum(DB::raw('credit - debit'));

        $totalExpense = \App\Models\GlEntry::whereBetween('entry_date', [$start, $end])
            ->whereHas('account.category', function ($q) {
                $q->where('name', 'Beban')
                    ->orWhere('name', 'Beban Lain-lain')
                    ->orWhere('name', 'Harga Pokok Penjualan');
            })
            ->sum(DB::raw('debit - credit'));

        $netIncome = $totalRevenue - $totalExpense;

        // ===============================
        // Trial Balance Check
        // ===============================

        $totalDebit = \App\Models\GlEntry::whereBetween('entry_date', [$start, $end])
            ->sum('debit');

        $totalCredit = \App\Models\GlEntry::whereBetween('entry_date', [$start, $end])
            ->sum('credit');

        $isBalanced = round($totalDebit, 2) === round($totalCredit, 2);

        // ===============================
        // Tambahan: Total Journal & GL
        // ===============================

        $totalJournal = \App\Models\Journal::whereBetween('journal_date', [$start, $end])
            ->count();

        $totalGl = \App\Models\GlEntry::whereBetween('entry_date', [$start, $end])
            ->count();

        return view('finance.periods.show', compact(
            'period',
            'totalRevenue',
            'totalExpense',
            'netIncome',
            'isBalanced',
            'totalJournal',
            'totalGl'
        ));
    }
}
