@extends('adminlte::page')

@section('content')
    <div class="container">
        <h3>Manual Journal</h3>

        <a href="{{ route('journals.create') }}" class="btn btn-primary mb-3">
            + Buat Journal
        </a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($journals as $journal)
                    <tr>
                        <td>{{ $journal->journal_no }}</td>
                        <td>{{ $journal->journal_date }}</td>
                        <td>
                            <span
                                class="badge bg-{{ $journal->status == 'posted' ? 'success' : ($journal->status == 'draft' ? 'warning' : 'danger') }}">
                                {{ strtoupper($journal->status) }}
                            </span>
                        </td>
                        <td class="d-flex gap-1">

                            <a href="{{ route('journals.show', $journal) }}"
                               class="btn btn-sm btn-info">
                                Detail
                            </a>
                        
                            @if ($journal->status == 'draft')
                                <a href="{{ route('journals.edit', $journal) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                        
                                <form method="POST"
                                      action="{{ route('journals.post', $journal) }}"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        Post
                                    </button>
                                </form>
                            @endif
                        
                            @if ($journal->status == 'posted')
                                <form method="POST"
                                      action="{{ route('journals.reverse', $journal) }}"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">
                                        Reverse
                                    </button>
                                </form>
                            @endif
                        
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $journals->links() }}
    </div>
@endsection
