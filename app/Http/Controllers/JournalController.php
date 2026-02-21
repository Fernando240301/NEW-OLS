<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\ChartOfAccount;
use App\Services\JournalService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    protected $service;

    public function __construct(JournalService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $journals = Journal::latest()->paginate(15);

        return view('finance.journals.index', compact('journals'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $accounts = ChartOfAccount::where('is_postable', true)
            ->orderBy('code')
            ->get();

        return view('finance.journals.create', compact('accounts'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        try {
            $journal = $this->service->createDraft($request->all());
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return redirect()->route('journals.index')
            ->with('success', 'Journal berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | POST
    |--------------------------------------------------------------------------
    */

    public function post(Journal $journal)
    {
        $this->service->post($journal);

        return back()->with('success', 'Journal berhasil dipost.');
    }

    /*
    |--------------------------------------------------------------------------
    | REVERSE
    |--------------------------------------------------------------------------
    */

    public function reverse(Journal $journal)
    {
        $this->service->reverse($journal);

        return back()->with('success', 'Journal berhasil direverse.');
    }

    public function show(Journal $journal)
    {
        $journal->load(['details.account']);

        return view('finance.journals.show', compact('journal'));
    }

    public function edit(Journal $journal)
    {
        if ($journal->status !== 'draft') {
            abort(403, 'Hanya draft yang bisa diedit.');
        }

        $journal->load('details');

        $accounts = ChartOfAccount::where('is_postable', true)
            ->orderBy('code')
            ->get();

        return view('finance.journals.edit', compact('journal', 'accounts'));
    }

    public function update(Request $request, Journal $journal)
    {
        try {

            $this->service->updateDraft($journal, $request->all());

            return redirect()
                ->route('journals.show', $journal)
                ->with('success', 'Journal berhasil diupdate.');
        } catch (\Exception $e) {

            dd($e->getMessage());
        }
    }
}
