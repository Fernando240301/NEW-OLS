@extends('adminlte::page')

@section('title', 'Income Statement')

@section('content_header')
    <h1>Income Statement</h1>
@stop

@section('content')

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <h4>Revenue</h4>
    <table class="table table-bordered">
        @foreach ($revenues as $row)
            <tr>
                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="font-weight-bold">
            <td>Total Revenue</td>
            <td class="text-right">{{ number_format($totalRevenue, 2) }}</td>
        </tr>
    </table>

    <h4>Expenses</h4>
    <table class="table table-bordered">
        @foreach ($expenses as $row)
            <tr>
                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="font-weight-bold">
            <td>Total Expense</td>
            <td class="text-right">{{ number_format($totalExpense, 2) }}</td>
        </tr>
    </table>

    <hr>

    <h3>
        Net Profit:
        @if ($netProfit >= 0)
            <span class="text-success">
                {{ number_format($netProfit, 2) }}
            </span>
        @else
            <span class="text-danger">
                {{ number_format($netProfit, 2) }}
            </span>
        @endif
    </h3>

@stop
