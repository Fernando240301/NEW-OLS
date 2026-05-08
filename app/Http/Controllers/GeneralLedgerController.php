<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\GlEntry;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = ChartOfAccount::where('is_postable', true)
            ->orderBy('code')
            ->get();

        $selectedAccount = null;
        $entries = collect();
        $runningBalance = 0;

        if ($request->account_id) {

            $selectedAccount = ChartOfAccount::findOrFail($request->account_id);

            $query = GlEntry::where('account_id', $selectedAccount->id)
                ->orderBy('entry_date')
                ->orderBy('id');

            if ($request->start_date && $request->end_date) {
                $query->whereBetween('entry_date', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $entries = $query->get()->map(function ($entry) use (&$runningBalance, $selectedAccount) {

                if ($selectedAccount->normal_balance === 'debit') {
                    $runningBalance += $entry->debit;
                    $runningBalance -= $entry->credit;
                } else {
                    $runningBalance += $entry->credit;
                    $runningBalance -= $entry->debit;
                }

                $entry->running_balance = $runningBalance;

                return $entry;
            });
        }

        return view('finance.reports.general_ledger', compact(
            'accounts',
            'selectedAccount',
            'entries'
        ));
    }
}
