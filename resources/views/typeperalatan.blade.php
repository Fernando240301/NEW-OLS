@extends('adminlte::page')

@section('title', 'Type Peralatan')

@section('content_header')
    <h1>Type Peralatan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📦 Data Type Peralatan</h3>
        <div class="card-tools">
            <a href="{{ route('tambahperalatan') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-plus"></i> Tambah Data
</a>

        </div>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover">
    <thead>
    <tr>
        <th>No</th>
        <th>Nama Peralatan</th>
        <th style="width: 20%" class="text-center">Aksi</th>
    </tr>
</thead>
<tbody>
@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama_peralatan }}</td>
        <td class="text-center">
            <button class="btn btn-info btn-sm">Detail</button>
            <a href="{{ route('tambahperalatan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('tambahperalatan.destroy', $item->id) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
@stop
