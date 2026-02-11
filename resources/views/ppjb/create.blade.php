@extends('adminlte::page')

@section('title', 'Tambah PPJB')

@section('content_header')
    <h1>Tambah PPJB</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Form PPJB</h3>
    </div>
    <form action="{{ route('ppjb.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Dari</label>
                <input type="text" name="dari"
                       class="form-control @error('dari') is-invalid @enderror"
                       value="{{ old('dari') }}"
                       placeholder=".....">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Tanggal Permohonan</label>
                <input type="date" name="tanggal_permohonan"
                       class="form-control @error('tanggal_permohonan') is-invalid @enderror"
                       value="{{ old('tanggal_permohonan') }}"
                       placeholder="....">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Dropdown peralatan -->
            <div class="form-group">
                <label>Tanggal Dibutuhkan</label>
                <input type="date" name="tanggal_dibutuhkan"
                       class="form-control @error('tanggal_permohonan') is-invalid @enderror"
                       value="{{ old('tanggal_permohonan') }}"
                       placeholder="Contoh: PT.....">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Project No</label>
                <input type="text" name="project"
                       class="form-control @error('project') is-invalid @enderror"
                       value="{{ old('project') }}"
                       placeholder="Contoh: PR/FR/ ">
                @error('project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan"
                       class="form-control @error('pekerjaan') is-invalid @enderror"
                       value="{{ old('pekerjaan') }}"
                       placeholder="Contoh: PT.....">
                @error('pekerjaan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>PIC</label>
                <input type="text" name="PIC"
                       class="form-control @error('PIC') is-invalid @enderror"
                       value="{{ old('PIC') }}"
                       placeholder="SE/SR, DLL">
                @error('PIC')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Lokasi Project</label>
                <input type="text" name="lokasi_project"
                       class="form-control @error('lokasi_project') is-invalid @enderror"
                       value="{{ old('lokasi_project') }}"
                       placeholder="SE/SR, DLL">
                @error('PIC')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
    <label>Transport</label>
    <select name="transport"
        class="form-control @error('transport') is-invalid @enderror">
        
        <option value="">-- Pilih Transport dari/ke Kantor - Bandara / Stasiun / Pelabuhan (P/P) --</option>
        <option value="Jatinegara" {{ old('transport') == 'Jatinegara' ? 'selected' : '' }}>
            Jatinegara
        </option>
        <option value="Gambir" {{ old('transport') == 'Gambir' ? 'selected' : '' }}>
            Gambir
        </option>
        <option value="Halim" {{ old('transport') == 'Halim' ? 'selected' : '' }}>
            Halim
        </option>
        <option value="Tg. Priok" {{ old('transport') == 'Tg. Priok' ? 'selected' : '' }}>
            Tg. Priok
        </option>
        <option value="Rambutan" {{ old('transport') == 'Rambutan' ? 'selected' : '' }}>
            Rambutan
        </option>
        <option value="Soetta" {{ old('transport') == 'Soetta' ? 'selected' : '' }}>
            Soetta
        </option>

    </select>

    @error('transport')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
        </div>
        <div class="card-footer">
            <a href="{{ route('ppjb.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Simpan</button>
        </div>
    </form>
</div>
@stop
