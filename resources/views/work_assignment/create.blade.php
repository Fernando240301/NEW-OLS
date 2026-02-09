@extends('adminlte::page')

@section('title', 'Tambah Work Assignment')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Tambah Work Assignment</h1>
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

            <form action="{{ route('work_assignment.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Project Type</label>
                    <select name="project_type" id="project_type" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="PR">PR</option>
                        <option value="FR">FR</option>
                        <option value="NP">NP</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Project Name</label>
                    <input type="text" name="projectname" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Client Name</label>
                    <select name="client" class="form-control select2bs4" required>
                        <option value="">-- Pilih Client --</option>
                        @foreach ($namaclient as $k)
                            <option value="{{ $k->pemohonid }}">
                                {{ $k->nama_perusahaan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Contract Number</label>
                    <input type="text" name="no_kontrak" class="form-control">
                </div>

                <div class="form-group">
                    <label>Issued Contract</label>
                    <input type="date" name="tanggal_kontrak" class="form-control">
                </div>

                <div class="form-group">
                    <label>Expired Contract</label>
                    <input type="date" name="tanggal_akhir_kerja" class="form-control">
                </div>

                <div class="form-group">
                    <label>Contract Price</label>
                    <input type="text" name="harga_kontrak" id="harga_kontrak" class="form-control" placeholder="0">
                </div>

                <div class="form-group">
                    <label for="alamat_perusahaan">Office Address</label>
                    <textarea name="lokasi_kantor" id="lokasi_kantor" class="form-control" rows="3"
                        placeholder="Silahkan masukan Alamat Kantor"></textarea>
                </div>

                {{-- <div class="form-group">
                    <label for="alamat_perusahaan">Site Address</label>
                    <textarea name="lokasi_lapangan" id="lokasi_lapangan" class="form-control" rows="3"
                        placeholder="Silahkan masukan Alamat Site"></textarea>
                </div> --}}

                <div class="form-group">
                    <label>Contact Person Client</label>
                    <input type="text" name="contact_person" class="form-control">
                </div>

                <div class="form-group">
                    <label>Number Phone Client</label>
                    <input type="text" name="no_hp" class="form-control">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="text" name="email" class="form-control">
                </div>

                <div class="form-group">
                    <label>Contact Person SA/SE/SR</label>
                    <input type="text" name="contact_person1" class="form-control">
                </div>

                <div class="form-group">
                    <label>Number Phone SA/SE/SR</label>
                    <input type="text" name="no_hp1" class="form-control">
                </div>

                <div class="form-group">
                    <label>Lokasi Pengujian PSV</label>
                    <input type="text" name="lokasiujipsv" class="form-control">
                </div>

                <div class="form-group">
                    <label for="pidp">Termin Pembayaran</label>
                    <textarea name="pidp" id="pidp" class="form-control" rows="3"
                        placeholder="Silahkan masukan Termin Pembayaran"></textarea>
                </div>

                <hr>
                <b>CONDITION</b>

                <div class="form-group">
                    <label>Mob Demob</label>
                    <select name="mobdemob" id="mobdemob" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT">MIT</option>
                        <option value="Client">Client</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Akomodasi</label>
                    <select name="akomodasi" id="akomodasi" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT">MIT</option>
                        <option value="Client">Client</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lokal Transport</label>
                    <select name="lokaltransport" id="lokaltransport" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT">MIT</option>
                        <option value="Client">Client</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Meals</label>
                    <select name="meals" id="meals" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="MIT">MIT</option>
                        <option value="Client">Client</option>
                    </select>
                </div>

                <hr>
                <b>LAMPIRAN PENDAMPING INVOICE</b>

                <div class="form-group">
                    <label>Invoice Asli</label>
                    <select name="invoiceasli" id="invoiceasli" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>BAST/BASTP</label>
                    <select name="bastp" id="bastp" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Payment Approval</label>
                    <select name="paymentapproval" id="paymentapproval" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Copy Lampiran C</label>
                    <select name="copylampiranc" id="copylampiranc" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Copy Lampiran D</label>
                    <select name="copylampirand" id="copylampirand" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>E-Faktur</label>
                    <select name="efaktur" id="efaktur" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>E-Nova</label>
                    <select name="enova" id="enova" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Performance Bond</label>
                    <select name="performancebond" id="performancebond" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lampiran HSE</label>
                    <select name="lampiranhse" id="lampiranhse" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Tidak">Tidak</option>
                        <option value="Iya">Iya</option>
                    </select>
                </div>

                <hr>
                <div class="form-group">
                    <label>Document Contract</label>
                    <input type="file" name="files[]" class="form-control" multiple>
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

@push('js')
    <script>
        $(document).ready(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Client --',
                width: '100%', // ⬅️ INI KUNCI UTAMA
                minimumResultsForSearch: 0
            });

            const rupiahInput = document.getElementById('harga_kontrak');
            if (rupiahInput) {
                rupiahInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                });
            }
        });
    </script>
@endpush


@section('css')
    <style>
        .select2-container--bootstrap4 .select2-selection {
            height: calc(2.25rem + 2px);
            border: 1px solid #ced4da;
            border-radius: .25rem;
        }

        .select2-container--bootstrap4 .select2-selection__rendered {
            line-height: 2.25rem;
        }

        .select2-container--bootstrap4 .select2-selection__arrow {
            height: calc(2.25rem + 2px);
        }

        /* Pastikan Select2 ngikut form-control */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--bootstrap4 .select2-selection {
            height: calc(2.25rem + 2px);
            border: 1px solid #ced4da;
            border-radius: .25rem;
            width: 100%;
        }
    </style>
@endsection
