@extends('adminlte::page')

@section('title', 'Tambah Activity')

@section('content_header')
    <h1>Tambah Daily Activity</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('dailyactivity.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="activity_date"
                       value="{{ request('date') }}"
                       class="form-control" required>
            </div>

            <div class="form-group">
                <label>Jenis Kegiatan</label>
                <select name="jenis_kegiatan" class="form-control" required>
                <option value="">-Pilih-</option>

                @foreach($kegiatan as $kode => $nama)
                    <option value="{{ $kode }}">
                        {{ $kode }} - {{ $nama }}
                    </option>
                @endforeach

            </select>
            </div>

            <div class="form-group">
                <label>Project Number</label>
                <input type="text" name="project_number" class="form-control">
            </div>

            <div class="form-group">
                <label>Uraian</label>
                <textarea name="uraian" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Link</label>
                <input type="text" name="link" class="form-control">
            </div>

            <div class="form-group">
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('dailyactivity.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

        </form>

    </div>
</div>

@stop