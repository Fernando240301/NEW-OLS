@extends('adminlte::page')

@section('title', 'Data Prospect')

@section('plugins.Datatables', true)

@section('content_header')
    <h1>Data Prospect</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📦 Data Prospect</h3>
        <div class="card-tools">
            <a href="{{ route('prospect.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="prospectTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Klient</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th>Sales</th>
                        <th>Create User</th>
                        <th>Create Date</th>
                        <th>FILE</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->judul }}</td>
                        <td>{{ $item->klient }}</td>
                        <td>{{ $item->catatan }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->sales }}</td>
                        <td>{{ $item->SysUser->username ?? '-' }}</td>
                        <td>{{ $item->createdate }}</td>
<td>
    @if (!empty($item->file))
        @foreach ($item->file as $file)
            <a href="{{ asset('storage/'.$file) }}"
               target="_blank"
               class="d-block text-primary">
                📎 {{ basename($file) }}
            </a>
        @endforeach
    @else
        <span class="text-muted">Tidak ada file</span>
    @endif
</td>
    </div>
</div>
                        <td class="text-center">
                            <a href="{{ route('prospect.edit', $item->id) }}"
                            class="btn btn-warning btn-sm me-1 mb-1"
                            onclick="event.stopPropagation();">
                            Edit
                            </a>

                            <form action="{{ route('prospect.delete', $item->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm mb-1"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                    Hapus
                                </button>
                            </form>
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
    $('#prospectTable').DataTable({
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
