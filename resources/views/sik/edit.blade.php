@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@endsection

@section('title', 'Surat Instruksi Kerja')

@section('plugins.Datatables', true)

@section('plugins.Select2', true)


@section('content_header')
    <h1 style="text-align: center; color: blue;">{{ $workflowdata1['projectname'] }}</h1>
@endsection

@section('content')
    <hr>
    <x-project-menu :workflowid="$parentWorkflow->workflowid" active="project" />

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

            <form action="{{ route('sik.update', ['projectId' => $parentWorkflow->workflowid, 'id' => $sik->workflowid]) }}"
                method="POST">

                @csrf
                @method('PUT')


                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>DATA SIK</strong>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Nomor SIK</label>
                            <div class="col-md-9">
                                <input type="text" name="no_sik" class="form-control"
                                    value="{{ $workflowdata['no_sik'] ?? '' }}" readonly>
                                <input type="hidden" name="nworkflowid" class="form-control"
                                    value="{{ $parentWorkflow->workflowid }}">
                                <input type="hidden" name="client" class="form-control"
                                    value="{{ $parentWorkflow->client }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Tanggal SIK</label>
                            <div class="col-md-9">
                                <input type="date" name="tanggal_sik" class="form-control"
                                    value="{{ $workflowdata['tanggal_sik'] ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Contact Person</label>
                            <div class="col-md-9">
                                <input type="text" name="contact_person" class="form-control"
                                    value="{{ $workflowdata['contact_person'] ?? '' }}">
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
                                        <option value="{{ $k->userid }}"
                                            {{ ($workflowdata['user_inspector'] ?? '') == $k->userid ? 'selected' : '' }}>
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

                                    @php $jabatan = $workflowdata['pilihan_jabatan_project'] ?? ''; @endphp

                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Leader" {{ $jabatan == 'Leader' ? 'selected' : '' }}>Leader</option>
                                    <option value="Anggota" {{ $jabatan == 'Anggota' ? 'selected' : '' }}>Anggota</option>
                                    <option value="Teknisi" {{ $jabatan == 'Teknisi' ? 'selected' : '' }}>Teknisi</option>
                                    <option value="Safety Man" {{ $jabatan == 'Safety Man' ? 'selected' : '' }}>Safety Man
                                    </option>
                                    <option value="Admin" {{ $jabatan == 'Admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row d-none" id="leader_wrapper">
                            <label class="col-md-3 col-form-label">Nama Leader</label>
                            <div class="col-md-9">
                                <select name="leadnya_pilihan_jabatan_project" class="form-control">
                                    <option value="">-- Pilih Leader --</option>
                                    @foreach ($leaderUsers as $k)
                                        <option value="{{ $k->userid }}"
                                            {{ ($workflowdata['leadnya_pilihan_jabatan_project'] ?? '') == $k->userid ? 'selected' : '' }}>
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

                    <div class="card-body">
                        <div id="peralatan-list">

                            @php
                                $peralatan = $workflowdata['peralatan'] ?? [];
                            @endphp

                            @if (count($peralatan) > 0)
                                @foreach ($peralatan as $index => $item)
                                    <div class="form-group row peralatan-row">
                                        <label class="col-md-3 col-form-label">
                                            Peralatan #{{ $index + 1 }}
                                        </label>

                                        <div class="col-md-2">
                                            <select name="peralatan[{{ $index }}][tipe_peralatan]"
                                                class="form-control" required>

                                                <option value="">-- Pilih Tipe --</option>
                                                <option value="Peralatan"
                                                    {{ $item['tipe_peralatan'] == 'Peralatan' ? 'selected' : '' }}>
                                                    Peralatan
                                                </option>
                                                <option value="Instalasi"
                                                    {{ $item['tipe_peralatan'] == 'Instalasi' ? 'selected' : '' }}>
                                                    Instalasi
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-5">
                                            <select name="peralatan[{{ $index }}][type_peralatan]"
                                                class="form-control">
                                                <option value="">-- Pilih Alat --</option>
                                                @foreach ($scopes as $k)
                                                    <option value="{{ $k->id }}"
                                                        {{ $item['type_peralatan'] == $k->id ? 'selected' : '' }}>
                                                        {{ $k->tipe_nama }} - {{ $k->kategori_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-1">
                                            <input type="number" name="peralatan[{{ $index }}][jumlah]"
                                                class="form-control" min="1" value="{{ $item['jumlah'] }}" required>
                                        </div>

                                        <div class="col-md-1 d-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-success btn-sm btnTambahPeralatan">
                                                <i class="fas fa-plus"></i>
                                            </button>

                                            <button type="button" class="btn btn-danger btn-sm btnHapusPeralatan">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- fallback kosong --}}
                            @endif

                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Lokasi Kerja</label>
                            <div class="col-md-9">
                                <textarea name="location_job" class="form-control" rows="3">{{ $workflowdata['location_job'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Area</label>
                            <div class="col-md-9">
                                <select name="area_sik" class="form-control" required>
                                    <option value="">-- Pilih Area --</option>
                                    <option value="On-Shore"
                                        {{ ($workflowdata['area_sik'] ?? '') == 'On-Shore' ? 'selected' : '' }}>
                                        On-Shore
                                    </option>
                                    <option value="Off-Shore"
                                        {{ ($workflowdata['area_sik'] ?? '') == 'Off-Shore' ? 'selected' : '' }}>
                                        Off-Shore
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Tanggal Mulai</label>
                            <div class="col-md-9">
                                <input type="date" name="date_start" class="form-control"
                                    value="{{ $workflowdata['date_start'] ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Tanggal Akhir</label>
                            <div class="col-md-9">
                                <input type="date" name="date_end" class="form-control"
                                    value="{{ $workflowdata['date_end'] ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Durasi</label>
                            <div class="col-md-9">
                                <input type="text" name="durasi" class="form-control" readonly
                                    value="{{ $workflowdata['durasi'] ?? '' }}">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>PERSIAPAN INSPEKSI</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Review Dokumen Awal</label>
                            <div class="col-md-9">
                                @php $persiapan = $workflowdata['persiapan'] ?? []; @endphp

                                <select name="peri1" class="form-control" required>
                                    <option value="Yes" {{ ($persiapan['peri1'] ?? '') == 'Yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="No" {{ ($persiapan['peri1'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Penyusunan PPBJ</label>
                            <div class="col-md-9">
                                <select name="peri2" class="form-control" required>
                                    <option value="Yes" {{ ($persiapan['peri2'] ?? '') == 'Yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="No" {{ ($persiapan['peri2'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Perizinan Kerja</label>
                            <div class="col-md-9">
                                <select name="peri3" class="form-control" required>
                                    <option value="Yes" {{ ($persiapan['peri3'] ?? '') == 'Yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="No" {{ ($persiapan['peri3'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Persiapan Peralatan Inspeksi</label>
                            <div class="col-md-9">
                                <select name="peri4" class="form-control" required>
                                    <option value="Yes" {{ ($persiapan['peri4'] ?? '') == 'Yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="No" {{ ($persiapan['peri4'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>PEMERIKSAAN LAPANGAN</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Pre-Inspeksi Meeting dan Compile Dokumen</label>
                            <div class="col-md-9">
                                @php $lapangan = $workflowdata['lapangan'] ?? []; @endphp

                                <select name="pl1" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl1'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl1'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Verifikasi Dokumen Teknis</label>
                            <div class="col-md-9">
                                <select name="pl2" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl2'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl2'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Verifikasi Material / Alat</label>
                            <div class="col-md-9">
                                <select name="pl3" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl3'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl3'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Inspeksi Pabrikasi / Instalasi</label>
                            <div class="col-md-9">
                                <select name="pl4" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl4'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl4'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Pengujian Fungsi</label>
                            <div class="col-md-9">
                                <select name="pl5" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl5'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl5'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Pengisian Form Inspeksi</label>
                            <div class="col-md-9">
                                <select name="pl6" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl6'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl6'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Laporan Awal</label>
                            <div class="col-md-9">
                                <select name="pl7" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl7'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl7'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Closing Meeting / BASTP</label>
                            <div class="col-md-9">
                                <select name="pl8" class="form-control" required>
                                    <option value="Yes" {{ ($lapangan['pl8'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($lapangan['pl8'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>PELAPORAN INSPEKSI</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Sistematika Pelaporan</label>
                            <div class="col-md-9">
                                @php $pelaporan = $workflowdata['pelaporan'] ?? []; @endphp

                                <select name="si1" class="form-control" required>
                                    <option value="Yes" {{ ($pelaporan['si1'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($pelaporan['si1'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Pemindahan Data Lapangan / Upload</label>
                            <div class="col-md-9">
                                <select name="si2" class="form-control" required>
                                    <option value="Yes" {{ ($pelaporan['si2'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($pelaporan['si2'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Design Appraisal / Perhitungan</label>
                            <div class="col-md-9">
                                <select name="si3" class="form-control" required>
                                    <option value="Yes" {{ ($pelaporan['si3'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($pelaporan['si3'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Report of Inspection</label>
                            <div class="col-md-9">
                                <select name="si4" class="form-control" required>
                                    <option value="Yes" {{ ($pelaporan['si4'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($pelaporan['si4'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Draft Sertifikat (COI / PLO MIGAS)</label>
                            <div class="col-md-9">
                                <select name="si5" class="form-control" required>
                                    <option value="Yes" {{ ($pelaporan['si5'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($pelaporan['si5'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>PENGURUSAN MIGAS</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Penandatanganan Surat-Surat Klien ke MIGAS</label>
                            <div class="col-md-9">
                                @php $migas = $workflowdata['migas'] ?? []; @endphp

                                <select name="pm1" class="form-control" required>
                                    <option value="Yes" {{ ($migas['pm1'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($migas['pm1'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Approval Konseptor / Inspektor / Kasie /
                                Kasubdit</label>
                            <div class="col-md-9">
                                <select name="pm2" class="form-control" required>
                                    <option value="Yes" {{ ($migas['pm2'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($migas['pm2'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Approval Direktur Migas</label>
                            <div class="col-md-9">
                                <select name="pm3" class="form-control" required>
                                    <option value="Yes" {{ ($migas['pm3'] ?? '') == 'Yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="No" {{ ($migas['pm3'] ?? '') == 'No' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>CATATAN SIK</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Catatan</label>
                            <div class="col-md-9">
                                <textarea name="catatan_sik" id="catatan_sik" class="form-control" rows="3"
                                    placeholder="Silahkan masukan Catatan">{{ $workflowdata['catatan_sik'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mb-4">
                    <div class="card-header py-2">
                        <strong>SURAT TUGAS</strong>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Upload Surat Tugas <small style="color: red;">(Jika
                                    Ada)</small></label>
                            <div class="col-md-9">
                                <input type="file" name="surat_tugas[]" class="form-control" multiple>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('project_list.sik', $parentWorkflow->workflowid) }}" class="btn btn-secondary">
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

            function resetFormData() {

                // Reset lokasi & tanggal
                $('textarea[name="location_job"]').val('');
                $('select[name="area_sik"]').val('');
                $('input[name="date_start"]').val('');
                $('input[name="date_end"]').val('');
                $('input[name="durasi"]').val('');

                // Reset checklist
                $('select[name^="peri"]').val('Yes');
                $('select[name^="pl"]').val('Yes');
                $('select[name^="si"]').val('Yes');
                $('select[name^="pm"]').val('Yes');

                // Reset peralatan
                $('#peralatan-list').html(`
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
            <input type="number"
                   name="peralatan[0][jumlah]"
                   class="form-control"
                   min="1"
                   placeholder="Qty"
                   required>
        </div>

        <div class="col-md-1 d-flex align-items-center">
            <button type="button"
                    class="btn btn-success btn-sm btnTambahPeralatan">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
`);

                peralatanIndex = 0;
            }


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
            $('#peralatan-list').on('click', '.btnTambahPeralatan', function() {
                peralatanIndex++;

                let row = `
        <div class="form-group row peralatan-row">
            <label class="col-md-3 col-form-label">Peralatan #${peralatanIndex + 1}</label>

            <div class="col-md-2">
                <select name="peralatan[${peralatanIndex}][tipe_peralatan]"
                        class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Peralatan">Peralatan</option>
                    <option value="Instalasi">Instalasi</option>
                </select>
            </div>

            <div class="col-md-5">
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

                $('#peralatan-list').append(row);
            });

            // HAPUS ROW
            $('#peralatan-list').on('click', '.btnHapusPeralatan', function() {
                $(this).closest('.peralatan-row').remove();

                // re-number label
                $('#peralatan-list .peralatan-row').each(function(i) {
                    $(this).find('label').text('Peralatan #' + (i + 1));
                });

                peralatanIndex = $('#peralatan-list .peralatan-row').length - 1;
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

        $(document).on('change', 'select[name="leadnya_pilihan_jabatan_project"]', function() {

            let leaderId = $(this).val();
            let workflowId = $('input[name="nworkflowid"]').val();

            if (!leaderId) return;

            $.get(`/sik/get-leader-data/${workflowId}/${leaderId}`, function(data) {

                if (!data) return;

                // =========================
                // AUTO COPY DATA
                // =========================
                $('textarea[name="location_job"]').val(data.location_job);
                $('select[name="area_sik"]').val(data.area_sik);
                $('input[name="date_start"]').val(data.date_start);
                $('input[name="date_end"]').val(data.date_end);
                $('input[name="durasi"]').val(data.durasi);

                if (data.persiapan) {
                    $('select[name="peri1"]').val(data.persiapan.peri1);
                    $('select[name="peri2"]').val(data.persiapan.peri2);
                    $('select[name="peri3"]').val(data.persiapan.peri3);
                    $('select[name="peri4"]').val(data.persiapan.peri4);
                }

                if (data.lapangan) {
                    for (let i = 1; i <= 8; i++) {
                        $(`select[name="pl${i}"]`).val(data.lapangan[`pl${i}`]);
                    }
                }

                if (data.pelaporan) {
                    for (let i = 1; i <= 5; i++) {
                        $(`select[name="si${i}"]`).val(data.pelaporan[`si${i}`]);
                    }
                }

                if (data.migas) {
                    for (let i = 1; i <= 3; i++) {
                        $(`select[name="pm${i}"]`).val(data.migas[`pm${i}`]);
                    }
                }

                // =========================
                // COPY PERALATAN
                // =========================
                if (data.peralatan && data.peralatan.length > 0) {

                    $('#peralatan-list .peralatan-row').remove();

                    let index = 0;

                    data.peralatan.forEach(function(item) {

                        let row = `
            <div class="form-group row peralatan-row">
                <label class="col-md-3 col-form-label">
                    Peralatan #${index + 1}
                </label>

                <div class="col-md-2">
                    <select name="peralatan[${index}][tipe_peralatan]"
                            class="form-control" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="Peralatan" ${item.tipe_peralatan === 'Peralatan' ? 'selected' : ''}>Peralatan</option>
                        <option value="Instalasi" ${item.tipe_peralatan === 'Instalasi' ? 'selected' : ''}>Instalasi</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <select name="peralatan[${index}][type_peralatan]"
                            class="form-control">
                        <option value="">-- Pilih Alat --</option>
                        @foreach ($scopes as $k)
                            <option value="{{ $k->id }}"
                                ${item.type_peralatan == "{{ $k->id }}" ? 'selected' : ''}>
                                {{ $k->tipe_nama }} - {{ $k->kategori_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-1">
                    <input type="number"
                           name="peralatan[${index}][jumlah]"
                           class="form-control"
                           min="1"
                           value="${item.jumlah}"
                           required>
                </div>

                <div class="col-md-1 d-flex align-items-center">
                    <button type="button"
                            class="btn btn-success btn-sm btnTambahPeralatan">
                        <i class="fas fa-plus"></i>
                    </button>

                    <button type="button"
                            class="btn btn-danger btn-sm btnHapusPeralatan">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            `;

                        $('#peralatan-list').append(row);

                        index++;
                    });

                    peralatanIndex = data.peralatan.length - 1;
                }

            });

        });
    </script>
@endsection
