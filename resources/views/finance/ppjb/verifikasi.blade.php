@extends('adminlte::page')

@section('title', 'Verifikasi Dokumen')

@section('content_header')
    <h1>Verifikasi Dokumen</h1>
@stop

@section('content')

    <table class="table table-bordered table-striped">
        <thead class="text-center">
            <tr>
                <th>Jenis</th>
                <th>Nomor</th>
                <th>Referensi</th>
                <th>PIC</th>
                <th>Project No</th>
                <th>Status</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
                <tr>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $doc['type'] }}</span>
                    </td>

                    <td>{{ $doc['number'] }}</td>

                    <td>{{ $doc['ref'] ?? '-' }}</td>

                    <td>{{ $doc['pic'] }}</td>

                    <td>{{ $doc['project_no'] }}</td>

                    <td class="text-center">
                        <span class="badge bg-warning">
                            {{ strtoupper(str_replace('_', ' ', $doc['status'])) }}
                        </span>
                    </td>

                    <td class="text-center">

                        {{-- Preview --}}
                        <a href="{{ $doc['pdf'] }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Edit (jika belum approved) --}}
                        @if ($doc['status'] !== 'approved' && $doc['status'] !== 'closed')
                            <a href="{{ $doc['edit'] }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif

                        {{-- Approve --}}
                        <form action="{{ $doc['route'] }}" method="POST" style="display:inline-block">
                            @csrf
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Tidak ada dokumen untuk diverifikasi
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

@stop
