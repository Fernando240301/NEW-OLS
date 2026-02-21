<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\GlEntry;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        $end = $request->end_date ?? now()->toDateString();

        $glSummary = GlEntry::selectRaw("
                account_id,
                SUM(debit) as total_debit,
                SUM(credit) as total_credit
            ")
            ->whereDate('entry_date', '<=', $end)
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $accounts = ChartOfAccount::with('category')
            ->where('is_postable', true)
            ->get();

        $assets = [];
        $liabilities = [];
        $equities = [];

        $totalAsset = 0;
        $totalLiability = 0;
        $totalEquity = 0;

        foreach ($accounts as $account) {

            $summary = $glSummary[$account->id] ?? null;

            $debit  = $summary->total_debit  ?? 0;
            $credit = $summary->total_credit ?? 0;

            if ($account->normal_balance === 'debit') {
                $balance = $debit - $credit;
            } else {
                $balance = $credit - $debit;
            }

            // Asset
            if (in_array($account->account_type_id, [25, 30])) {
                $assets[] = ['account' => $account, 'balance' => $balance];
                $totalAsset += $balance;
            }

            // Liability
            if ($account->account_type_id == 26) {
                $liabilities[] = ['account' => $account, 'balance' => $balance];
                $totalLiability += $balance;
            }

            // Equity
            if ($account->account_type_id == 27) {
                $equities[] = ['account' => $account, 'balance' => $balance];
                $totalEquity += $balance;
            }
        }

        return view('finance.reports.balance_sheet', compact(
            'assets',
            'liabilities',
            'equities',
            'totalAsset',
            'totalLiability',
            'totalEquity',
            'end'
        ));
    }
}
