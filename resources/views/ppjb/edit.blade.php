@extends('adminlte::page')

@section('title', 'Edit PPJB')

@section('content_header')
    <h1>Edit PPJB</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Form Edit PPJB</h3>
    </div>

    <form action="{{ route('ppjb.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">

            {{-- DARI --}}
            <div class="form-group">
                <label>Dari</label>
                <input type="text" name="dari"
                       class="form-control @error('dari') is-invalid @enderror"
                       value="{{ old('dari', $item->dari) }}">
                @error('dari')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- TANGGAL PERMOHONAN --}}
            <div class="form-group">
                <label>Tanggal Permohonan</label>
                <input type="date" name="tanggal_permohonan"
                       class="form-control @error('tanggal_permohonan') is-invalid @enderror"
                       value="{{ old('tanggal_permohonan', $item->tanggal_permohonan) }}">
                @error('tanggal_permohonan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- TANGGAL DIBUTUHKAN --}}
            <div class="form-group">
                <label>Tanggal Dibutuhkan</label>
                <input type="date" name="tanggal_dibutuhkan"
                       class="form-control @error('tanggal_dibutuhkan') is-invalid @enderror"
                       value="{{ old('tanggal_dibutuhkan', $item->tanggal_dibutuhkan) }}">
                @error('tanggal_dibutuhkan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- PROJECT --}}
            <div class="form-group">
                <label>Project No</label>
                <input type="text" name="project"
                       class="form-control @error('project') is-invalid @enderror"
                       value="{{ old('project', $item->project) }}">
                @error('project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- PEKERJAAN --}}
            <div class="form-group">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan"
                       class="form-control @error('pekerjaan') is-invalid @enderror"
                       value="{{ old('pekerjaan', $item->pekerjaan) }}">
                @error('pekerjaan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- PIC --}}
            <div class="form-group">
                <label>PIC</label>
                <input type="text" name="PIC"
                       class="form-control @error('PIC') is-invalid @enderror"
                       value="{{ old('PIC', $item->PIC) }}">
                @error('PIC')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- LOKASI PROJECT --}}
            <div class="form-group">
                <label>Lokasi Project</label>
                <input type="text" name="lokasi_project"
                       class="form-control @error('lokasi_project') is-invalid @enderror"
                       value="{{ old('lokasi_project', $item->lokasi_project) }}">
                @error('lokasi_project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- TRANSPORT --}}
            <div class="form-group">
                <label>Transport</label>
                <select name="transport"
                        class="form-control @error('transport') is-invalid @enderror">

                    <option value="">-- Pilih Transport --</option>

                    @php
                        $transportList = [
                            'Jatinegara',
                            'Gambir',
                            'Halim',
                            'Tg. Priok',
                            'Rambutan',
                            'Soetta'
                        ];
                    @endphp

                    @foreach($transportList as $transport)
                        <option value="{{ $transport }}"
                            {{ old('transport', $item->transport) == $transport ? 'selected' : '' }}>
                            {{ $transport }}
                        </option>
                    @endforeach

                </select>

                @error('transport')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <hr>
<h5>Detail Barang</h5>

<div class="mb-2">
    <button type="button" class="btn btn-primary btn-sm" onclick="addRow()">
        + Tambah Barang
    </button>
</div>

<table class="table table-bordered" id="detailTable">
    <thead>
        <tr>
            <th>No</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Uraian / Spesifikasi Barang</th>
            <th>Harga</th>
            <th>Keterangan</th>
            <th width="120">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($item->details as $index => $detail)
        <tr>
            <td>{{ $loop->iteration }}</td>

            <td>
                <input type="number" name="details[{{ $index }}][qty]"
                       class="form-control qty"
                       value="{{ $detail->qty }}">
            </td>

            <td>
                <input type="text" name="details[{{ $index }}][satuan]"
                       class="form-control"
                       value="{{ $detail->satuan }}">
            </td>

            <td>
                <input type="text" name="details[{{ $index }}][uraian]"
                       class="form-control"
                       value="{{ $detail->uraian }}">
            </td>

            <td>
                <input type="number" name="details[{{ $index }}][harga]"
                       class="form-control harga"
                       value="{{ $detail->harga }}">
            </td>

            <td>
                <input type="text" name="details[{{ $index }}][keterangan]"
                       class="form-control"
                       value="{{ $detail->keterangan }}">
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm"
                        onclick="removeRow(this)">
                    Hapus
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


        </div>

        <div class="card-footer">
            <a href="{{ route('ppjb.index') }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-primary float-right">Update</button>
        </div>
    </form>
</div>
@stop
@section('js')
<script>
let rowIndex = {{ $item->details->count() }};

function addRow() {
    let table = document.getElementById('detailTable').getElementsByTagName('tbody')[0];

    let row = table.insertRow();
    row.innerHTML = `
        <td>${table.rows.length}</td>
        <td><input type="number" name="details[${rowIndex}][qty]" class="form-control"></td>
        <td><input type="text" name="details[${rowIndex}][satuan]" class="form-control"></td>
        <td><input type="text" name="details[${rowIndex}][uraian]" class="form-control"></td>
        <td><input type="number" name="details[${rowIndex}][harga]" class="form-control"></td>
        <td><input type="text" name="details[${rowIndex}][keterangan]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Hapus</button></td>
    `;

    rowIndex++;
}

function removeRow(button) {
    button.closest('tr').remove();
}
</script>
@stop

