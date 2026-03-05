<?php

namespace App\Http\Controllers;

use App\Models\AccountCategory;
use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    public function index()
    {
        $categories = AccountCategory::with('type')
            ->latest()
            ->paginate(10);

        return view('finance.account_categories.index', compact('categories'));
    }

    public function create()
    {
        $types = AccountType::all();
        return view('finance.account_categories.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'code' => 'nullable',
            'name' => 'required'
        ]);

        AccountCategory::create($request->all());

        return redirect()->route('account-categories.index')
            ->with('success', 'Category berhasil dibuat');
    }

    public function edit(AccountCategory $accountCategory)
    {
        $types = AccountType::all();
        return view('finance.account_categories.edit', compact('accountCategory', 'types'));
    }

    public function update(Request $request, AccountCategory $accountCategory)
    {
        $request->validate([
            'account_type_id' => 'required|exists:account_types,id',
            'code' => 'nullable',
            'name' => 'required'
        ]);

        $accountCategory->update($request->all());

        return redirect()->route('account-categories.index')
            ->with('success', 'Category berhasil diupdate');
    }

    public function destroy(AccountCategory $accountCategory)
    {
        $accountCategory->delete();

        return back()->with('success', 'Category berhasil dihapus');
    }
}
