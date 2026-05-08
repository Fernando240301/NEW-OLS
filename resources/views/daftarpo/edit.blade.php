@extends('adminlte::page')

@section('title', 'Edit PO')

@section('content_header')
    <h1>Edit Purchase Order</h1>
@endsection

@section('content')

<form action="{{ route('daftarpo.update', $po->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-body">

            {{-- No PO --}}
            <div class="form-group">
                <label>No. PO</label>
                <input type="text" name="no_po" class="form-control" value="{{ $po->no_po }}" readonly>
            </div>

            <!-- {{-- Nomor tambahan (463) --}}
            <div class="form-group">
                <input type="text" name="no_urut" class="form-control" value="{{ $po->no_urut }}" readonly>
            </div> -->

            {{-- Nama Pengaju --}}
            <div class="form-group">
    <label>Nama Pengaju</label>
    <select name="nama_pengaju" class="form-control" required>
        <option value="">-- Pilih Nama Pengaju --</option>

        <option value="Dea Popirawati" 
            {{ (isset($po) && $po->nama_pengaju == 'Dea Popirawati') ? 'selected' : '' }}>
            Dea Popirawati
        </option>

        <option value="Rony Dwi Cahyono" 
            {{ (isset($po) && $po->nama_pengaju == 'Rony Dwi Cahyono') ? 'selected' : '' }}>
            Rony Dwi Cahyono
        </option>

        <option value="Maulina" 
            {{ (isset($po) && $po->nama_pengaju == 'Maulina') ? 'selected' : '' }}>
            Maulina
        </option>

        <option value="Beiby Septi Arynsa" 
            {{ (isset($po) && $po->nama_pengaju == 'Beiby Septi Arynsa') ? 'selected' : '' }}>
            Beiby Septi Arynsa
        </option>
    </select>
</div>

            {{-- To --}}
            <div class="form-group">
                <label>To</label>
                <input type="text" name="tujuan" class="form-control" value="{{ $po->tujuan }}">
            </div>

            {{-- Address --}}
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3">{{ $po->address }}</textarea>
            </div>

            {{-- Date --}}
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="tanggal" class="form-control"
                       value="{{ \Carbon\Carbon::parse($po->tanggal)->format('Y-m-d') }}">
            </div>

            {{-- Attention --}}
            <div class="form-group">
                <label>Attention</label>
                <input type="text" name="attention" class="form-control"
                       value="{{ $po->attention }}" placeholder="Masukkan Attention">
            </div>

            {{-- Ship Date --}}
            <div class="form-group">
                <label>Ship Date</label>
                <input type="date" name="ship_date" class="form-control"
                       value="{{ $po->ship_date ? \Carbon\Carbon::parse($po->ship_date)->format('Y-m-d') : '' }}">
            </div>

            {{-- File --}}
            <div class="form-group">
                <label>File</label>
                <input type="file" name="file" class="form-control">
                @if($po->file)
                    <small class="text-muted">File sekarang: {{ $po->file }}</small>
                @endif
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success">SIMPAN</button>
            <button type="reset" class="btn btn-warning">RESET</button>
        </div>
    </div>

</form>

@endsection