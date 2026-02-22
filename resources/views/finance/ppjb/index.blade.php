@extends('adminlte::page')

@section('title', 'PPJB')

@section('content_header')
    <h1>Daftar PPJB</h1>
@stop

@section('content')

    <a href="{{ route('ppjb-new.create') }}" class="btn btn-primary mb-3">
        Buat PPJB
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>No PPJB</th>
                <th>Tanggal</th>
                <th>PIC</th>
                <th>Total</th>
                <th>Status</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ppjbs as $ppjb)
                <tr>
                    <td>{{ $ppjb->no_ppjb }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->format('d-m-Y') }}
                    </td>

                    <td>{{ $ppjb->pic }}</td>

                    <td class="text-end">
                        Rp {{ number_format($ppjb->total, 2, ',', '.') }}
                    </td>

                    <td>
                        @if ($ppjb->status == 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($ppjb->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">{{ $ppjb->status }}</span>
                        @endif
                    </td>

                    <td>

                        <a href="{{ route('ppjb-new.show', $ppjb->id) }}" class="btn btn-sm btn-info">
                            Detail
                        </a>

                        @if ($ppjb->status == 'draft')
                            <form action="{{ route('ppjb-new.approve', $ppjb->id) }}" method="POST"
                                style="display:inline-block"
                                onsubmit="return confirm('Approve PPJB ini dan generate journal?')">
                                @csrf
                                <button class="btn btn-sm btn-success">
                                    Approve
                                </button>
                            </form>
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Belum ada data PPJB
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $ppjbs->links() }}

@stop
