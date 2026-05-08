@extends('adminlte::page')

@section('title', 'Detail Journal')

@section('content_header')
    <h1>Journal Detail</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>No Journal:</strong><br>
                    {{ $journal->journal_no }}
                </div>
                <div class="col-md-4">
                    <strong>Tanggal:</strong><br>
                    {{ $journal->journal_date }}
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong><br>
                    @if ($journal->status === 'draft')
                        <span class="badge badge-secondary">DRAFT</span>
                    @elseif($journal->status === 'posted')
                        <span class="badge badge-success">POSTED</span>
                    @elseif($journal->status === 'reversed')
                        <span class="badge badge-danger">REVERSED</span>
                    @endif
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th width="150">Debit</th>
                        <th width="150">Credit</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($journal->details as $detail)
                        <tr>
                            <td>
                                {{ $detail->account->code }} -
                                {{ $detail->account->name }}
                            </td>
                            <td class="text-right">
                                {{ number_format($detail->debit, 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($detail->credit, 0, ',', '.') }}
                            </td>
                            <td>
                                {{ $detail->memo }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-right mt-3">
                <strong>
                    Total Debit:
                    {{ number_format($journal->details->sum('debit'), 0, ',', '.') }}
                    |
                    Total Credit:
                    {{ number_format($journal->details->sum('credit'), 0, ',', '.') }}
                </strong>
            </div>

        </div>

        <div class="card-footer">

            @if ($journal->status === 'draft')
                <form method="POST" action="{{ route('journals.post', $journal) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-success">
                        Post Journal
                    </button>
                </form>
            @endif

            @if ($journal->status === 'posted')
                <form method="POST" action="{{ route('journals.reverse', $journal) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-danger">
                        Reverse
                    </button>
                </form>
            @endif

            <a href="{{ route('journals.index') }}" class="btn btn-secondary">
                Kembali
            </a>

        </div>
    </div>

@stop
