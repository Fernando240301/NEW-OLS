@extends('adminlte::page')

@section('title', 'Penawaran')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Penawaran</h1>
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
        <h3 class="card-title">📦 Data Penawaran</h3>
        <div class="card-tools">
            <a href="{{ route('penawaran.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="penawaranTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Surat</th>
                        <th>Judul Surat</th>
                        <th>Nama Client</th>
                        <th>PIC Client</th>
                        <th>PIC MIT</th>
                        <th>Tanggal</th>
                        <th>Status / QR</th>
                        <th>Approve</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
@foreach ($data as $item)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $item->nosurat }}</td>
    <td>{{ $item->judul }}</td>
    <td>{{ $item->namaclient }}</td>
    <td>{{ $item->pic ?? '-' }}</td>
    <td>{{ $item->picMitUser->name ?? '-' }}</td>
    <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}</td>

    {{-- STATUS & QR CODE --}}
    <td class="text-center">
    @if ($item->status === 'revision')
        <span class="badge badge-danger">Revisi</span>
    @elseif ($item->barcode)
        <img src="{{ Storage::url($item->barcode) }}" width="70"><br>
        <small>
            Approved by:<br>
            <b>{{ $item->approver->name ?? '-' }}</b>
        </small>
    @else
        <span class="badge badge-warning">Draft</span>
    @endif

    {{-- CATATAN REVISI --}}
    @if ($item->is_revision)
        <small class="text-danger d-block mt-1">
            <b>Catatan:</b><br>
            {{ $item->revision_note }}
        </small>
    @endif
</td>


   {{-- APPROVE --}}
<td class="text-center">

    {{-- MODE REVISI --}}
    @if ($item->status === 'revision')
        <span class="badge badge-danger">Revisi</span>

    {{-- BELUM APPROVE --}}
    @elseif (!$item->barcode)

        @if ($item->can_approve)
            <form action="{{ route('penawaran.approve', $item->id) }}"
                  method="POST"
                  onsubmit="return confirm('Approve penawaran ini?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i> Approve
                </button>
            </form>
        @else
            <span class="badge badge-warning">QR belum tersedia</span>
        @endif

    {{-- SUDAH APPROVE --}}
    @else
        <span class="text-muted">✔</span>
    @endif

</td>


    {{-- AKSI: Word / PDF / Upload --}}
   <td class="text-center">

    {{-- WORD --}}
    @if ($item->approved_word)
        <a href="{{ asset('storage/' . $item->approved_word) }}"
           class="btn btn-sm btn-success mb-1"
           target="_blank">
            <i class="fas fa-file-word"></i> Word Approved
        </a>
    @elseif ($item->surat)
        <a href="{{ asset('storage/' . $item->surat) }}"
           class="btn btn-sm btn-primary mb-1"
           target="_blank">
            <i class="fas fa-file-word"></i> Word
        </a>
    @endif

    {{-- PDF --}}
    @if ($item->pdf)
        <a href="{{ asset('storage/' . $item->pdf) }}"
           class="btn btn-sm btn-success mb-1"
           target="_blank">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    @endif

    {{-- EDIT (HANYA DRAFT) --}}
    @if (!$item->barcode && $item->status !== 'revision')
        <a href="{{ route('penawaran.edit', $item->id) }}"
           class="btn btn-sm btn-primary mb-1">
            <i class="fas fa-edit"></i> Edit
        </a>
    @endif

    {{-- AJUKAN REVISI (HANYA SUDAH APPROVE) --}}
    @if ($item->barcode)
        <button class="btn btn-sm btn-danger mb-1"
                data-toggle="modal"
                data-target="#revisiModal{{ $item->id }}">
            <i class="fas fa-undo"></i> Revisi
        </button>
    @endif

    {{-- UPLOAD ULANG (DRAFT / REVISI) --}}
    @if (in_array($item->status, ['draft', 'revision']))
        <a href="{{ route('penawaran.upload', $item->id) }}"
           class="btn btn-sm btn-warning mb-1">
            <i class="fas fa-upload"></i> Upload Ulang
        </a>
    @endif

</td>

    </td>
    
</tr>
@endforeach
                </tbody>
            </table>
            @foreach ($data as $item)
<div class="modal fade" id="revisiModal{{ $item->id }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('penawaran.revisi', $item->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Ajukan Revisi</h5>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Revisi</label>
                        <textarea name="revision_note"
                                  class="form-control"
                                  required
                                  placeholder="Contoh: perubahan harga, typo, dsb"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger">Ajukan Revisi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#penawaranTable').DataTable({
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
