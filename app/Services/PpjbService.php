<?php

namespace App\Services;

use App\Models\Ppjbnew;
use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Exception;

class PpjbService
{
    public function approve(Ppjbnew $ppjb)
    {
        if ($ppjb->status !== 'draft') {
            throw new Exception("Hanya draft yang bisa di-approve.");
        }

        return DB::transaction(function () use ($ppjb) {

            $total = $ppjb->details()->sum(DB::raw('qty * harga'));

            $ppjb->update([
                'status' => 'approved',
                'total'  => $total
            ]);

            // 🔥 AUTO GENERATE JOURNAL
            $this->generateJournal($ppjb);

            return $ppjb;
        });
    }

    protected function generateJournal(Ppjbnew $ppjb)
    {
        // Nanti kita isi detailnya
    }
}
