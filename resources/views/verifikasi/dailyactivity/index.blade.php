@extends('adminlte::page')

@section('title', 'Verifikasi Aktivitas')

@section('content')
<div class="container-fluid">

<h3 class="mb-3">Verifikasi Aktivitas</h3>

{{-- FILTER --}}
<div class="card mb-4">
    <div class="card-body bg-light">
        <form method="GET">

            <div class="row">
                <div class="col-md-3">
                    <label>Tgl Awal</label>
                    <input type="date" name="tgl_awal"
                           value="{{ $tgl_awal }}"
                           class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Tgl Akhir</label>
                    <input type="date" name="tgl_akhir"
                           value="{{ $tgl_akhir }}"
                           class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="verifikasi"
                            {{ $status=='verifikasi'?'selected':'' }}>
                            Verifikasi
                        </option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Search</label>
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Cari Nama...">
                </div>
            </div>

        </form>
    </div>
</div>

{{-- PER PAGE --}}
<div class="mb-2">
    <form method="GET" class="form-inline">
        <label class="mr-2">Tampilkan</label>
        <select name="per_page"
                onchange="this.form.submit()"
                class="form-control mr-2">
            <option value="10" {{ $perPage==10?'selected':'' }}>10</option>
            <option value="25" {{ $perPage==25?'selected':'' }}>25</option>
            <option value="50" {{ $perPage==50?'selected':'' }}>50</option>
        </select>
        Data Perhalaman
    </form>
</div>

{{-- TABLE --}}
<div class="card">
<div class="card-body table-responsive">

<table class="table table-bordered table-hover">
<thead class="thead-light">
<tr>
    <th>No</th>
    <th>Verifikasi</th>
    <th>Tanggal</th>
    <th>Nama</th>
    <th>Divisi</th>
    <th>Kegiatan</th>
    <th>Uraian</th>
    <th>PR Num</th>
    <th>Status</th>
    <th>Evidence</th>
</tr>
</thead>
<tbody>

@forelse($data as $item)
<tr>
    <td>{{ $loop->iteration }}</td>

    <td>
        <form action="{{ route('activity.approve',$item->id) }}"
              method="POST"
              style="display:inline;">
            @csrf
            <button class="btn btn-sm btn-success">
                ✔
            </button>
        </form>

        <form action="{{ route('activity.reject',$item->id) }}"
              method="POST"
              style="display:inline;">
            @csrf
            <button class="btn btn-sm btn-danger">
                ✖
            </button>
        </form>
    </td>

    <td>{{ $item->tanggal }}</td>
    <td>{{ $item->user->name }}</td>
    <td>{{ $item->user->divisi ?? '-' }}</td>
    <td>{{ $item->jenis_kegiatan }}</td>
    <td>{{ $item->uraian }}</td>
    <td>{{ $item->project_number ?? '-' }}</td>

    <td>
        <span class="badge badge-info">
            {{ $item->status }}
        </span>
    </td>

    <td>
        @if($item->link)
            <a href="{{ $item->link }}"
               target="_blank"
               class="btn btn-sm btn-outline-primary">
                Link
            </a>
        @endif

        @foreach($item->evidences as $file)
            <a href="{{ asset('storage/'.$file->file_path) }}"
               target="_blank"
               class="btn btn-sm btn-outline-secondary">
                File#{{ $loop->iteration }}
            </a>
        @endforeach
    </td>
</tr>

@empty
<tr>
    <td colspan="10" class="text-center">
        Tidak ada data
    </td>
</tr>
@endforelse

</tbody>
</table>

{{ $data->links() }}

</div>
</div>

</div>
@endsection