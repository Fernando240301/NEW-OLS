@extends('adminlte::page')

@section('title', 'Data DAFTAR PO')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Data DAFTAR PO</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📦 Data PPJB</h3>
        <div class="card-tools">
            <a href="{{ route('daftarpo.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="daftarpoTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Surat</th>
                        <th>Nama Pengaju</th>
                        <th>Kepada</th>
                        <th>Dokumen Penawaran</th>
                        <th>Status PO</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->no_surat }}</td>
                        <td>{{ $item->namapengaju }}</td>                       
                        <td>{{ $item->to }}</td>
                        <td class="text-center">
    @if($item->file_penawaran)

        <a href="{{ asset('storage/' . $item->file_penawaran) }}"
           target="_blank"
           class="btn btn-primary btn-sm">
            <i class="fas fa-eye"></i> Preview
        </a>

    @else
        <span class="badge bg-secondary">Belum Upload</span>
    @endif
</td>
                        <td>
                            @if($item->status_daftarpo == 'APPROVED FINAL')
                                <span class="badge bg-success">Approved</span>
                            @elseif(str_contains($item->status_daftarpo,'MENUNGGU'))
                                <span class="badge bg-warning">{{ $item->status_daftarpo }}</span>
                            @else
                                <span class="badge bg-danger">{{ $item->status_daftarpo }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Edit --}}
                            <a href="{{ route('daftarpo.edit', $item->id) }}"
                               class="btn btn-warning btn-sm me-1 mb-1"
                               onclick="event.stopPropagation();">
                               Revisi
                            </a>

                            <!-- {{-- Hapus --}}
                            <form action="{{ route('daftarpo.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </button>
                            </form> -->

                            {{-- Preview --}}
                            <a href="{{ route('daftarpo.preview', $item->id) }}"
                               target="_blank"
                               class="btn btn-primary btn-sm me-1 mb-1">
                               Preview
                            </a>
 
@if($item->canApprove())
    <form action="{{ route('daftarpo.approve', $item->id) }}" method="POST" style="display:inline-block;">
        @csrf
        <button class="btn btn-success btn-sm">
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
    $('#daftarpoTable').DataTable({
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
