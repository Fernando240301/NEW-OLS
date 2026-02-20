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
        $request->validate([
            'journal_date' => 'required|date',
            'details' => 'required|array|min:2',
        ]);

        $journal = $this->service->createDraft($request->all());

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
}
