@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@endsection

@section('title', 'Surat Instruksi Kerja')

@section('plugins.Datatables', true)

@section('plugins.Select2', true)


@section('content_header')
    <h1 style="text-align: center; color: blue;">{{ $workflowdata['projectname'] }}</h1>
@endsection

@section('content')
    <hr>
    <x-project-menu :workflowid="$workflow->workflowid" active="project" />

    <hr>
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

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA SIK</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Nomor SIK</label>
                            <div class="col-md-9">
                                <input type="text" name="no_sik" class="form-control" value="{{ $noSik }}"
                                    readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Tanggal SIK</label>
                            <div class="col-md-9">
                                <input type="date" name="tanggal_sik" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Contact Person</label>
                            <div class="col-md-9">
                                <input type="text" name="contact_person" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA INSPECTOR</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Data Inspector yang Ditugaskan</label>
                            <div class="col-md-9">
                                <select name="user_inspector" class="form-control" required>
                                    <option value="">-- Pilih Inspector --</option>
                                    @foreach ($namaInspector as $k)
                                        <option value="{{ $k->userid }}">
                                            {{ $k->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Jabatan</label>
                            <div class="col-md-9">
                                <select name="pilihan_jabatan_project" id="pilihan_jabatan_project" class="form-control"
                                    required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Leader">Leader</option>
                                    <option value="Anggota">Anggota</option>
                                    <option value="Teknisi">Teknisi</option>
                                    <option value="Safety Man">Safety Man</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row d-none" id="leader_wrapper">
                            <label class="col-md-3 col-form-label">Nama Leader</label>
                            <div class="col-md-9">
                                <select name="leader_userid" class="form-control">
                                    <option value="">-- Pilih Leader --</option>
                                    @foreach ($leaderUsers as $k)
                                        <option value="{{ $k->userid }}">
                                            {{ $k->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA PERALATAN</strong>
                    </div>

                    <div class="card-body" id="peralatan-wrapper">

                        {{-- ROW #1 --}}
                        <div class="form-group row peralatan-row">
                            <label class="col-md-3 col-form-label">Peralatan #1</label>

                            <div class="col-md-2">
                                <select name="peralatan[0][tipe_peralatan]" class="form-control" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="Peralatan">Peralatan</option>
                                    <option value="Instalasi">Instalasi</option>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <select name="peralatan[0][type_peralatan]" class="form-control">
                                    <option value="">-- Pilih Alat --</option>
                                    @foreach ($scopes as $k)
                                        <option value="{{ $k->id }}">
                                            {{ $k->tipe_nama }} - {{ $k->kategori_nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1">
                                <input type="number" name="peralatan[0][jumlah]" class="form-control" min="1"
                                    placeholder="Qty" required>
                            </div>

                            <div class="col-md-1 d-flex align-items-center">
                                <button type="button" class="btn btn-success btn-sm btnTambahPeralatan" title="Tambah">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Lokasi Kerja</label>
                            <div class="col-md-9">
                                <textarea name="location_job" id="location_job" class="form-control" rows="3"
                                    placeholder="Silahkan masukan Lokasi Kerja"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Area</label>
                            <div class="col-md-9">
                                <select name="area_sik" id="area_sik" class="form-control" required>
                                    <option value="">-- Pilih Area --</option>
                                    <option value="On-Shore">On-Shore</option>
                                    <option value="Off-Shore">Off-Shore</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Tanggal Mulai</label>
                            <div class="col-md-9">
                                <input type="date" name="date_start" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Tanggal Akhir</label>
                            <div class="col-md-9">
                                <input type="date" name="date_end" class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Durasi</label>
                            <div class="col-md-9">
                                <input type="text" name="durasi" class="form-control" readonly>
                            </div>
                        </div>

                    </div>
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

@section('js')
    <script>
        $(document).ready(function() {

            // =========================
            // TOGGLE LEADER
            // =========================
            function toggleLeaderField() {
                let jabatan = $('#pilihan_jabatan_project').val();

                if (jabatan === 'Anggota' || jabatan === 'Teknisi') {
                    $('#leader_wrapper').removeClass('d-none');
                } else {
                    $('#leader_wrapper').addClass('d-none');
                    $('#leader_wrapper select').val('');
                }
            }

            $('#pilihan_jabatan_project').on('change', toggleLeaderField);
            toggleLeaderField();

            // =========================
            // DATA PERALATAN (INLINE +)
            // =========================
            let peralatanIndex = 0;

            // TAMBAH ROW (klik tombol + di baris mana saja)
            $('#peralatan-wrapper').on('click', '.btnTambahPeralatan', function() {
                peralatanIndex++;

                let row = `
        <div class="form-group row peralatan-row">
            <label class="col-md-2 col-form-label">Peralatan #${peralatanIndex + 1}</label>

            <div class="col-md-2">
                <select name="peralatan[${peralatanIndex}][tipe_peralatan]"
                        class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Peralatan">Peralatan</option>
                    <option value="Instalasi">Instalasi</option>
                </select>
            </div>

            <div class="col-md-6">
                <select name="peralatan[${peralatanIndex}][type_peralatan]"
                        class="form-control">
                    <option value="">-- Pilih Alat --</option>
                    @foreach ($scopes as $k)
                        <option value="{{ $k->id }}">
                            {{ $k->tipe_nama }} - {{ $k->kategori_nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <input type="number"
                       name="peralatan[${peralatanIndex}][jumlah]"
                       class="form-control"
                       min="1"
                       placeholder="Qty"
                       required>
            </div>

            <div class="col-md-1 d-flex align-items-center gap-1">
                <button type="button"
                        class="btn btn-success btn-sm btnTambahPeralatan"
                        title="Tambah">
                    <i class="fas fa-plus"></i>
                </button>

                <button type="button"
                        class="btn btn-danger btn-sm btnHapusPeralatan"
                        title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        `;

                $('#peralatan-wrapper').append(row);
            });

            // HAPUS ROW
            $('#peralatan-wrapper').on('click', '.btnHapusPeralatan', function() {
                $(this).closest('.peralatan-row').remove();

                // re-number label
                $('#peralatan-wrapper .peralatan-row').each(function(i) {
                    $(this).find('label').text('Peralatan #' + (i + 1));
                });

                peralatanIndex = $('#peralatan-wrapper .peralatan-row').length - 1;
            });

        });
    </script>

    <script>
        $(document).ready(function() {

            // =========================
            // HITUNG DURASI OTOMATIS
            // =========================
            function hitungDurasi() {
                let start = $('input[name="date_start"]').val();
                let end = $('input[name="date_end"]').val();

                if (start && end) {
                    let startDate = new Date(start);
                    let endDate = new Date(end);

                    if (endDate >= startDate) {
                        // selisih hari (inklusif)
                        let diffTime = endDate - startDate;
                        let diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

                        $('input[name="durasi"]').val(diffDays + ' hari');
                    } else {
                        $('input[name="durasi"]').val('');
                    }
                } else {
                    $('input[name="durasi"]').val('');
                }
            }

            // trigger saat tanggal berubah
            $('input[name="date_start"], input[name="date_end"]').on('change', hitungDurasi);

        });
    </script>
@endsection
