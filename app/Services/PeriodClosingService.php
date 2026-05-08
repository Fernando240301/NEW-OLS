<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use App\Models\ChartOfAccount;
use App\Models\GlEntry;
use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Exception;

class PeriodClosingService
{
    public function close(AccountingPeriod $period)
    {
        if ($period->status !== 'open') {
            throw new Exception("Period sudah ditutup.");
        }

        return DB::transaction(function () use ($period) {

            $start = $period->start_date;
            $end   = $period->end_date;

            $revenueCategories = [12, 13];
            $expenseCategories = [14, 15, 16];

            $accounts = ChartOfAccount::whereIn(
                'account_category_id',
                array_merge($revenueCategories, $expenseCategories)
            )
                ->where('is_postable', true)
                ->get();

            $journal = Journal::create([
                'journal_no'   => 'CLS-' . $period->year . $period->month,
                'journal_date' => $period->end_date,
                'period_id'    => $period->id,
                'status'       => 'posted'
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($accounts as $account) {

                $summary = GlEntry::where('account_id', $account->id)
                    ->whereBetween('entry_date', [$start, $end])
                    ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                    ->first();

                $debit  = $summary->debit ?? 0;
                $credit = $summary->credit ?? 0;

                if ($account->normal_balance === 'credit') {
                    $balance = $credit - $debit;
                } else {
                    $balance = $debit - $credit;
                }

                if ($balance == 0) continue;

                if (in_array($account->account_category_id, $revenueCategories)) {

                    $journal->details()->create([
                        'account_id' => $account->id,
                        'debit'  => $balance,
                        'credit' => 0
                    ]);

                    $totalDebit += $balance;
                }

                if (in_array($account->account_category_id, $expenseCategories)) {

                    $journal->details()->create([
                        'account_id' => $account->id,
                        'debit'  => 0,
                        'credit' => $balance
                    ]);

                    $totalCredit += $balance;
                }
            }

            $retained = ChartOfAccount::where('name', 'Retained Earnings')->first();

            if (!$retained) {
                throw new Exception("Akun Retained Earnings tidak ditemukan.");
            }

            $net = $totalDebit - $totalCredit;

            if ($net > 0) {
                $journal->details()->create([
                    'account_id' => $retained->id,
                    'debit'  => 0,
                    'credit' => $net
                ]);
            } else {
                $journal->details()->create([
                    'account_id' => $retained->id,
                    'debit'  => abs($net),
                    'credit' => 0
                ]);
            }

            $period->update([
                'status' => 'closed'
            ]);

            return $journal;
        });
    }
}
