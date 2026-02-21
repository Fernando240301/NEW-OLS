@extends('adminlte::page')

@section('title', 'General Ledger')

@section('content_header')
    <h1>General Ledger</h1>
@stop

@section('content')

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="account_id" class="form-control" required>
                    <option value="">-- Select Account --</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            <div class="col-md-2">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    @if ($selectedAccount)

        <h5>
            Account: {{ $selectedAccount->code }} - {{ $selectedAccount->name }}
        </h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Running Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td>{{ $entry->entry_date }}</td>
                        <td>{{ number_format($entry->debit, 2) }}</td>
                        <td>{{ number_format($entry->credit, 2) }}</td>
                        <td>
                            @if ($entry->running_balance < 0)
                                <span class="text-danger">
                                    ({{ number_format(abs($entry->running_balance), 2) }})
                                </span>
                            @else
                                {{ number_format($entry->running_balance, 2) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

@stop
