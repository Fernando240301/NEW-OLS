@extends('adminlte::page')

@section('title', 'Project List')

@section('plugins.Datatables', true)

@section('plugins.Select2', true)


@section('content_header')
    <h1 style="text-align: center; color: blue;">{{ $workflowdata['projectname'] }}</h1>
@endsection

@section('content')
    <hr>
    <x-project-menu :workflowid="$app_workflow->workflowid" active="project" />

    <hr>
    <div class="card">

        <div class="card card-outline card-primary mb-4">
            <div class="card-header py-2">
                <strong>DATA KONTRAK KERJA</strong>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Project Number</label>
                    <div class="col-md-9">
                        <input type="text" name="project_number" class="form-control"
                            value="{{ old('project_number', $workflowdata['project_number']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Client Name</label>
                    <div class="col-md-9">
                        <select class="form-control select2bs4" disabled>
                            @foreach ($namaclient as $k)
                                <option value="{{ $k->pemohonid }}"
                                    {{ $app_workflow->client == $k->pemohonid ? 'selected' : '' }}>
                                    {{ $k->nama_perusahaan }}
                                </option>
                            @endforeach
                        </select>

                        {{-- hidden supaya tetap terkirim --}}
                        <input type="hidden" name="client" value="{{ $app_workflow->client }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Project Name</label>
                    <div class="col-md-9">
                        <input type="text" name="projectname" class="form-control"
                            value="{{ old('projectname', $app_workflow->projectname) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Issued Contract</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ old('tanggal_kontrak', date('d F Y', strtotime($workflowdata['tanggal_kontrak']))) }}"
                            readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Expired Contract</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ old('tanggal_akhir', date('d F Y', strtotime($workflowdata['tanggal_akhir']))) }}"
                            readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Scope of Work</label>
                    <div class="col-md-9">
                        <button type="button" class="btn btn-primary btnScope"
                            data-workflowid="{{ $app_workflow->workflowid }}">
                            <i class="fas fa-list"></i> Lihat Data
                        </button>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Contact Person Client</label>
                    <div class="col-md-9">
                        <input type="text" name="contact_person" class="form-control"
                            value="{{ old('contact_person', $workflowdata['contact_person']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Client Mobile Number</label>
                    <div class="col-md-9">
                        <input type="text" name="no_hp" class="form-control"
                            value="{{ old('no_hp', $workflowdata['no_hp']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Client Email</label>
                    <div class="col-md-9">
                        <input type="text" name="email" class="form-control"
                            value="{{ old('email', $workflowdata['email']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">SA/SE/SR Contact Person</label>
                    <div class="col-md-9">
                        <input type="text" name="contact_person1" class="form-control"
                            value="{{ old('contact_person1', $workflowdata['contact_person1']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">SA/SE/SR Mobile Number</label>
                    <div class="col-md-9">
                        <input type="text" name="no_hp1" class="form-control"
                            value="{{ old('no_hp1', $workflowdata['no_hp1']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Office Address</label>
                    <div class="col-md-9">
                        <input type="text" name="lokasi_kantor" class="form-control"
                            value="{{ old('lokasi_kantor', $workflowdata['lokasi_kantor']) }}" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Site Address</label>
                    <div class="col-md-9">
                        <input type="text" name="lokasi_kantor" class="form-control" value="{{ $siteAddressText }}"
                            readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary mb-4">
            <div class="card-header py-2">
                <strong>Nilai Proyek</strong>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Project Value</label>
                    <div class="col-md-9">
                        <input type="text" name="harga_kontrak" class="form-control"
                            value="{{ old('harga_kontrak', $workflowdata['harga_kontrak']) }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary mb-4">
            <div class="card-header py-2">
                <strong>Term and Condition</strong>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Term and Condition</label>
                    <div class="col-md-9">
                        <textarea name="pidp" class="form-control form-control-sm textarea-readonly" rows="4" readonly>{{ old('pidp', $workflowdata['pidp'] ?? '-') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary mb-4 card-marketing">
            <div class="card-header py-2">
                <strong>Syarat Administrasi</strong>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Work Assignment</label>

                    <div class="col-md-9">
                        <div class="file-preview-list">
                            <div class="file-preview">
                                <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                <div class="file-preview-name">
                                    Work Assignment
                                </div>
                                <a href="{{ route('work_assignment.pdf', $app_workflow->workflowid) }}" target="_blank"
                                    class="btn btn-xs btn-primary mt-2">
                                    Buka
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kontrak Kerja</label>

                    <div class="col-md-9">
                        <div class="file-preview-list">
                            @foreach ($workflowdata['lampiran_kontrak'] as $file)
                                <div class="file-preview">
                                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                    <div class="file-preview-name">
                                        {{ Str::limit($file, 30) }}
                                    </div>
                                    <a href="{{ route('kontrak.view', $file) }}" target="_blank"
                                        class="btn btn-xs btn-primary mt-2">
                                        Buka
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $dokumenHse = $workflowdata['dokumen_hse'] ?? [];
            $dokumenSimlok = $workflowdata['dokumen_simlok'] ?? [];
            $dokumenSika = $workflowdata['dokumen_sika'] ?? [];
            $dokumenPja = $workflowdata['dokumen_pja'] ?? [];
            $dokumenLainnya = $workflowdata['dokumen_lainnya'] ?? [];
        @endphp

        <div class="card card-outline card-primary mb-4 card-marketing">
            <div class="card-header py-2">
                <strong>Data Marketing</strong>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        Dokumen HSE Plan
                    </label>

                    <div class="col-md-7">
                        @if (count($dokumenHse))
                            <div class="file-preview-list">
                                @foreach ($dokumenHse as $file)
                                    <div class="file-preview">
                                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>

                                        <div class="file-preview-name">
                                            {{ Str::limit($file, 30) }}
                                        </div>

                                        <a href="{{ route('kontrak.view', $file) }}" target="_blank"
                                            class="btn btn-xs btn-primary mt-2">
                                            Buka
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="Belum ada file"
                                readonly>
                        @endif

                    </div>

                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-sm btn-outline-info btn-block btnUpload"
                            data-type="dokumen_hse" data-title="Upload Dokumen HSE Plan">
                            <i class="fas fa-upload"></i> Update Files
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        Dokumen Personil Untuk SIMLOK<br>(SKCK;Narkoba;Covid;Sertifikat)
                    </label>

                    <div class="col-md-7">
                        @if (count($dokumenSimlok))
                            <div class="file-preview-list">
                                @foreach ($dokumenSimlok as $file)
                                    <div class="file-preview">
                                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>

                                        <div class="file-preview-name">
                                            {{ Str::limit($file, 30) }}
                                        </div>

                                        <a href="{{ route('kontrak.view', $file) }}" target="_blank"
                                            class="btn btn-xs btn-primary mt-2">
                                            Buka
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="Belum ada file"
                                readonly>
                        @endif

                    </div>

                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-sm btn-outline-info btn-block btnUpload"
                            data-type="dokumen_simlok" data-title="Upload Dokumen SIMLOK">
                            <i class="fas fa-upload"></i> Update Files
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        Dokumen SIKA
                    </label>

                    <div class="col-md-7">
                        @if (count($dokumenSika))
                            <div class="file-preview-list">
                                @foreach ($dokumenSika as $file)
                                    <div class="file-preview">
                                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>

                                        <div class="file-preview-name">
                                            {{ Str::limit($file, 30) }}
                                        </div>

                                        <a href="{{ route('kontrak.view', $file) }}" target="_blank"
                                            class="btn btn-xs btn-primary mt-2">
                                            Buka
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="Belum ada file"
                                readonly>
                        @endif

                    </div>

                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-sm btn-outline-info btn-block btnUpload"
                            data-type="dokumen_sika" data-title="Upload Dokumen SIKA">
                            <i class="fas fa-upload"></i> Update Files
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        Dokumen PJA
                    </label>

                    <div class="col-md-7">
                        @if (count($dokumenPja))
                            <div class="file-preview-list">
                                @foreach ($dokumenPja as $file)
                                    <div class="file-preview">
                                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>

                                        <div class="file-preview-name">
                                            {{ Str::limit($file, 30) }}
                                        </div>

                                        <a href="{{ route('kontrak.view', $file) }}" target="_blank"
                                            class="btn btn-xs btn-primary mt-2">
                                            Buka
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="Belum ada file"
                                readonly>
                        @endif

                    </div>

                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-sm btn-outline-info btn-block btnUpload"
                            data-type="dokumen_pja" data-title="Upload Dokumen PJA">
                            <i class="fas fa-upload"></i> Update Files
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        Dokumen Lainnya
                    </label>

                    <div class="col-md-7">
                        @if (count($dokumenLainnya))
                            <div class="file-preview-list">
                                @foreach ($dokumenLainnya as $file)
                                    <div class="file-preview">
                                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>

                                        <div class="file-preview-name">
                                            {{ Str::limit($file, 30) }}
                                        </div>

                                        <a href="{{ route('kontrak.view', $file) }}" target="_blank"
                                            class="btn btn-xs btn-primary mt-2">
                                            Buka
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <input type="text" class="form-control form-control-sm bg-light" value="Belum ada file"
                                readonly>
                        @endif

                    </div>

                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-sm btn-outline-info btn-block btnUpload"
                            data-type="dokumen_lainnya" data-title="Upload Dokumen Lainnya">
                            <i class="fas fa-upload"></i> Update Files
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- MODAL SCOPE OF WORK --}}
    <div class="modal fade" id="scopeModal" tabindex="-1" aria-labelledby="scopeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="scopeModalLabel">
                        <i class="fas fa-tasks mr-1"></i> Scope of Work
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form action="{{ route('work_assignment.scope.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="workflowid" id="workflowid">

                    {{-- BODY --}}
                    <div class="modal-body">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" id="scopeTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="20%">Lokasi</th>
                                        <th width="20%">Jenis</th>
                                        <th width="15%">Tipe</th>
                                        <th width="15%">Kategori</th>
                                        <th width="10%" class="text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- -diisi via Ajax --}}
                                </tbody>
                            </table>
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer">
                        {{-- <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button> --}}
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('project_list.file.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header bg-info">
                        <h5 class="modal-title" id="uploadModalTitle">Upload File</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="workflowid" value="{{ $app_workflow->workflowid }}">
                        <input type="hidden" name="type" id="uploadType">

                        <div class="form-group">
                            <label>Pilih File</label>
                            <input type="file" name="files[]" class="form-control" multiple required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-info">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            $('#clientTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '.btnScope', function() {
            let workflowid = $(this).data('workflowid');

            $('#workflowid').val(workflowid);
            $('#scopeTable tbody').html(`
            <tr>
                <td colspan="6" class="text-center">Loading...</td>
            </tr>
        `);

            $.get("{{ route('project_list.scope.get') }}", {
                workflowid: workflowid
            }, function(res) {
                $('#scopeTable tbody').html(res);
            });

            $('#scopeModal').modal('show');
        });

        $(document).on('click', '.btnUpload', function() {
            let type = $(this).data('type');
            let title = $(this).data('title');

            $('#uploadType').val(type);
            $('#uploadModalTitle').text(title);

            $('#uploadModal').modal('show');
        });
    </script>
@endpush

@push('css')
    <style>
        /* Samakan tinggi input & select */
        #scopeTable .form-control,
        #scopeTable .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 10px;
            font-size: 14px;
        }

        /* Tengahin konten cell */
        #scopeTable td,
        #scopeTable th {
            vertical-align: middle !important;
        }

        /* Select2 full width */
        #scopeTable .select2-container {
            width: 100% !important;
        }

        /* Rapihin tombol hapus */
        #scopeTable .btnRemoveRow {
            padding: 4px 8px;
        }

        /* Lebarin kolom input angka */
        #scopeTable input[type="number"],
        #scopeTable input.harga-input {
            text-align: right;
        }

        /* Header table lebih clean */
        #scopeTable thead th {
            background: #f8f9fa;
            font-weight: 600;
        }

        /* Modal footer */
        .modal-footer {
            justify-content: space-between;
        }

        .file-preview-list {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .file-preview {
            width: 180px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            text-align: center;
            background: #fff;
        }

        .file-preview:hover {
            background: #f8f9fa;
        }

        .file-preview-name {
            font-size: 13px;
            margin-top: 5px;
        }

        /* Divider antar dokumen marketing */
        .card-marketing .card-body:not(:first-child) {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 5px;
        }
    </style>
@endpush
