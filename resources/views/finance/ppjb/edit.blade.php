@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Edit PPJB')

@section('content_header')
    <h1>Edit PPJB - {{ $ppjb->no_ppjb }}</h1>
@stop

@section('content')

@php
    $selectedProjects = json_decode($ppjb->workflow_id ?? $ppjb->pr_workflow_id, true) ?? [];
@endphp

    <form method="POST" action="{{ route('ppjb-new.update', $ppjb->id) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header bg-primary">
                <strong>Header PPJB</strong>
            </div>

            <div class="card-body">

                {{-- KEPADA & DARI --}}
                <div class="row">
                    <div class="col-md-6">
                        <label>Kepada</label>
                        <input type="text" name="kepada" class="form-control" value="{{ $ppjb->kepada }}">
                    </div>

                    <div class="col-md-6">
                        <label>Dari</label>
                        <select name="dari" id="dari" class="form-control" required>
                            @foreach (['Dept. Operasional', 'Dept. Marketing', 'Dept. Keuangan', 'Dept. IT', 'Dept. HR/GA'] as $dept)
                                <option value="{{ $dept }}" {{ $ppjb->dari == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- JENIS PENGAJUAN --}}
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label>Pengajuan Untuk</label>
                        <select name="jenis_pengajuan" id="jenis_pengajuan" class="form-control" required>
                            <option value="">-- Pilih --</option>

                            <option value="project" {{ $ppjb->jenis_pengajuan == 'project' ? 'selected' : '' }}>
                                Project
                            </option>

                            <option value="project_migas" {{ $ppjb->jenis_pengajuan == 'project_migas' ? 'selected' : '' }}>
                                Project Migas
                            </option>

                            <option value="non_project" {{ $ppjb->jenis_pengajuan == 'non_project' ? 'selected' : '' }}>
                                Non Project
                            </option>
                        </select>
                    </div>
                </div>

                {{-- PROJECT SECTION --}}
                <div id="projectSection"
                    style="{{ in_array($ppjb->jenis_pengajuan, ['project', 'project_migas']) ? '' : 'display:none;' }}">

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Pilih Project (SIK)</label>
                            <select name="workflow_id[]" id="projectSelect"
                                class="form-control select2bs4"
                                multiple
                                style="width:100%">

                                <option value="">-- Pilih Project --</option>

                                @foreach ($projects as $project)
                                    <option value="{{ $project['workflowid'] }}"
                                        {{ in_array($project['workflowid'], $selectedProjects) ? 'selected' : '' }}
                                        data-noproject="{{ $project['no_project'] }}"
                                        data-nama="{{ $project['projectname'] }}"
                                        data-start="{{ $project['date_start'] }}"
                                        data-end="{{ $project['date_end'] }}">

                                        {{ $project['no_project'] }}
                                        | {{ $project['projectname'] }}
                                        | {{ $project['client'] }}

                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                </div>

                {{-- PROJECT INFO --}}
                <div class="row mt-3" id="projectInfoSection"
                    style="{{ in_array($ppjb->jenis_pengajuan, ['project', 'project_migas']) ? '' : 'display:none;' }}">

                    <div class="col-md-6">
                        <label>No Project</label>
                        <input type="text" name="project_no" id="project_no" class="form-control" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Refer to Project No</label>
                        <input type="text" name="refer_projecta" class="form-control" value="-">
                    </div>

                </div>

                {{-- TANGGAL --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Tanggal Permohonan</label>
                        <input type="date" name="tanggal_permohonan" class="form-control"
                            value="{{ $ppjb->tanggal_permohonan }}" required>
                    </div>

                    <div class="col-md-6">
                        <label>Tanggal Dibutuhkan</label>

                        <input type="text" id="tanggal_display" class="form-control">

                        <input type="hidden" name="tanggal_mulai" id="tanggal_mulai" value="{{ $ppjb->tanggal_mulai }}">

                        <input type="hidden" name="tanggal_selesai" id="tanggal_selesai"
                            value="{{ $ppjb->tanggal_selesai }}">
                    </div>
                </div>

                {{-- PEKERJAAN & PIC --}}
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" id="pekerjaan" class="form-control"
                            value="{{ $ppjb->pekerjaan }}">
                    </div>

                    <div class="col-md-6">

                        <label>PIC</label>

                        {{-- NORMAL --}}
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

                        {{-- SPECIAL --}}
                        <div id="picSpecial" style="display:none;">
                            <select name="pic_special" id="picSelect" class="form-control select2" style="width:100%;">
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- DETAIL --}}
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

                        @foreach ($ppjb->details as $i => $detail)
                            <tr>
                                <td>
                                    <select name="details[{{ $i }}][coa_id]"
                                        class="form-control form-control-sm coa-select">
                                        <option value="">-- Pilih COA --</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{ $coa->id }}"
                                                {{ $detail->coa_id == $coa->id ? 'selected' : '' }}>
                                                {{ $coa->code }} - {{ $coa->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="number" step="any" name="details[{{ $i }}][qty]"
                                        value="{{ $detail->qty }}" class="form-control form-control-sm qty text-center">
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $i }}][satuan]"
                                        value="{{ $detail->satuan }}" class="form-control form-control-sm text-center">
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $i }}][uraian]"
                                        value="{{ $detail->uraian }}" class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="number" step="any" name="details[{{ $i }}][harga]"
                                        value="{{ abs($detail->harga) }}" class="form-control form-control-sm harga text-end">
                                </td>

                                <td>
                                    <input type="text" class="form-control form-control-sm total text-end"
                                        value="{{ $detail->qty * $detail->harga }}" readonly>
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $i }}][keterangan]"
                                        value="{{ $detail->keterangan }}" class="form-control form-control-sm">
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        @endforeach

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
            Update PPJB
        </button>

    </form>

@stop

@section('js')
    <script>
        let COA_PPH = {{ $coas->firstWhere('code','1115-002')->id ?? 67 }};
        let COA_PPN = {{ $coas->firstWhere('code','1115-004')->id ?? 69 }};
    </script>

    <script>
        let index = {{ count($ppjb->details) }};

        /* =========================================
           INIT SELECT2
        ========================================= */
        function initSelect2(el = null) {

            if (el) {
                el.find('.coa-select').select2({
                    width: '100%',
                    placeholder: "Cari kode / nama COA..."
                });
            } else {
                $('.select2bs4').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });

                $('.coa-select').select2({
                    width: '100%',
                    placeholder: "Cari kode / nama COA..."
                });

                $('#projectSelect').select2({
                    width: '100%',
                    allowClear: true
                });
            }
        }

        /* =========================================
           GRAND TOTAL
        ========================================= */
        function calculateGrandTotal() {

            let subtotal = getBaseSubtotal();

            let ppn = $('.ppn-row').length ? subtotal * 0.11 : 0;
            let pph = $('.pph23-row').length ? subtotal * 0.02 : 0;

            let grand = subtotal + ppn - pph;

            $('#grandTotal').text(grand.toLocaleString('id-ID'));
        }

        $('.qty, .harga').trigger('change');

        /* =========================================
           DOCUMENT READY
        ========================================= */
        $(document).ready(function() {

            initSelect2();

            $('#projectSelect').trigger('change');

            /* =============================
               INIT TANGGAL DISPLAY (EDIT)
            ============================= */
            let start = $('#tanggal_mulai').val();
            let end = $('#tanggal_selesai').val();

            if (start && end) {
                if (start === end) {
                    $('#tanggal_display').val(start);
                } else {
                    $('#tanggal_display').val(start + ' s.d ' + end);
                }
            }

            /* =============================
               AUTO TRIGGER PROJECT SELECT
            ============================= */
            $('#projectSelect').trigger('change');

            calculateGrandTotal();

            // =============================
            // AUTO CHECK BASED ON DATA DB
            // =============================

            let hasPPH = false;
            let hasPPN = false;

            $('#detailTable tbody tr').each(function () {

                let uraian = $(this).find('input[name*="[uraian]"]').val()?.toLowerCase() || '';
                let ket = $(this).find('input[name*="[keterangan]"]').val()?.toLowerCase() || '';

                if (uraian.includes('pph') || ket.includes('pph')) {
                    $(this).addClass('pph23-row');
                    $(this).find('.harga').addClass('pph23-harga');
                    $(this).find('.total').addClass('pph23-total');
                    hasPPH = true;
                }

                if (uraian.includes('ppn') || ket.includes('ppn')) {
                    $(this).addClass('ppn-row');
                    $(this).find('.harga').addClass('ppn-harga');
                    $(this).find('.total').addClass('ppn-total');
                    hasPPN = true;
                }
            });

            refreshTaxStatus();

            if (hasPPH) $('#pph23Checkbox').prop('checked', true);
            if (hasPPN) $('#ppnCheckbox').prop('checked', true);

            setTimeout(() => {
                calculatePPN();
                calculatePPH23();
                calculateGrandTotal();
            }, 100);
        });


        /* =========================================
           ADD ROW
        ========================================= */
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
        <td><input type="number" step="any" name="details[${index}][qty]" class="form-control qty"></td>
        <td><input type="text" name="details[${index}][satuan]" class="form-control"></td>
        <td><input type="text" name="details[${index}][uraian]" class="form-control"></td>
        <td><input type="number" step="any" name="details[${index}][harga]" class="form-control harga"></td>
        <td><input type="text" class="form-control total" readonly></td>
        <td><input type="text" name="details[${index}][keterangan]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger removeRow">X</button></td>
    </tr>
    `;

            $('#detailTable tbody').append(row);

            index++;
            initSelect2();

            calculatePPN();
            calculatePPH23();
        });


        /* =========================================
           HITUNG TOTAL PER BARIS
        ========================================= */
        $(document).on('keyup change', '.qty, .harga', function() {

            let row = $(this).closest('tr');

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let harga = parseFloat(row.find('.harga').val()) || 0;

            let total = qty * harga;

            row.find('.total').val(total);

            // 🔥 baru hitung pajak setelah total benar
            calculatePPN();
            calculatePPH23();

            calculateGrandTotal();
        });


        /* =========================================
           REMOVE ROW
        ========================================= */
        $(document).on('click', '.removeRow', function() {

            $(this).closest('tr').remove();

            calculatePPN();
            calculatePPH23();
            calculateGrandTotal();
        });


        /* =========================================
           PROJECT SELECT
        ========================================= */
        $('#projectSelect').on('change', function () {

            let selectedOptions = $(this).find(':selected');

            let projectNos = [];
            let startDates = [];
            let endDates = [];

            selectedOptions.each(function () {
                let opt = $(this);

                let raw = opt.data('noproject');
                let nama = opt.data('nama');

                if (raw) {
                    let clean = raw.replace(/^PR-PR-/i, 'PR-');

                    if (nama) {
                        projectNos.push(clean + ' - ' + nama);
                    } else {
                        projectNos.push(clean);
                    }
                }

                if (opt.data('start')) startDates.push(opt.data('start'));
                if (opt.data('end')) endDates.push(opt.data('end'));
            });

            $('#project_no').val(projectNos.join(', '));

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


        /* =========================================
           JENIS PENGAJUAN
        ========================================= */
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
        <option value="">-- Pilih Project MIGAS --</option>
        ` + $('#migasOptions').html());

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


        /* =========================================
           NON PROJECT DATE
        ========================================= */
        $('#tanggal_display').on('change', function() {

            let date = $(this).val();

            if (date) {
                $('#tanggal_mulai').val(date);
                $('#tanggal_selesai').val(date);
            }

        });


        /* =========================================
           PIC SPECIAL MODE
        ========================================= */

        function generateRekeningOptions() {

            let select = $('#picSelect');

            if (select.hasClass("select2-hidden-accessible")) {
                select.select2('destroy');
            }

            select.empty();

            select.append(`<option value="{{ $ppjb->pic }}">
        {{ $ppjb->pic }}
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
                'pemeriksaan keselamatan'
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

        let pph23Index = null;

        $('#pph23Checkbox').change(function () {

            if ($(this).is(':checked')) {
                addPPH23Row();
            } else {
                removePPH23Row();
            }

        });

        function addPPH23Row() {

            if ($('#detailTable tbody tr').filter('.pph23-row').length) return;

            let row = `
            <tr class="pph23-row">
                <td>
                    <input type="hidden" name="details[${index}][coa_id]" value="${COA_PPH}">
                    <input type="text" class="form-control" value="1115-002 - PPh Psl 23" readonly>
                </td>
                <td><input type="number" step="any" name="details[${index}][qty]" value="1" class="form-control qty" readonly></td>
                <td><input type="text" name="details[${index}][satuan]" value="ls" class="form-control" readonly></td>
                <td><input type="text" name="details[${index}][uraian]" value="Potongan PPH 23" class="form-control" readonly></td>
                <td><input type="number" step="any" name="details[${index}][harga]" class="form-control harga pph23-harga" readonly></td>
                <td><input type="text" class="form-control total pph23-total" readonly></td>
                <td><input type="text" name="details[${index}][keterangan]" value="PPH 23" class="form-control" readonly></td>
                <td class="text-center text-muted">Auto</td>
            </tr>
            `;

            let newRow = $(row);
            $('#detailTable tbody').append(newRow);

            index++;

            calculatePPH23();
            refreshTaxStatus();
        }

        let ppnIndex = null;

        $('#ppnCheckbox').change(function () {

            if ($(this).is(':checked')) {
                addPPNRow();
            } else {
                removePPNRow();
            }

        });

        function addPPNRow() {

            if ($('#detailTable tbody tr').filter('.ppn-row').length) return;

            let row = `
            <tr class="ppn-row">
                <td>
                    <input type="hidden" name="details[${index}][coa_id]" value="${COA_PPN}">
                    <input type="text" class="form-control" value="1115-004 - PPN Masukan" readonly>
                </td>
                <td><input type="number" step="any" name="details[${index}][qty]" value="1" class="form-control qty" readonly></td>
                <td><input type="text" name="details[${index}][satuan]" value="ls" class="form-control" readonly></td>
                <td><input type="text" name="details[${index}][uraian]" value="PPN Masukan 11%" class="form-control" readonly></td>
                <td><input type="number" step="any" name="details[${index}][harga]" class="form-control harga ppn-harga" readonly></td>
                <td><input type="text" class="form-control total ppn-total" readonly></td>
                <td><input type="text" name="details[${index}][keterangan]" value="PPN" class="form-control" readonly></td>
                <td class="text-center text-muted">Auto</td>
            </tr>
            `;

            let newRow = $(row);
            $('#detailTable tbody').append(newRow);

            index++;

            calculatePPN();
            refreshTaxStatus();
        }

        function calculatePPH23() {

            let subtotal = getBaseSubtotal();
            let pph = Math.round(subtotal * 0.02);

            $('.pph23-harga').val(pph);
            $('.pph23-total').val('(' + pph.toLocaleString('id-ID') + ')');

            calculateGrandTotal();
        }

        function calculatePPN() {

            let subtotal = getBaseSubtotal();
            let ppn = Math.round(subtotal * 0.11);

            $('.ppn-harga').val(ppn);
            $('.ppn-total').val(ppn.toLocaleString('id-ID'));

            calculateGrandTotal();
        }

        function removePPH23Row() {
            $('.pph23-row').remove();
            refreshTaxStatus();
            calculateGrandTotal();
        }

        function removePPNRow() {
            $('.ppn-row').remove();
            refreshTaxStatus();
            calculateGrandTotal();
        }

        function refreshTaxStatus() {

            let hasPPN = $('.ppn-row').length > 0;
            let hasPPH = $('.pph23-row').length > 0;

            $('#ppnCheckbox').prop('checked', hasPPN);
            $('#pph23Checkbox').prop('checked', hasPPH);
        }
        

        function getBaseSubtotal() {

            let subtotal = 0;

            $('#detailTable tbody tr').each(function () {

                if ($(this).hasClass('ppn-row')) return;
                if ($(this).hasClass('pph23-row')) return;

                let qty = parseFloat($(this).find('.qty').val()) || 0;
                let harga = parseFloat($(this).find('.harga').val()) || 0;

                subtotal += qty * harga; // 🔥 pakai raw, bukan .total
            });

            return subtotal;
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

        .select2-container--bootstrap4 .select2-selection__choice {
            color: #000 !important;
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
        }

        .select2-selection__choice {
            color: #000 !important;
        }

        #detailTable tbody tr.ppn-row td {
            background-color: #e8f7ff !important;
        }

        #detailTable tbody tr.pph23-row td {
            background-color: #fff3cd !important;
        }
    </style>
@endsection
