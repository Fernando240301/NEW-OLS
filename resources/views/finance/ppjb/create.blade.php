@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Tambah PPJB')

@section('content_header')
    <h1>Permohonan Pengadaan Barang / Jasa</h1>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

    <form method="POST" action="{{ route('ppjb-new.store') }}">
        @csrf

        <div class="card">
            <div class="card-header bg-primary">
                <strong>Header PPJB</strong>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <label>Kepada</label>
                        <input type="text" name="kepada" class="form-control" value="Dept. Keuangan">
                    </div>

                    <div class="col-md-6">
                        <label>Dari</label>
                        <select name="dari" id="dari" class="form-control" required>
                            <option value="">-- Pilih Departemen --</option>
                            <option value="Dept. Operasional">Dept. Operasional</option>
                            <option value="Dept. Marketing">Dept. Marketing</option>
                            <option value="Dept. HSE">Dept. HSE</option>
                            <option value="Dept. Keuangan">Dept. Keuangan</option>
                            <option value="Dept. IT">Dept. IT</option>
                            <option value="Dept. HR/GA">Dept. HR/GA</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-12">

                    <div class="col-md-12">
                        <label>Pengajuan Untuk</label>
                        <select name="jenis_pengajuan" id="jenis_pengajuan" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="project">Project</option>
                            <option value="project_migas">Project (untuk MIGAS)</option>
                            <option value="non_project">Non Project</option>
                        </select>
                    </div>

                </div>

                <div id="projectSection" style="display:none;">
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Pilih Project (SIK)</label>
                            <select name="workflow_id[]" id="projectSelect" class="form-control select2bs4" multiple style="width:150%">

                                <option value="">-- Pilih Project --</option>

                                @foreach ($projects as $project)
                                    <option value="{{ $project['workflowid'] }}"
                                        data-noproject="{{ $project['no_project'] }}"
                                        data-nama="{{ $project['projectname'] }}"
                                        data-start="{{ $project['date_start'] }}"
                                        data-end="{{ $project['date_end'] }}">

                                        {{ $project['no_sik'] ?? $project['no_project'] }}

                                    </option>
                                @endforeach

                            </select>

                            <script id="migasOptions" type="text/template">
                                
                                @foreach ($migas as $p)
                                <option value="{{ $p['workflowid'] }}"
                                data-noproject="{{ $p['project_number'] }}">
                                
                                {{ $p['project_number'] }} | {{ $p['project_name'] }} | {{ $p['client'] }}
                                
                                </option>
                                @endforeach
                                
                                </script>
                        </div>
                    </div>
                </div>

                <div class="row mt-3" id="projectInfoSection" style="display:none;">
                    <div class="col-md-6">
                        <label>No Project</label>
                        <input type="text" name="project_no" id="project_no" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Refer to Project No</label>
                        <input type="text" name="refer_project" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Tanggal Permohonan</label>
                        <input type="date" name="tanggal_permohonan" class="form-control"
                            value="{{ old('tanggal_permohonan', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label>Tanggal Dibutuhkan</label>

                        {{-- FIELD TAMPILAN --}}
                        <input type="text" id="tanggal_display" class="form-control">

                        {{-- HIDDEN FIELD UNTUK SIMPAN --}}
                        <input type="hidden" name="tanggal_mulai" id="tanggal_mulai">
                        <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">

                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" required>
                    </div>

                    <div class="col-md-6">

                        <label>PIC</label>

                        {{-- MODE NORMAL --}}
                        <div id="picNormal">
                            <select name="pic" id="picNormalSelect" class="form-control select2bs4" style="width:100%">
                                <option value="">-- Pilih PIC --</option>

                                @foreach ($users as $u)
                                    <option value="{{ $u->fullname }}"
                                        {{ $u->userid == $user->userid ? 'selected' : '' }}>
                                        {{ $u->fullname }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- MODE SPECIAL --}}
                        <div id="picSpecial" style="display:none;">
                            <select name="pic_special" id="picSelect" class="form-control select2" style="width:100%">
                                <option value="">-- Pilih PIC --</option>

                                @foreach ($users as $u)
                                    <option value="{{ $u->fullname }}">
                                        {{ $u->fullname }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- DETAIL TABLE --}}

        <div class="card mt-4">
            <div class="card-header bg-info">
                <strong>Detail Barang / Jasa</strong>
            </div>

            <div class="card-body">

            <div class="mb-2">
                <label>
                    <input type="checkbox" id="pph23Checkbox">
                    PPH 23 (2%)
                </label>
            </div>

            <div class="mb-2">
                <label>
                    <input type="checkbox" id="ppnCheckbox">
                    PPN Masukan (11%)
                </label>
            </div>

                <table class="table table-bordered table-sm align-middle" id="detailTable">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="25%">COA</th>
                            <th width="8%">Qty</th>
                            <th width="10%">Satuan</th>
                            <th width="20%">Uraian</th>
                            <th width="12%">Harga</th>
                            <th width="12%">Total</th>
                            <th width="13%">Keterangan</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <select name="details[0][coa_id]" class="form-control form-control-sm coa-select">
                                    <option value="">-- Pilih COA --</option>
                                    @foreach ($coas as $coa)
                                        <option value="{{ $coa->id }}">
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" name="details[0][qty]"
                                    class="form-control form-control-sm qty text-center">
                            </td>

                            <td>
                                <input type="text" name="details[0][satuan]"
                                    class="form-control form-control-sm text-center">
                            </td>

                            <td>
                                <input type="text" name="details[0][uraian]" class="form-control form-control-sm">
                            </td>

                            <td>
                                <input type="number" name="details[0][harga]"
                                    class="form-control form-control-sm harga text-end">
                            </td>

                            <td>
                                <input type="text" class="form-control form-control-sm total text-end" readonly>
                            </td>

                            <td>
                                <input type="text" name="details[0][keterangan]" class="form-control form-control-sm">
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger removeRow">
                                    ✕
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="btn btn-success" id="addRow">
                    + Tambah Baris
                </button>

                <hr>

                <h4 class="text-right">
                    Grand Total: Rp. <span id="grandTotal">0</span>
                </h4>

            </div>
        </div>

        <button class="btn btn-primary mt-3">
            Simpan PPJB
        </button>

    </form>

@stop


@section('js')
    <script>
        let index = 1;

        function initSelect2() {

            // 🔥 EXCLUDE picNormalSelect
            $('.select2bs4').not('#picNormalSelect').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih --',
                width: '100%',
                minimumResultsForSearch: 0
            });

            // 🔥 KHUSUS PIC NORMAL (FREE INPUT)
            $('#picNormalSelect').select2({
                width: '100%',
                placeholder: '-- Pilih / Ketik PIC --',
                tags: true,
                allowClear: true,
                createTag: function (params) {
                    let term = $.trim(params.term);

                    if (term === '') return null;

                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            $('.coa-select').select2({
                width: '100%',
                placeholder: "Cari kode / nama COA..."
            });

            $('#projectSelect').select2({
                width: '100%',
                placeholder: '-- Cari Project / No / Client --',
                allowClear: true
            });
        }

        $(document).ready(function() {
            initSelect2();
        });

        $('#addRow').click(function() {

            let row = `
        <tr>
            <td>
                <select name="details[${index}][coa_id]" class="form-control coa-select">
                    <option value="">-- Pilih COA --</option>
                    @foreach ($coas as $coa)
                        <option value="{{ $coa->id }}">
                            {{ $coa->code }} - {{ $coa->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="details[${index}][qty]" class="form-control qty"></td>
            <td><input type="text" name="details[${index}][satuan]" class="form-control"></td>
            <td><input type="text" name="details[${index}][uraian]" class="form-control"></td>
            <td><input type="number" name="details[${index}][harga]" class="form-control harga"></td>
            <td><input type="text" class="form-control total" readonly></td>
            <td><input type="text" name="details[${index}][keterangan]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger removeRow">X</button></td>
        </tr>
        `;

            $('#detailTable tbody').append(row);

            index++;
            initSelect2();
        });

        $(document).on('keyup change', '.qty, .harga', function() {

            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.qty').val()) || 0;
            let harga = parseFloat(row.find('.harga').val()) || 0;
            let total = qty * harga;

            row.find('.total').val(total.toLocaleString('id-ID'));

            calculateGrandTotal();
            calculatePPH23();
            calculatePPN();
        });

        function calculateGrandTotal() {

            let grand = 0;

            $('#detailTable tbody tr').each(function () {

                let row = $(this);

                let val = parseFloat(
                    row.find('.total').val().replace(/\./g, '').replace(',', '.')
                ) || 0;

                if (row.hasClass('pph23-row')) {
                    grand -= val; // ❗ PPH dikurang
                } else {
                    grand += val; // barang + PPN
                }

            });

            $('#grandTotal').text(grand.toLocaleString('id-ID'));
        }

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateGrandTotal();
        });

        $(document).ready(function() {

            $('#picSelect').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            function generateRekeningOptions() {

                let select = $('#picSelect');

                if (select.hasClass("select2-hidden-accessible")) {
                    select.select2('destroy');
                }

                select.empty();

                select.append(`<option value="{{ $user->fullname }}">
        {{ $user->fullname }}
    </option>`);

                for (let i = 1; i <= 50; i++) {
                    select.append(`<option value="Rekening ${i}">
            Rekening ${i}
        </option>`);
                }

                select.select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });
            }

            $('#pekerjaan').on('keyup change', function() {

                let value = $(this).val().toLowerCase();

                const keywords = [
                    'pemkes',
                    'keselamatan'
                ];

                let found = keywords.some(keyword => value.includes(keyword));

                if (found) {
                    $('#picNormal').hide();
                    $('#picSpecial').show();
                    generateRekeningOptions();
                } else {
                    $('#picSpecial').hide();
                    $('#picNormal').show();
                }

            });

        });

        $('#projectSelect').on('change', function() {

            let selectedOptions = $(this).find(':selected');

            let projectNos = [];
            let startDates = [];
            let endDates = [];

            selectedOptions.each(function () {
                let opt = $(this);

                // 🔥 ambil SEMUA project
                let raw = opt.data('noproject');
                let nama = opt.data('nama');

                if (raw) {

                    // 🔥 NORMALISASI PR-PR jadi PR
                    let clean = raw.replace(/^PR-PR-/i, 'PR-');

                    // 🔥 GABUNGKAN DENGAN NAMA PROJECT
                    if (nama) {
                        projectNos.push(clean + ' - ' + nama);
                    } else {
                        projectNos.push(clean);
                    }
                }

                if (opt.data('start')) {
                    startDates.push(opt.data('start'));
                }

                if (opt.data('end')) {
                    endDates.push(opt.data('end'));
                }
            });

            // ✅ tampilkan SEMUA (bukan cuma 1)
            $('#project_no').val(projectNos.join(', '));

            // 🔥 handle tanggal range
            if (startDates.length && endDates.length) {

                let minStart = startDates.sort()[0];
                let maxEnd = endDates.sort().slice(-1)[0];

                if (minStart === maxEnd) {
                    $('#tanggal_display').val(minStart);
                } else {
                    $('#tanggal_display').val(minStart + ' s.d ' + maxEnd);
                }

                $('#tanggal_mulai').val(minStart);
                $('#tanggal_selesai').val(maxEnd);
            }
        });

        $('#jenis_pengajuan').change(function() {

            let val = $(this).val();

            if (val === 'project') {

                $('#projectSection').slideDown();
                $('#projectInfoSection').slideDown();

                $('label:contains("Pilih Project")').text('Pilih Project (SIK)');

                $('#projectSelect').html(`
<option value="">-- Pilih Project --</option>
{!! collect($projects)->map(function ($p) {
        return "<option value='{$p['workflowid']}'
data-noproject='{$p['no_project']}'
data-nama='{$p['projectname']}'
data-start='{$p['date_start']}'
data-end='{$p['date_end']}'>
{$p['no_sik']} | {$p['projectname']} | {$p['client']}
</option>";
    })->implode('') !!}
`);

                $('#projectSelect').select2('destroy');

                $('#projectSelect').select2({
                    width: '100%',
                    placeholder: '-- Cari Project / No / Client --',
                    allowClear: true
                });

                $('#tanggal_display')
                    .attr('type', 'date')
                    .prop('readonly', false)
                    .val('');

            } else if (val === 'project_migas') {

                $('#projectSection').slideDown();
                $('#projectInfoSection').slideDown();

                $('label:contains("Pilih Project")').text('Pilih Project (PR)');

                $('#projectSelect').html(`
<option value="">-- Pilih Project --</option>
{!! collect($projects)->map(function ($p) {
        return "<option value='{$p['workflowid']}'
data-noproject='{$p['no_project']}'
data-nama='{$p['projectname']}'
data-start='{$p['date_start']}'
data-end='{$p['date_end']}'>
{$p['no_sik']} | {$p['projectname']} | {$p['client']}
</option>";
    })->implode('') !!}
`);

                $('#projectSelect').select2('destroy');

                $('#projectSelect').select2({
                    width: '100%',
                    placeholder: '-- Cari Project / No / Client --',
                    allowClear: true
                });

                $('#tanggal_display')
                    .attr('type', 'date')
                    .prop('readonly', false)
                    .val('');

            } else {

                $('#projectSection').slideUp();
                $('#projectInfoSection').slideUp();

                $('#projectSelect').val('').trigger('change');

                $('#tanggal_display')
                    .attr('type', 'date')
                    .prop('readonly', false)
                    .val('');
            }

            $('#tanggal_mulai').val('');
            $('#tanggal_selesai').val('');

        });

        // ===============================
        // FIX NON PROJECT TANGGAL
        // ===============================
        $('#tanggal_display').on('change', function () {

            let date = $(this).val();

            if (date) {
                $('#tanggal_mulai').val(date);
                $('#tanggal_selesai').val(date);
            }
        });

        // ===============================
        // FALLBACK SAAT SUBMIT (WAJIB)
        // ===============================
        $('form').on('submit', function () {

            let display = $('#tanggal_display').val();

            if (display && !$('#tanggal_mulai').val()) {
                $('#tanggal_mulai').val(display);
                $('#tanggal_selesai').val(display);
            }
        });

        $('#jenis_pengajuan').change(function () {

            let val = $(this).val();

            if (val === 'non_project') {

                // 🔥 HAPUS VALUE
                $('#projectSelect').val(null).trigger('change');

                // 🔥 HAPUS NAME BIAR TIDAK KEKIRIM
                $('#projectSelect').removeAttr('name');

            } else {

                // 🔥 BALIKKAN NAME
                $('#projectSelect').attr('name', 'workflow_id[]');
            }
        });

        let ppnIndex = null;

        $('#ppnCheckbox').change(function () {

            if ($(this).is(':checked')) {
                addPPNRow();
            } else {
                removePPNRow();
            }

        });

        function addPPNRow() {

    if (ppnIndex !== null) return;

    let coaId = null;

    $('.coa-select option').each(function () {
        if ($(this).text().includes('1115-004')) {
            coaId = $(this).val();
        }
    });

    let row = `
            <tr class="ppn-row">
                <td>
                    <select name="details[${index}][coa_id]" class="form-control coa-select">
                        <option value="${coaId}" selected>1115-004 - PPN Masukan</option>
                    </select>
                </td>
                <td><input type="number" name="details[${index}][qty]" value="1" class="form-control qty"></td>
                <td><input type="text" name="details[${index}][satuan]" value="ls" class="form-control"></td>
                <td><input type="text" name="details[${index}][uraian]" value="PPN Masukan 11%" class="form-control"></td>
                <td><input type="number" name="details[${index}][harga]" class="form-control harga ppn-harga" readonly></td>
                <td><input type="text" class="form-control total ppn-total" readonly></td>
                <td><input type="text" name="details[${index}][keterangan]" value="PPN" class="form-control"></td>
                <td></td>
            </tr>
            `;

            $('#detailTable tbody').append(row);

            ppnIndex = index;
            index++;

            calculatePPN();
        }

        function getSubtotal() {

            let subtotal = 0;

            $('#detailTable tbody tr').each(function () {

                let row = $(this);

                if (row.hasClass('ppn-row')) return;
                if (row.hasClass('pph23-row')) return;

                let val = parseFloat(
                    row.find('.total').val().replace(/\./g, '').replace(',', '.')
                ) || 0;

                subtotal += val;
            });

            return subtotal;
        }

        function removePPNRow() {
            $('.ppn-row').remove();
            ppnIndex = null;
            calculateGrandTotal();
        }

        function calculatePPN() {

            if (ppnIndex === null) return;

            let subtotal = getSubtotal();

            let ppn = subtotal * 0.11;

            $('.ppn-harga').val(ppn);
            $('.ppn-total').val(ppn.toLocaleString('id-ID'));

            calculateGrandTotal();
        }

        let pph23Index = null;

        $('#pph23Checkbox').change(function () {

            if ($(this).is(':checked')) {

                addPPH23Row();

            } else {

                removePPH23Row();
            }

        });

        function addPPH23Row() {

            if (pph23Index !== null) return; // biar gak double

            let coaId = null;

            // 🔥 cari COA 1115-002 dari dropdown
            $('.coa-select option').each(function () {
                if ($(this).text().includes('1115-002')) {
                    coaId = $(this).val();
                }
            });

            let row = `
            <tr class="pph23-row">
                <td>
                    <select name="details[${index}][coa_id]" class="form-control coa-select">
                        <option value="${coaId}" selected>1115-002 - PPh Psl - 23 Bayar Dimuka</option>
                    </select>
                </td>
                <td><input type="number" name="details[${index}][qty]" value="1" class="form-control qty"></td>
                <td><input type="text" name="details[${index}][satuan]" value="ls" class="form-control"></td>
                <td><input type="text" name="details[${index}][uraian]" value="Potongan PPH 23" class="form-control"></td>
                <td><input type="number" name="details[${index}][harga]" class="form-control harga pph23-harga" readonly></td>
                <td><input type="text" class="form-control total pph23-total" readonly></td>
                <td><input type="text" name="details[${index}][keterangan]" value="PPH 23" class="form-control"></td>
                <td></td>
            </tr>
            `;

            $('#detailTable tbody').append(row);

            pph23Index = index;
            index++;

            calculatePPH23();
        }

        function removePPH23Row() {
            $('.pph23-row').remove();
            pph23Index = null;
            calculateGrandTotal();
        }

        function calculatePPH23() {

            if (pph23Index === null) return;

            let subtotal = getSubtotal(); // ✅ hanya dari barang

            let pph = subtotal * 0.02;

            $('.pph23-harga').val(pph);
            $('.pph23-total').val(pph.toLocaleString('id-ID'));

            calculateGrandTotal();
        }

        function formatRupiahNegative(val) {
            return '(' + val.toLocaleString('id-ID') + ')';
        }
    </script>
@stop

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

        /* text selected jadi hitam */
        .select2-container--bootstrap4 .select2-selection__choice {
            color: #000 !important;
            background-color: #e9ecef !important; /* opsional biar tetap soft */
            border-color: #0b78e5 !important;
        }

        /* teks di dalamnya */
        .select2-container--bootstrap4 .select2-selection__choice__display {
            color: #000 !important;
        }

        .select2-selection__choice {
            color: #000 !important;
        }

        /* =========================
        FIX KHUSUS PIC (NO THEME)
        ========================= */

        /* container PIC */
        #picNormal .select2-container .select2-selection {
            min-height: 38px !important;
            height: auto !important;
            display: flex;
            align-items: center;
            padding: 4px 8px;
        }

        /* teks di dalam */
        #picNormal .select2-selection__rendered {
            line-height: normal !important;
            display: flex;
            align-items: center;
        }

        /* tombol clear (X) biar center */
        #picNormal .select2-selection__clear {
            margin-top: 0 !important;
        }

        /* arrow dropdown */
        #picNormal .select2-selection__arrow {
            height: 100% !important;
        }

        /* biar text gak turun */
        #picNormal .select2-selection__rendered span {
            line-height: normal !important;
        }

        .pph23-row {
            background-color: #fff3cd; /* kuning soft */
        }

        .pph23-row .pph23-total,
        .pph23-row .pph23-harga {
            color: #dc3545; /* merah bootstrap */
            font-weight: bold;
        }

        .ppn-row {
            background-color: #d1ecf1; /* biru muda */
        }

        .ppn-row .ppn-total,
        .ppn-row .ppn-harga {
            color: #0c5460;
            font-weight: bold;
        }
    </style>
@endsection
