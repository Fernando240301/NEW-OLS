<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\AccountCategory;
use App\Models\AccountType;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ChartOfAccountImport;

class ChartOfAccountController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $accounts = ChartOfAccount::with('childrenRecursive')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        return view('finance.chart_of_accounts.index', compact('accounts'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = AccountCategory::all();

        // SEMUA akun boleh jadi parent
        $parents = ChartOfAccount::orderBy('code')->get();

        return view('finance.chart_of_accounts.create', compact('categories', 'parents'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (AUTO CODE SYSTEM)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'account_category_id' => 'nullable|exists:account_categories,id',
            'parent_id' => 'nullable|exists:chartofaccounts,id',
            'code' => 'nullable'
        ]);

        $parent = $request->parent_id
            ? ChartOfAccount::findOrFail($request->parent_id)
            : null;

        /*
        |--------------------------------------------------------------------------
        | AUTO CODE
        |--------------------------------------------------------------------------
        */

        if ($parent) {
            $code = $this->generateChildCode($parent);
        } else {
            if (!$request->code) {
                return back()->withErrors([
                    'code' => 'Root account wajib isi kode.'
                ])->withInput();
            }

            $code = $request->code;
        }

        // Cegah duplicate
        if (ChartOfAccount::where('code', $code)->exists()) {
            return back()->withErrors([
                'code' => 'Kode akun sudah digunakan.'
            ])->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DIGIT PERTAMA
        |--------------------------------------------------------------------------
        */

        $firstDigit = substr($code, 0, 1);

        if (!in_array($firstDigit, ['1', '2', '3', '4', '5', '6', '7', '8'])) {
            return back()->withErrors([
                'code' => 'Kode akun harus diawali angka 1-8.'
            ])->withInput();
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
        $type = AccountType::where('code', $typeCode)->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | LEVEL
        |--------------------------------------------------------------------------
        */

        $level = substr_count($code, '-') + 1;

        /*
        |--------------------------------------------------------------------------
        | DETERMINE HEADER / POSTABLE
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
        | VALIDASI PARENT
        |--------------------------------------------------------------------------
        */

        if ($parent) {

            if ($parent->account_type_id !== $type->id) {
                return back()->withErrors([
                    'parent_id' => 'Parent harus memiliki tipe akun yang sama.'
                ])->withInput();
            }

            // Parent otomatis jadi header
            $parent->update([
                'is_postable' => false
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        ChartOfAccount::create([
            'code' => $code,
            'name' => $request->name,
            'account_type_id' => $type->id,
            'account_category_id' => $request->account_category_id,
            'parent_id' => $parent?->id,
            'level' => $level,
            'is_postable' => $isPostable,
            'is_active' => true,
            'is_system' => false,
            'normal_balance' => $type->normal_balance,
            'opening_balance' => 0,
        ]);

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'COA berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE CHILD CODE (CORE ENGINE)
    |--------------------------------------------------------------------------
    */

    private function generateChildCode($parent)
    {
        $lastChild = ChartOfAccount::where('parent_id', $parent->id)
            ->orderByDesc('code')
            ->first();

        if (!$lastChild) {
            $nextNumber = '01';
        } else {
            $parts = explode('-', $lastChild->code);
            $lastSegment = end($parts);
            $nextNumber = str_pad(((int)$lastSegment + 1), 2, '0', STR_PAD_LEFT);
        }

        return $parent->code . '-' . $nextNumber;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX GENERATE CODE (UNTUK PREVIEW DI BLADE)
    |--------------------------------------------------------------------------
    */

    public function generateNextCode($parentId)
    {
        $parent = ChartOfAccount::findOrFail($parentId);

        return response()->json([
            'code' => $this->generateChildCode($parent)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ChartOfAccountImport, $request->file('file'));

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'Import COA berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(ChartOfAccount $chart_of_account)
    {
        $categories = AccountCategory::all();

        return view('finance.chart_of_accounts.edit', [
            'account' => $chart_of_account,
            'categories' => $categories,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, ChartOfAccount $chart_of_account)
    {
        if ($chart_of_account->is_system) {
            return back()->withErrors([
                'error' => 'Akun system tidak boleh diubah.'
            ]);
        }

        $request->validate([
            'name' => 'required',
            'account_category_id' => 'nullable|exists:account_categories,id',
        ]);

        $chart_of_account->update([
            'name' => $request->name,
            'account_category_id' => $request->account_category_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'COA berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(ChartOfAccount $chart_of_account)
    {
        if ($chart_of_account->is_system) {
            return back()->withErrors([
                'error' => 'Akun system tidak boleh dihapus.'
            ]);
        }

        if ($chart_of_account->children()->exists()) {
            return back()->withErrors([
                'error' => 'Akun memiliki turunan dan tidak bisa dihapus.'
            ]);
        }

        $chart_of_account->delete();

        return redirect()->route('chart-of-accounts.index')
            ->with('success', 'COA berhasil dihapus');
    }
}
