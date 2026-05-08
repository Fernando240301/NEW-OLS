<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountTypeController extends Controller
{
    public function index()
    {
        $types = AccountType::latest()->paginate(10);
        return view('finance.account_types.index', compact('types'));
    }

    public function create()
    {
        return view('finance.account_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:account_types',
            'name' => 'required',
            'normal_balance' => 'required|in:debit,credit'
        ]);

        AccountType::create($request->all());

        return redirect()->route('account-types.index')
            ->with('success', 'Account Type berhasil dibuat');
    }

    public function edit(AccountType $accountType)
    {
        return view('finance.account_types.edit', compact('accountType'));
    }

    public function update(Request $request, AccountType $accountType)
    {
        $request->validate([
            'code' => 'required|unique:account_types,code,' . $accountType->id,
            'name' => 'required',
            'normal_balance' => 'required|in:debit,credit'
        ]);

        $accountType->update($request->all());

        return redirect()->route('account-types.index')
            ->with('success', 'Account Type berhasil diupdate');
    }

    public function destroy(AccountType $accountType)
    {
        $accountType->delete();

        return back()->with('success', 'Account Type berhasil dihapus');
    }
}
