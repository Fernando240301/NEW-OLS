<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class IncomeStatementController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        $accounts = ChartOfAccount::where('is_postable', true)
            ->with('glEntries')
            ->get();

        $revenues = [];
        $expenses = [];

        $totalRevenue = 0;
        $totalExpense = 0;

        foreach ($accounts as $account) {

            $query = $account->glEntries();

            if ($start && $end) {
                $query->whereBetween('entry_date', [$start, $end]);
            }

            $debit  = $query->sum('debit');
            $credit = $query->sum('credit');

            $balance = 0;

            if ($account->normal_balance === 'credit') {
                $balance = $credit - $debit;
            } else {
                $balance = $debit - $credit;
            }

            $revenueCategories = [12, 13];
            $expenseCategories = [14, 15, 16];

            // Revenue
            if (in_array($account->account_category_id, $revenueCategories)) {
                $revenues[] = [
                    'account' => $account,
                    'balance' => $balance
                ];
                $totalRevenue += $balance;
            }

            // Expense
            if (in_array($account->account_category_id, $expenseCategories)) {
                $expenses[] = [
                    'account' => $account,
                    'balance' => $balance
                ];
                $totalExpense += $balance;
            }
        }

        $netProfit = $totalRevenue - $totalExpense;

        return view('finance.reports.income_statement', compact(
            'revenues',
            'expenses',
            'totalRevenue',
            'totalExpense',
            'netProfit',
            'start',
            'end'
        ));
    }
}
