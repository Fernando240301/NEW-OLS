@extends('adminlte::page')

@section('title', 'Work Assignment')

@section('plugins.Datatables', true)

@section('plugins.Select2', true)


@section('content_header')
    <h1 style="text-align: center;">WORK ASSIGNMENT</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('work_assignment.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-bordered table-striped" id="clientTable">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Project Number</th>
                        <th>Client Name</th>
                        <th>Contract Number</th>
                        <th>Nama Project</th>
                        <th>Created by</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td class="aksi-cell">
                                <div class="aksi-grid">
                                    <a href="{{ route('verifikasi.preview', $row->workflowid) }}"
                                        class="btn btn-danger btn-xs" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                    <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#scopeModal"
                                        data-project="{{ $row->workflowid }}">
                                        <i class="fas fa-tools"></i>
                                    </button>

                                    <a href="{{ route('work_assignment.edit', $row->workflowid) }}"
                                        class="btn btn-warning btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('work_assignment.delete', $row->workflowid) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td>{{ $row->project_number }}</td>
                            <td>{{ $row->client_name }}</td>
                            <td>{{ $row->contract_number }}</td>
                            <td>{{ $row->projectname }}</td>
                            <td>{{ $row->createuser }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- MODAL SCOPE OF WORK --}}
            <div class="modal fade" id="scopeModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">

                        <div class="modal-header bg-primary">
                            <h5 class="modal-title">
                                <i class="fas fa-tasks"></i> Input Scope of Work
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form action="{{ route('work_assignment.scope.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="workflowid" id="workflowid">

                            <div class="modal-body">
                                <datalist id="lokasi-list"></datalist>

                                <button type="button" class="btn btn-secondary btn-sm mb-3" id="btnAddRow">
                                    <i class="fas fa-plus"></i> Tambah Jenis Peralatan
                                </button>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="scopeTable">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="20%">Lokasi</th>
                                                <th width="20%">Jenis</th>
                                                <th width="15%">Tipe</th>
                                                <th width="15%">Kategori</th>
                                                <th width="10%">Jumlah</th>
                                                <th width="10%">Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- row akan ditambahkan via JS --}}
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Simpan Scope
                                </button>
                                <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </form>

                    </div>
                </div>
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
        let scopeIndex = 0;
        let lokasiList = [];

        const jenisData = @json($jenisPeralatan);
        const tipeData = @json($tipePeralatan);
        const kategoriData = @json($kategoriPeralatan);

        function lokasiInput(index) {
            return `
        <input type="text"
            name="scope[${index}][lokasi]"
            class="form-control lokasi-input"
            list="lokasi-list"
            placeholder="Lokasi"
        >
    `;
        }

        function renderLokasiList() {
            let html = lokasiList
                .map(l => `<option value="${l}"></option>`)
                .join('');
            $('#lokasi-list').html(html);
        }

        function jenisOptions() {
            return jenisData
                .map(j => `<option value="${j.id}">${j.nama}</option>`)
                .join('');
        }

        function kategoriOptions() {
            return kategoriData
                .map(k => `<option value="${k.id}">${k.nama}</option>`)
                .join('');
        }

        $('#btnAddRow').on('click', function() {
            scopeIndex++;

            let row = `
<tr>
    <td class="text-center">
        <input type="hidden" name="scope[${scopeIndex}][id]" value="">
        <button type="button" class="btn btn-danger btn-sm btnRemoveRow">
            <i class="fas fa-trash"></i>
        </button>
    </td>

    <td>${lokasiInput(scopeIndex)}</td>

    <td>
        <select name="scope[${scopeIndex}][jenis]"
            class="form-control jenis-select">
            <option value="">-- Pilih Jenis --</option>
            ${jenisOptions()}
        </select>
    </td>

    <td>
        <select name="scope[${scopeIndex}][tipe]"
            class="form-control tipe-select">
            <option value="">-- Pilih Tipe --</option>
        </select>
    </td>

    <td>
        <select name="scope[${scopeIndex}][kategori]"
            class="form-control kategori-select">
            <option value="">-- Pilih Kategori --</option>
            ${kategoriOptions()}
        </select>
    </td>

    <td>
        <input type="number"
            name="scope[${scopeIndex}][jumlah]"
            class="form-control"
            min="1">
    </td>

    <td>
        <input type="text"
            name="scope[${scopeIndex}][harga]"
            class="form-control harga-input"
            placeholder="0">
    </td>
</tr>`;

            let $row = $(row);
            $('#scopeTable tbody').append($row);

            initSelect2JenisTipe($row); // ⬅️ INI
        });


        // simpan lokasi unik
        $(document).on('blur', '.lokasi-input', function() {
            let val = $(this).val().trim();
            if (val && !lokasiList.includes(val)) {
                lokasiList.push(val);
                renderLokasiList(); // ⬅️ PENTING
            }
        });

        // filter tipe berdasarkan jenis
        $(document).on('change', '.jenis-select', function() {
            let jenisId = $(this).val();
            let $row = $(this).closest('tr');
            let $tipeSelect = $row.find('.tipe-select');

            $tipeSelect.empty().append('<option value="">-- Pilih Tipe --</option>');

            tipeData
                .filter(t => t.jenis == jenisId)
                .forEach(t => {
                    $tipeSelect.append(
                        `<option value="${t.id}">${t.nama}</option>`
                    );
                });

            $tipeSelect.val(null).trigger('change');
        });

        // hapus row
        $(document).on('click', '.btnRemoveRow', function() {
            $(this).closest('tr').remove();
        });

        // format harga
        $(document).on('input', '.harga-input', function() {
            let val = this.value.replace(/\D/g, '');
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    </script>

    <script>
        function initSelect2JenisTipe($row) {
            $row.find('.kategori-select').select2({
                width: '100%',
                placeholder: '-- Pilih Kategori --',
                dropdownParent: $('#scopeModal')
            });

            $row.find('.jenis-select').select2({
                width: '100%',
                placeholder: '-- Pilih Jenis --',
                dropdownParent: $('#scopeModal')
            });

            $row.find('.tipe-select').select2({
                width: '100%',
                placeholder: '-- Pilih Tipe --',
                dropdownParent: $('#scopeModal')
            });
        }
    </script>

    <script>
        $('#scopeModal').on('show.bs.modal', function(e) {
            let workflowid = $(e.relatedTarget).data('project');
            $('#workflowid').val(workflowid);

            // reset
            $('#scopeTable tbody').html('');
            scopeIndex = 0;
            lokasiList = [];

            $.get(`/work-assignment/${workflowid}/scope`, function(rows) {

                rows.forEach(row => {
                    scopeIndex++;

                    let html = `
    <tr>
        <td class="text-center">
            <input type="hidden" name="scope[${scopeIndex}][id]" value="${row.id}">
            <button type="button" class="btn btn-danger btn-sm btnRemoveRow">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    
        <td>
            <input type="text"
                name="scope[${scopeIndex}][lokasi]"
                class="form-control lokasi-input"
                value="${row.lokasi ?? ''}">
        </td>
    
        <td>
            <select name="scope[${scopeIndex}][jenis]"
                class="form-control jenis-select">
                ${jenisOptions()}
            </select>
        </td>
    
        <td>
            <select name="scope[${scopeIndex}][tipe]"
                class="form-control tipe-select">
            </select>
        </td>
    
        <td>
            <select name="scope[${scopeIndex}][kategori]"
                class="form-control kategori-select">
                ${kategoriOptions()}
            </select>
        </td>
    
        <td>
            <input type="number"
                name="scope[${scopeIndex}][jumlah]"
                class="form-control"
                value="${row.jumlah}">
        </td>
    
        <td>
            <input type="text"
                name="scope[${scopeIndex}][harga]"
                class="form-control harga-input"
                value="${row.harga}">
        </td>
    </tr>`;

                    let $row = $(html);
                    $('#scopeTable tbody').append($row);

                    initSelect2JenisTipe($row);

                    // set value
                    $row.find('.jenis-select').val(row.jenis).trigger('change');
                    setTimeout(() => {
                        $row.find('.tipe-select').val(row.tipe).trigger('change');
                    }, 100);
                    $row.find('.kategori-select').val(row.kategori).trigger('change');
                });
            });
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

        /* kolom aksi */
        .aksi-cell {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            vertical-align: middle !important;
        }

        /* grid tombol 2x2 */
        .aksi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            /* jarak antar tombol */
        }

        /* tombol kecil & rapi */
        .aksi-grid .btn {
            padding: 4px 6px;
            font-size: 12px;
            line-height: 1;
            border-radius: 4px;
        }

        /* form jangan nambah tinggi */
        .aksi-grid form {
            margin: 0;
        }
    </style>
@endpush
