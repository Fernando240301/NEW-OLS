<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\ChartOfAccount;
use App\Models\AccountingPeriod;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class JournalService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE DRAFT JOURNAL
    |--------------------------------------------------------------------------
    */

    public function createDraft(array $data): Journal
    {
        return DB::transaction(function () use ($data) {

            if (empty($data['details']) || count($data['details']) < 2) {
                throw new Exception("Journal minimal harus memiliki 2 baris.");
            }

            $period = $this->getOpenPeriod($data['journal_date']);

            $journal = Journal::create([
                'journal_no'     => $this->generateJournalNumber($data['journal_date']),
                'journal_date'   => $data['journal_date'],
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id'   => $data['reference_id'] ?? null,
                'period_id'      => $period->id,
                'status'         => 'draft',
            ]);

            $totalDebit  = 0;
            $totalCredit = 0;

            foreach ($data['details'] as $row) {

                $account = ChartOfAccount::findOrFail($row['account_id']);

                if (!$account->is_postable) {
                    throw new Exception("Account {$account->code} bukan akun postable.");
                }

                $debit  = (float) ($row['debit'] ?? 0);
                $credit = (float) ($row['credit'] ?? 0);

                if ($debit > 0 && $credit > 0) {
                    throw new Exception("Debit dan Credit tidak boleh diisi bersamaan.");
                }

                if ($debit == 0 && $credit == 0) {
                    throw new Exception("Debit atau Credit harus diisi.");
                }

                $journal->details()->create([
                    'account_id' => $account->id,
                    'project_id' => $row['project_id'] ?? null,
                    'debit'      => $debit,
                    'credit'     => $credit,
                    'memo'       => $row['memo'] ?? null, // 🔥 TAMBAHKAN INI
                ]);

                $totalDebit  += $debit;
                $totalCredit += $credit;
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new Exception("Journal tidak balance.");
            }

            return $journal;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | POST JOURNAL
    |--------------------------------------------------------------------------
    */

    public function post(Journal $journal): Journal
    {
        if ($journal->status !== 'draft') {
            throw new Exception("Hanya journal draft yang bisa dipost.");
        }

        if ($journal->period->status !== 'open') {
            throw new Exception("Period sudah ditutup.");
        }

        return DB::transaction(function () use ($journal) {

            $totalDebit  = $journal->details()->sum('debit');
            $totalCredit = $journal->details()->sum('credit');

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new Exception("Journal tidak balance.");
            }

            $journal->update([
                'status'    => 'posted',
                'posted_at' => now(),
            ]);

            return $journal;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REVERSE JOURNAL
    |--------------------------------------------------------------------------
    */

    public function reverse(Journal $journal): Journal
    {
        if ($journal->status !== 'posted') {
            throw new Exception("Hanya journal posted yang bisa direverse.");
        }

        if ($journal->reversal_of) {
            throw new Exception("Journal ini adalah reversal dan tidak bisa direverse lagi.");
        }

        if (Journal::where('reversal_of', $journal->id)->exists()) {
            throw new Exception("Journal ini sudah pernah direverse.");
        }

        return DB::transaction(function () use ($journal) {

            $reversal = Journal::create([
                'journal_no'     => $this->generateJournalNumber(now()),
                'journal_date'   => now(),
                'reference_type' => 'REVERSAL',
                'reference_id'   => $journal->id,
                'period_id'      => $journal->period_id,
                'status'         => 'posted',
                'reversal_of'    => $journal->id,
                'posted_at'      => now(),
            ]);

            foreach ($journal->details as $detail) {

                $reversal->details()->create([
                    'account_id' => $detail->account_id,
                    'project_id' => $detail->project_id,
                    'debit'      => $detail->credit,
                    'credit'     => $detail->debit,
                    'memo'       => $detail->memo, // 🔥 TAMBAHKAN INI
                ]);
            }

            $journal->update([
                'status' => 'reversed'
            ]);

            return $reversal;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GET OPEN PERIOD
    |--------------------------------------------------------------------------
    */

    private function getOpenPeriod($date): AccountingPeriod
    {
        $date = Carbon::parse($date);

        $period = AccountingPeriod::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();

        if (!$period) {
            throw new Exception("Period tidak ditemukan.");
        }

        if ($period->status !== 'open') {
            throw new Exception("Period sudah closed.");
        }

        return $period;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE JOURNAL NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateJournalNumber($date): string
    {
        $prefix = Carbon::parse($date)->format('Ym');

        $last = Journal::where('journal_no', 'like', "JR-$prefix%")
            ->orderByDesc('journal_no')
            ->first();

        if (!$last) {
            return "JR-$prefix-0001";
        }

        $lastNumber = (int) substr($last->journal_no, -4);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return "JR-$prefix-$nextNumber";
    }
}
