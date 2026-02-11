@extends('adminlte::page')

@section('title', 'Data PPJB')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Data PPJB</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📦 Data PPJB</h3>
        <div class="card-tools">
            <a href="{{ route('ppjb.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="PPJBTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Surat</th>
                        <th>Dari</th>
                        <th>Tanggal Permohonan</th>
                        <th>Tanggal Dibutuhkan</th>
                        <th>Project No.</th>
                        <th>PIC</th>
                        <th>Status PPJB</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nosurat }}</td>
                        <td>{{ $item->dari }}</td>
                        <td>{{ $item->tanggal_permohonan }}</td>
                        <td>{{ $item->tanggal_dibutuhkan }}</td>
                        <td>{{ $item->project }}</td>
                        <td>{{ $item->PIC }}</td>
                        <td>
                            @if($item->status == 'APPROVED FINAL')
                                <span class="badge bg-success">Approved</span>
                            @elseif(str_contains($item->status,'MENUNGGU'))
                                <span class="badge bg-warning">{{ $item->status }}</span>
                            @else
                                <span class="badge bg-danger">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Edit --}}
                            <a href="{{ route('ppjb.edit', $item->id) }}"
                               class="btn btn-warning btn-sm me-1 mb-1"
                               onclick="event.stopPropagation();">
                               Edit
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('ppjb.delete', $item->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </button>
                            </form>

                            {{-- Preview --}}
                            <a href="{{ route('ppjb.preview', $item->id) }}"
                               target="_blank"
                               class="btn btn-primary btn-sm me-1 mb-1">
                               Preview
                            </a>

                            {{-- ✅ Approve hanya untuk user terkait --}}
                            @php
    $currentApproval = $item->approvals
        ->where('user_id', auth()->id())
        ->where('is_approved', false)
        ->first();
@endphp

@if($currentApproval)
    <form action="{{ route('ppjb.approve', $item->id) }}" method="POST" style="display:inline-block;">
        @csrf
        <button class="btn btn-success btn-sm mb-1">
            Approve
        </button>
    </form>
@endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#PPJBTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "columnDefs": [
            { "responsivePriority": 1, "targets": -1 }, // kolom Aksi prioritas 1
            { "responsivePriority": 2, "targets": 0 }   // kolom No prioritas 2
        ]
    });
});
</script>
@endsection
