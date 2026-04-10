@extends('adminlte::page')

@section('title', 'Jenis Layanan')

@section('content_header')
    <h1>Jenis Layanan</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📦 Data Jenis Layanan</h3>
            <div class="card-tools">
                <a href="{{ route('jenis_layanan.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>

            </div>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Layanan</th>
                        <th>Alias</th>
                        <th style="width: 20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_layanan }}</td>
                            <td>{{ $item->alias }}</td>
                            <td class="text-center">
                                <a href="{{ route('jenis_layanan.edit', $item->id) }}"
                                    class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('jenis_layanan.delete', $item->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
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
