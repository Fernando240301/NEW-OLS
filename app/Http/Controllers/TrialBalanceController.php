<?php

namespace App\Http\Controllers;

use App\Models\GlEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        /*
    |--------------------------------------------------------------------------
    | 1️⃣ Ambil summary GL (1 query saja)
    |--------------------------------------------------------------------------
    */

        $glSummary = \App\Models\GlEntry::selectRaw("
            account_id,
            SUM(debit) as total_debit,
            SUM(credit) as total_credit
        ")
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('entry_date', [$start, $end]);
            })
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');


        /*
    |--------------------------------------------------------------------------
    | 2️⃣ Ambil semua akun postable
    |--------------------------------------------------------------------------
    */

        $accounts = \App\Models\ChartOfAccount::where('is_postable', true)
            ->orderBy('code')
            ->get()
            ->map(function ($account) use ($glSummary) {

                $summary = $glSummary[$account->id] ?? null;

                $account->total_debit  = $summary->total_debit  ?? 0;
                $account->total_credit = $summary->total_credit ?? 0;

                // Hitung balance sesuai normal balance
                if ($account->normal_balance === 'debit') {
                    $account->balance = $account->total_debit - $account->total_credit;
                } else {
                    $account->balance = $account->total_credit - $account->total_debit;
                }

                return $account;
            });


        /*
    |--------------------------------------------------------------------------
    | 3️⃣ Hide zero jika dicentang
    |--------------------------------------------------------------------------
    */

        if ($request->hide_zero) {
            $accounts = $accounts->filter(function ($acc) {
                return ($acc->total_debit != 0 || $acc->total_credit != 0);
            });
        }

        /*
    |--------------------------------------------------------------------------
    | 4️⃣ Grand Total
    |--------------------------------------------------------------------------
    */

        $grandDebit  = $accounts->sum('total_debit');
        $grandCredit = $accounts->sum('total_credit');

        return view('finance.reports.trial_balance', compact(
            'accounts',
            'start',
            'end',
            'grandDebit',
            'grandCredit'
        ));
    }
}
