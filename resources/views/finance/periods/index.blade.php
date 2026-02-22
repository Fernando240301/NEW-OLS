@extends('adminlte::page')

@section('title', 'Accounting Period')

@section('content_header')
    <h1>Accounting Period</h1>
@stop

@section('content')

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($periods as $period)
                <tr>
                    <td>{{ $period->year }}</td>
                    <td>
                        {{ \Carbon\Carbon::create()->month($period->month)->translatedFormat('F') }}
                    </td>
                    <td>
                        @if ($period->status == 'open')
                            <span class="badge bg-success">Open</span>
                        @else
                            <span class="badge bg-danger">Closed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('period.show', $period) }}" class="btn btn-info btn-sm">
                            Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
