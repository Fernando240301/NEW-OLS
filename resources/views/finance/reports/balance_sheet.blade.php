@extends('adminlte::page')

@section('title', 'Balance Sheet')

@section('content_header')
    <h1>Balance Sheet</h1>
@stop

@section('content')

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <h4>Assets</h4>
    <table class="table table-bordered">
        @foreach ($assets as $row)
            <tr>
                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="font-weight-bold">
            <td>Total Assets</td>
            <td class="text-right">{{ number_format($totalAsset, 2) }}</td>
        </tr>
    </table>

    <h4>Liabilities</h4>
    <table class="table table-bordered">
        @foreach ($liabilities as $row)
            <tr>
                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="font-weight-bold">
            <td>Total Liabilities</td>
            <td class="text-right">{{ number_format($totalLiability, 2) }}</td>
        </tr>
    </table>

    <h4>Equity</h4>
    <table class="table table-bordered">
        @foreach ($equities as $row)
            <tr>
                <td>{{ $row['account']->code }} - {{ $row['account']->name }}</td>
                <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
            </tr>
        @endforeach
        <tr class="font-weight-bold">
            <td>Total Equity</td>
            <td class="text-right">{{ number_format($totalEquity, 2) }}</td>
        </tr>
    </table>

    <hr>

    <h3>
        Assets: {{ number_format($totalAsset, 2) }} <br>
        Liability + Equity: {{ number_format($totalLiability + $totalEquity, 2) }}
    </h3>

@stop
