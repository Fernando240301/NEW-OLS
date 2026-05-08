@extends('adminlte::page')

@section('title', 'Daftar PO')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Daftar Purchase Order</h1>
@endsection

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">📦 Data PO</h3>
        <div class="card-tools">
            <a href="{{ route('daftarpo.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    <div class="card-body table-responsive">
    <table class="table table-bordered table-hover" id="poTable">
        <thead>
            <tr>
                <th>No</th>
                <th>No PR</th>
                <th>Nama Pengaju</th>
                <th>Kepada</th>
                <th>Deskripsi</th>
                <th>Status PO</th>
                <th>Dokumen Penawaran</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $role = auth()->user()->role ?? null;
            @endphp
            @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->pr_number }}</td>
                <td>{{ $item->nama_pengaju }}</td>
                <td>{{ $item->to }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">

    {{-- ❌ Jika ada yang reject --}}
    @if(
        $item->marketing_status == 'rejected' ||
        $item->finance_status == 'rejected' ||
        $item->direktur_status == 'rejected'
    )
        <span class="badge badge-danger">Rejected</span>

    {{-- ⏳ Menunggu Marketing --}}
    @elseif($item->marketing_status == 'pending')
        <span class="badge badge-warning">Menunggu Marketing</span>

    {{-- ⏳ Menunggu Finance --}}
    @elseif($item->finance_status == 'pending')
        <span class="badge badge-info">Menunggu Finance</span>

    {{-- ⏳ Menunggu Direktur --}}
    @elseif($item->direktur_status == 'pending')
        <span class="badge badge-primary">Menunggu Direktur</span>

    {{-- ✅ Semua approve --}}
    @else
        <span class="badge badge-success">Approved</span>

    @endif

</td>
                <td class="text-center">
                    <!-- Lihat File jika ada -->
                    @if($item->dokumen_penawaran)
                        <a href="{{ asset('storage/dokumen_po/'.$item->dokumen_penawaran) }}" target="_blank">
                    <i class="fas fa-file"></i> Lihat File
                     </a>
                    @endif

                    <!-- Form Upload File -->
                    <form action="{{ route('daftarpo.upload', $item->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="dokumen_penawaran" class="form-control form-control-sm mb-1" required>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
                </td>
                
                <td class="text-center">
    {{-- PREVIEW --}}
    <a href="{{ route('daftarpo.preview', $item->id) }}" class="btn btn-sm btn-info mb-1" target="_blank">
        <i class="fas fa-eye"></i> Preview
    </a>

    {{-- EDIT --}}
    <a href="{{ route('daftarpo.edit', $item->id) }}" class="btn btn-sm btn-primary mb-1">
        <i class="fas fa-edit"></i> Revisi
    </a>
    <a href="{{ route('daftarpo.pdf', $item->id) }}" 
   class="btn btn-sm btn-dark mb-1" target="_blank">
    <i class="fas fa-file-pdf"></i> PDF
</a>

   {{-- APPROVAL --}}
<div class="mb-1">

    {{-- ================= MARKETING ================= --}}
    @if(auth()->user()->rolesid == 10)

        @if($item->marketing_status == 'pending')
            <a href="/po/{{ $item->id }}/marketing/approved" class="btn btn-sm btn-success">
                ✔ Approve
            </a>
            <a href="/po/{{ $item->id }}/marketing/rejected" class="btn btn-sm btn-danger">
                ✖ Reject
            </a>
        @elseif($item->marketing_status == 'approved')
            <span class="badge badge-success">✔ Marketing Approved</span>
        @else
            <span class="badge badge-danger">✖ Marketing Rejected</span>
        @endif

    @endif


    {{-- ================= FINANCE ================= --}}
    @if(auth()->user()->rolesid == 7)

        {{-- hanya bisa approve kalau marketing sudah approve --}}
        @if($item->marketing_status == 'approved')

            @if($item->finance_status == 'pending')
                <a href="/po/{{ $item->id }}/finance/approved" class="btn btn-sm btn-success">
                    ✔ Approve
                </a>
                <a href="/po/{{ $item->id }}/finance/rejected" class="btn btn-sm btn-danger">
                    ✖ Reject
                </a>
            @elseif($item->finance_status == 'approved')
                <span class="badge badge-success">✔ Finance Approved</span>
            @else
                <span class="badge badge-danger">✖ Finance Rejected</span>
            @endif

        @else
            <span class="badge badge-secondary">Menunggu Marketing</span>
        @endif

    @endif


    {{-- ================= DIREKTUR ================= --}}
    @if(auth()->user()->rolesid == 12)

        {{-- hanya bisa approve kalau finance sudah approve --}}
        @if($item->finance_status == 'approved')

            @if($item->direktur_status == 'pending')
                <a href="/po/{{ $item->id }}/direktur/approved" class="btn btn-sm btn-success">
                    ✔ Approve
                </a>
                <a href="/po/{{ $item->id }}/direktur/rejected" class="btn btn-sm btn-danger">
                    ✖ Reject
                </a>
            @elseif($item->direktur_status == 'approved')
                <span class="badge badge-success">✔ Direktur Approved</span>
            @else
                <span class="badge badge-danger">✖ Direktur Rejected</span>
            @endif

        @else
            <span class="badge badge-secondary">Menunggu Finance</span>
        @endif

    @endif

</div>

    {{-- DELETE --}}
    <form action="{{ route('daftarpo.destroy', $item->id) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Yakin ingin menghapus PO ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger mb-1">
            <i class="fas fa-trash"></i> Hapus
        </button>
    </form>
</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#poTable').DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: -1 }, // kolom Aksi prioritas 1
            { responsivePriority: 2, targets: 0 }   // kolom No prioritas 2
        ]
    });
});
</script>
@endsection