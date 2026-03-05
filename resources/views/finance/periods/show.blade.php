@extends('adminlte::page')

@section('title', 'Detail Period')

@section('content_header')
    <h1>Detail Accounting Period</h1>
@stop

@section('content')

    {{-- Closing Preview --}}
    <div class="card mt-4">
        <div class="card-header">
            <strong>Closing Preview</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Total Revenue</th>
                    <td>{{ number_format($totalRevenue, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Expense</th>
                    <td>{{ number_format($totalExpense, 2) }}</td>
                </tr>
                <tr>
                    <th>Net Income</th>
                    <td>
                        <strong>{{ number_format($netIncome, 2) }}</strong>
                    </td>
                </tr>
                <tr>
                    <th>Trial Balance</th>
                    <td>
                        @if ($isBalanced)
                            <span class="badge bg-success">BALANCED</span>
                        @else
                            <span class="badge bg-danger">NOT BALANCED</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Detail Period --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Year</th>
                    <td>{{ $period->year }}</td>
                </tr>
                <tr>
                    <th>Month</th>
                    <td>
                        {{ \Carbon\Carbon::create()->month($period->month)->translatedFormat('F') }}
                    </td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td>{{ $period->start_date }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td>{{ $period->end_date }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if ($period->status == 'open')
                            <span class="badge bg-success">Open</span>
                        @else
                            <span class="badge bg-danger">Closed</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Total Journal</th>
                    <td>{{ $totalJournal }}</td>
                </tr>
                <tr>
                    <th>Total GL Entries</th>
                    <td>{{ $totalGl }}</td>
                </tr>
            </table>

            <br>

            {{-- Tombol Close --}}
            @if ($period->status == 'open')

                @if (!$isBalanced)
                    <div class="alert alert-danger">
                        Trial Balance belum balanced.
                        Tidak bisa melakukan closing.
                    </div>
                @else
                    <form method="POST" action="{{ route('period.close', $period) }}">
                        @csrf
                        <button class="btn btn-danger">
                            Close Period
                        </button>
                    </form>
                @endif

            @endif

        </div>
    </div>

@stop
