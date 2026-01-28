@extends('adminlte::page')

@section('title', 'Edit Client')

@section('content_header')
    <h1>Edit Client</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('client.update', $client->pemohonid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" class="form-control"
                        value="{{ old('nama_perusahaan', $client->nama_perusahaan) }}" required>
                </div>

                <div class="form-group">
                    <label>Klasifikasi</label>
                    <select name="klasifikasi" class="form-control" required>
                        @foreach ($klasifikasi as $k)
                            <option value="{{ $k->id }}" {{ $client->klasifikasi == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_pemohon" class="form-control"
                        value="{{ old('email_pemohon', $client->email_pemohon) }}" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat_perusahaan" class="form-control" rows="3">{{ old('alamat_perusahaan', $client->alamat_perusahaan) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="kota_perusahaan" class="form-control"
                        value="{{ old('kota_perusahaan', $client->kota_perusahaan) }}">
                </div>

                <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" name="provinsi_perusahaan" class="form-control"
                        value="{{ old('provinsi_perusahaan', $client->provinsi_perusahaan) }}">
                </div>

                <div class="form-group">
                    <label>Negara</label>
                    <input type="text" name="negara" class="form-control" value="{{ old('negara', $client->negara) }}">
                </div>

                <div class="form-group">
                    <label>Kode Pos</label>
                    <input type="number" name="kode_pos" class="form-control"
                        value="{{ old('kode_pos', $client->kode_pos) }}">
                </div>

                <div class="form-group">
                    <label>No. Telephone Perusahaan</label>
                    <input type="text" name="telp_perusahaan" class="form-control"
                        value="{{ old('telp_perusahaan', $client->telp_perusahaan) }}">
                </div>

                <div class="form-group">
                    <label>Kontak Person #1</label>
                    <input type="text" name="contact1" class="form-control"
                        value="{{ old('contact1', $client->contact1) }}">
                </div>

                <div class="form-group">
                    <label>No. Kontak Person #1</label>
                    <input type="text" name="contact_celluler1" class="form-control"
                        value="{{ old('contact_celluler1', $client->contact_celluler1) }}">
                </div>

                <div class="form-group">
                    <label>Kontak Person #2</label>
                    <input type="text" name="contact2" class="form-control"
                        value="{{ old('contact2', $client->contact2) }}">
                </div>

                <div class="form-group">
                    <label>No. Kontak Person #2</label>
                    <input type="text" name="contact_celluler2" class="form-control"
                        value="{{ old('contact_celluler2', $client->contact_celluler2) }}">
                </div>

                <div class="form-group">
                    <label>Kontak Person #3</label>
                    <input type="text" name="contact3" class="form-control"
                        value="{{ old('contact3', $client->contact3) }}">
                </div>

                <div class="form-group">
                    <label>No. Kontak Person #3</label>
                    <input type="text" name="contact_celluler3" class="form-control"
                        value="{{ old('contact_celluler3', $client->contact_celluler3) }}">
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
