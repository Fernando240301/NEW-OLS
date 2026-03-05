@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Edit PPJB')

@section('content_header')
    <h1>Edit PPJB - {{ $ppjb->no_ppjb }}</h1>
@stop

@section('content')

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
                            <option value="non_project" {{ $ppjb->jenis_pengajuan == 'non_project' ? 'selected' : '' }}>
                                Non Project
                            </option>
                        </select>
                    </div>
                </div>

                {{-- PROJECT SECTION --}}
                <div id="projectSection" style="{{ $ppjb->jenis_pengajuan == 'project' ? '' : 'display:none;' }}">

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Pilih Project (SIK)</label>
                            <select name="workflow_id" id="projectSelect" class="form-control select2">

                                <option value="">-- Pilih Project --</option>

                                @foreach ($projects as $project)
                                    <option value="{{ $project['workflowid'] }}"
                                        data-noproject="{{ $project['no_project'] }}"
                                        data-start="{{ $project['date_start'] }}" data-end="{{ $project['date_end'] }}"
                                        {{ $ppjb->workflow_id == $project['workflowid'] ? 'selected' : '' }}>

                                        {{ $project['no_sik'] }}
                                        @if ($project['extend'])
                                            (Extend)
                                        @endif
                                        | {{ $project['client'] }}
                                        | {{ $project['location'] }}

                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                </div>

                {{-- PROJECT INFO --}}
                <div class="row mt-3" id="projectInfoSection"
                    style="{{ $ppjb->jenis_pengajuan == 'project' ? '' : 'display:none;' }}">

                    <div class="col-md-6">
                        <label>No Project</label>
                        <input type="text" id="project_no" class="form-control" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Refer to Project No</label>
                        <input type="text" name="refer_project" class="form-control" value="{{ $ppjb->refer_project }}">
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
                            <input type="text" class="form-control" value="{{ $ppjb->pic }}" readonly>
                            <input type="hidden" name="pic" id="picHidden" value="{{ $ppjb->pic }}">
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
                                    <input type="number" name="details[{{ $i }}][qty]"
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
                                    <input type="number" name="details[{{ $i }}][harga]"
                                        value="{{ $detail->harga }}" class="form-control form-control-sm harga text-end">
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
        let index = {{ count($ppjb->details) }};

        /* =========================================
           INIT SELECT2
        ========================================= */
        function initSelect2() {
            $('.coa-select').select2({
                placeholder: 'Cari kode / nama COA...',
                width: '100%'
            });

            $('#picSelect').select2({
                width: '100%',
                dropdownAutoWidth: true
            });
        }

        /* =========================================
           GRAND TOTAL
        ========================================= */
        function calculateGrandTotal() {
            let grand = 0;

            $('.total').each(function() {
                grand += parseFloat($(this).val()) || 0;
            });

            $('#grandTotal').text(grand.toLocaleString('id-ID'));
        }

        /* =========================================
           DOCUMENT READY
        ========================================= */
        $(document).ready(function() {

            initSelect2();

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


        /* =========================================
           HITUNG TOTAL PER BARIS
        ========================================= */
        $(document).on('keyup change', '.qty, .harga', function() {

            let row = $(this).closest('tr');

            let qty = parseFloat(row.find('.qty').val()) || 0;
            let harga = parseFloat(row.find('.harga').val()) || 0;

            let total = qty * harga;

            row.find('.total').val(total);

            calculateGrandTotal();
        });


        /* =========================================
           REMOVE ROW
        ========================================= */
        $(document).on('click', '.removeRow', function() {

            $(this).closest('tr').remove();

            calculateGrandTotal();
        });


        /* =========================================
           PROJECT SELECT
        ========================================= */
        $('#projectSelect').on('change', function() {

            let selected = $(this).find(':selected');

            let noProject = selected.data('noproject');
            $('#project_no').val(noProject ?? '');

            let startDate = selected.data('start');
            let endDate = selected.data('end');

            if (startDate && endDate) {

                if (startDate === endDate) {
                    $('#tanggal_display').val(startDate);
                } else {
                    $('#tanggal_display').val(startDate + ' s.d ' + endDate);
                }

                $('#tanggal_mulai').val(startDate);
                $('#tanggal_selesai').val(endDate);
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

                $('#tanggal_display')
                    .attr('type', 'text')
                    .prop('readonly', true)
                    .val('');

            } else {

                $('#projectSection').slideUp();
                $('#projectInfoSection').slideUp();

                $('#projectSelect').val('').trigger('change');

                $('#project_no').val('');

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
    </script>
@stop

@section('css')
    <style>
        #detailTable {
            table-layout: fixed;
            width: 100%;
        }

        #detailTable th,
        #detailTable td {
            vertical-align: middle;
            overflow: hidden;
        }

        #detailTable td {
            padding: 4px;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            height: 31px !important;
            padding: 2px 6px !important;
        }

        .select2-selection__rendered {
            line-height: 24px !important;
        }

        .select2-selection__arrow {
            height: 29px !important;
        }
    </style>
@stop
