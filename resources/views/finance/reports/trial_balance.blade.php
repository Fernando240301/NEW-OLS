@extends('adminlte::page')

@section('title', 'Trial Balance')

@section('content_header')
    <h1>Trial Balance</h1>
@stop

@section('content')
    <div class="mb-2">
        <strong>Total Debit:</strong> {{ number_format($grandDebit, 2) }}
        |
        <strong>Total Credit:</strong> {{ number_format($grandCredit, 2) }}
    </div>

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <div class="form-check mt-2">
                    <input type="checkbox" name="hide_zero" class="form-check-input"
                        {{ request('hide_zero') ? 'checked' : '' }}>
                    <label class="form-check-label">Hide Zero</label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>
        </div>
    </form>

    @if ($grandDebit == $grandCredit)
        <div class="alert alert-success">
            Trial Balance BALANCED
        </div>
    @else
        <div class="alert alert-danger">
            Trial Balance NOT BALANCED
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th>Total Debit</th>
                <th>Total Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($accounts as $account)
                <tr>
                    <td>{{ $account->code }}</td>
                    <td>{{ $account->name }}</td>
                    <td>{{ number_format($account->total_debit, 2) }}</td>
                    <td>{{ number_format($account->total_credit, 2) }}</td>
                    <td>
                        @if ($account->balance < 0)
                            <span class="text-danger">
                                ({{ number_format(abs($account->balance), 2) }})
                            </span>
                        @else
                            {{ number_format($account->balance, 2) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background:#f4f6f9;">
                <td colspan="2">TOTAL</td>
                <td>{{ number_format($grandDebit, 2) }}</td>
                <td>{{ number_format($grandCredit, 2) }}</td>
                <td>{{ number_format($grandDebit - $grandCredit, 2) }}</td>
            </tr>
        </tfoot>
    </table>

@stop
