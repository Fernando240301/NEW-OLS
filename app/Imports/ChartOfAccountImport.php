<?php

namespace App\Imports;

use App\Models\ChartOfAccount;
use App\Models\AccountType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ChartOfAccountImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // Skip header
            if ($index === 0) continue;

            $code = trim($row[0] ?? '');
            $name = trim($row[1] ?? '');

            if (!$code || !$name) continue;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DIGIT PERTAMA
            |--------------------------------------------------------------------------
            */

            $firstDigit = substr($code, 0, 1);

            if (!in_array($firstDigit, ['1', '2', '3', '4', '5', '6', '7', '8'])) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | AUTO ACCOUNT TYPE
            |--------------------------------------------------------------------------
            */

            $typeMap = [
                '1' => 'ASSET',
                '2' => 'LIABILITY',
                '3' => 'EQUITY',
                '4' => 'REVENUE',
            ];

            $typeCode = $typeMap[$firstDigit] ?? 'EXPENSE';

            $type = AccountType::where('code', $typeCode)->first();

            if (!$type) continue;

            /*
            |--------------------------------------------------------------------------
            | DETERMINE LEVEL
            |--------------------------------------------------------------------------
            */

            $level = substr_count($code, '-') + 1;

            /*
            |--------------------------------------------------------------------------
            | DETERMINE POSTABLE
            |--------------------------------------------------------------------------
            */

            $isPostable = true;

            if (!str_contains($code, '-')) {
                $isPostable = false;
            }

            if (str_ends_with($code, '-000')) {
                $isPostable = false;
            }

            /*
            |--------------------------------------------------------------------------
            | FIND PARENT
            |--------------------------------------------------------------------------
            */

            $parent = null;

            if (str_contains($code, '-')) {

                $parts = explode('-', $code);
                array_pop($parts);
                $parentCode = implode('-', $parts);

                $parent = ChartOfAccount::where('code', $parentCode)->first();
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT / UPDATE
            |--------------------------------------------------------------------------
            */

            ChartOfAccount::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'account_type_id' => $type->id,
                    'parent_id' => $parent?->id,
                    'level' => $level,
                    'is_postable' => $isPostable,
                    'is_active' => true,
                    'is_system' => false,
                    'normal_balance' => $type->normal_balance,
                    'opening_balance' => 0,
                ]
            );
        }
    }
}
