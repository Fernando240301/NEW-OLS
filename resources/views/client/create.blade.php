@extends('adminlte::page')

@section('title', 'Tambah Client')

@section('content_header')
    <h1>Tambah Client</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('client.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Klasifikasi</label>
                    <select name="klasifikasi" class="form-control" required>
                        <option value="">-- Pilih Klasifikasi --</option>
                        @foreach ($klasifikasi as $k)
                            <option value="{{ $k->id }}">
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email_pemohon" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="alamat_perusahaan">Alamat</label>
                    <textarea name="alamat_perusahaan" id="alamat_perusahaan" class="form-control" rows="3"
                        placeholder="Silahkan masukan Alamat Client"></textarea>
                </div>

                <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="kota_perusahaan" class="form-control">
                </div>

                <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" name="provinsi_perusahaan" class="form-control">
                </div>

                <div class="form-group">
                    <label>Negara</label>
                    <input type="text" name="negara" class="form-control">
                </div>

                <div class="form-group">
                    <label>Kode Pos</label>
                    <input type="number" name="kode_pos" class="form-control">
                </div>

                <div class="form-group">
                    <label>No. Telephone Perusahaan</label>
                    <input type="text" name="telp_perusahaan" class="form-control">
                </div>

                <div class="form-group">
                    <label>Kontak Person #1</label>
                    <input type="text" name="contact1" class="form-control">
                </div>

                <div class="form-group">
                    <label>No. Kontak Person #1</label>
                    <input type="text" name="contact_celluler1" class="form-control">
                </div>

                <div class="form-group">
                    <label>Kontak Person #2</label>
                    <input type="text" name="contact2" class="form-control">
                </div>

                <div class="form-group">
                    <label>No. Kontak Person #2</label>
                    <input type="text" name="contact_celluler2" class="form-control">
                </div>

                <div class="form-group">
                    <label>Kontak Person #3</label>
                    <input type="text" name="contact3" class="form-control">
                </div>

                <div class="form-group">
                    <label>No. Kontak Person #3</label>
                    <input type="text" name="contact_celluler3" class="form-control">
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
