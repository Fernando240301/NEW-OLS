@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('plugins.DatatablesButtons', true)

@section('content')
    <div class="container">
        <h3>Manual Journal</h3>

        <a href="{{ route('journals.create') }}" class="btn btn-primary mb-3">
            + Buat Journal
        </a>

        <table id="journalTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date</th>
                    <th>Dokumen</th>
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
                            @php $type = strtoupper($journal->reference_type); @endphp

                            <div>

                                {{-- BADGE TYPE --}}
                                @if ($type == 'PPJB')
                                    <span class="badge bg-primary">PPJB</span>
                                @elseif ($type == 'LPJB')
                                    <span class="badge bg-info">LPJB</span>
                                @elseif ($type == 'MIGAS')
                                    <span class="badge bg-dark">MIGAS</span>
                                @elseif (str_contains($type, 'REV'))
                                    <span class="badge bg-danger">REV</span>
                                @else
                                    <span class="badge bg-secondary">MANUAL</span>
                                @endif

                                {{-- NOMOR DOKUMEN --}}
                                @if ($journal->ref_no && $journal->ref_no != '-')
                                    <div style="font-size:12px; font-weight:600;">

                                        {{-- PPJB → PDF --}}
                                        @if ($type == 'PPJB')
                                            <a href="{{ route('ppjb-new.pdf', $journal->reference_id) }}" target="_blank"
                                                class="text-primary">
                                                {{ $journal->ref_no }}
                                            </a>

                                            {{-- LPJB → PDF --}}
                                        @elseif ($type == 'LPJB')
                                            <a href="{{ route('lpjb.pdf', $journal->reference_id) }}" target="_blank"
                                                class="text-info">
                                                {{ $journal->ref_no }}
                                            </a>

                                            {{-- MIGAS / lainnya --}}
                                        @else
                                            {{ $journal->ref_no }}
                                        @endif

                                    </div>
                                @endif

                            </div>
                        </td>
                        <td>
                            <span
                                class="badge bg-{{ $journal->status == 'posted' ? 'success' : ($journal->status == 'draft' ? 'warning' : 'danger') }}">
                                {{ strtoupper($journal->status) }}
                            </span>
                        </td>
                        <td class="d-flex gap-1">

                            <a href="{{ route('journals.show', $journal) }}" class="btn btn-sm btn-info">
                                Detail
                            </a> &nbsp;

                            @if ($journal->status == 'draft')
                                <a href="{{ route('journals.edit', $journal) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                &nbsp;

                                <form method="POST" action="{{ route('journals.post', $journal) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        Post
                                    </button>
                                </form>
                            @endif

                            &nbsp;

                            @if ($journal->status == 'posted')
                                <form method="POST" action="{{ route('journals.reverse', $journal) }}" class="d-inline">
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

    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            $('#journalTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [
                    [1, 'desc']
                ], // sort by date

                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-success'
                }]
            });

        });
    </script>
@stop
