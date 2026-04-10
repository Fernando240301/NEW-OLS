@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Buat LPBJ')

@section('content_header')
    <h1>LPBJ - {{ $ppjb->no_ppjb }}</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('lpjb.store', $ppjb->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-4">
                        <label>Tanggal LPBJ</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Total Budget</label>
                        <input type="text" class="form-control" value="{{ number_format($ppjb->total, 2, ',', '.') }}"
                            readonly>
                    </div>

                </div>

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-info">
                Detail LPBJ
            </div>

            <div class="card-body">

                <table class="table table-bordered table-sm align-middle" id="lpjbTable">
                    <thead class="text-center table-light">
                        <tr>
                            <th width="29%">COA</th>
                            <th width="22%">Uraian</th>
                            <th width="7%">Qty</th>
                            <th width="9%">Harga</th>
                            <th width="7%">Real Qty</th>
                            <th width="8%">Satuan</th>
                            <th width="14%">Real Harga</th>
                            <th width="10%">Selisih</th>
                            <th width="6%">Bukti</th>
                            <th width="6%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ppjb->details as $i => $detail)
                            <tr>

                                {{-- COA --}}
                                <td class="text-truncate">
                                    <input type="hidden" name="details[{{ $i }}][coa_id]"
                                        value="{{ $detail->coa_id }}">
                                    {{ $detail->coa->code }} - {{ $detail->coa->name }}
                                    <input type="hidden" name="details[{{ $i }}][ppjb_detail_id]"
                                        value="{{ $detail->id }}">
                                </td>

                                {{-- URAIAN --}}
                                <td>
                                    {{ $detail->uraian }}
                                    <input type="hidden" name="details[{{ $i }}][uraian]"
                                        value="{{ $detail->uraian }}">
                                </td>

                                {{-- QTY --}}
                                <td class="text-center">
                                    {{ $detail->qty }}
                                    <input type="hidden" name="details[{{ $i }}][budget_qty]"
                                        value="{{ $detail->qty }}">
                                </td>

                                {{-- HARGA --}}
                                <td class="text-end">
                                    {{ number_format($detail->harga, 2, ',', '.') }}
                                    <input type="hidden" name="details[{{ $i }}][budget_harga]"
                                        value="{{ $detail->harga }}">
                                </td>

                                {{-- REAL QTY --}}
                                <td>
                                    <input type="number" name="details[{{ $i }}][real_qty]"
                                        class="form-control form-control-sm text-center real_qty">
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $i }}][satuan]"
                                        value="{{ $detail->satuan }}" class="form-control form-control-sm text-center">
                                </td>

                                {{-- REAL HARGA --}}
                                <td>
                                    <input type="number" name="details[{{ $i }}][real_harga]"
                                        class="form-control form-control-sm text-end real_harga">
                                </td>

                                {{-- SELISIH --}}
                                <td class="text-end">
                                    <input type="text" class="form-control form-control-sm text-end selisih" readonly>
                                </td>

                                {{-- BUKTI --}}
                                <td class="text-center position-relative">

                                    <input type="file" name="details[{{ $i }}][bukti_file]"
                                        class="d-none bukti-input">

                                    <button type="button" class="btn btn-sm btn-outline-primary uploadBtn"
                                        data-bs-toggle="tooltip" title="Upload Bukti">
                                        <i class="fas fa-paperclip"></i>
                                    </button>

                                    <span class="badge bg-success d-none file-badge"
                                        style="position:absolute; top:-5px; right:-5px;">
                                        ✔
                                    </span>

                                </td>

                                {{-- ACTION --}}
                                <td></td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn btn-success" id="addRow">
                    + Tambah Item Baru
                </button>

                <hr>

                <h4>Total Realisasi: Rp <span id="grandTotal">0</span></h4>

            </div>
        </div>

        <button class="btn btn-primary mt-3">
            Simpan LPBJ
        </button>

    </form>

@stop


@section('js')
    <script>
        let index = {{ count($ppjb->details) }};

        function initSelect2() {
            $('.coa-select').select2({
                placeholder: 'Pilih COA',
                width: 'resolve',
                dropdownAutoWidth: true
            });
        }

        $(document).ready(function() {
            initSelect2(); // 🔥 WAJIB DIPANGGIL
        });

        function calculateGrand() {

            let grand = 0;

            $('#lpjbTable tbody tr').each(function() {

                let qty = parseFloat($(this).find('.real_qty').val()) || 0;
                let harga = parseFloat($(this).find('.real_harga').val()) || 0;

                grand += qty * harga;

            });

            $('#grandTotal').text(
                grand.toLocaleString('id-ID')
            );
        }

        $(document).on('keyup change', '.real_qty, .real_harga', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.real_qty').val()) || 0;
            let harga = parseFloat(row.find('.real_harga').val()) || 0;
            let total = qty * harga;
            row.find('.real_total').val(total);
            calculateGrand();
        });

        $('#addRow').click(function() {

            let row = `
<tr>

<td>
<select name="details[${index}][coa_id]" 
        class="form-control form-control-sm coa-select">
<option value="">-- Pilih COA --</option>
@foreach ($coas as $coa)
<option value="{{ $coa->id }}">
{{ $coa->code }} - {{ $coa->name }}
</option>
@endforeach
</select>
</td>

<td>
<input type="text" 
       name="details[${index}][uraian]" 
       class="form-control form-control-sm">
</td>

<td class="text-center">
-
<input type="hidden"
       name="details[${index}][budget_qty]"
       value="0">
</td>

<td class="text-center">
-
<input type="hidden"
       name="details[${index}][budget_harga]"
       value="0">
</td>

<td>
<input type="number" 
       name="details[${index}][real_qty]" 
       class="form-control form-control-sm text-center real_qty">
</td>

<td>
<input type="text"
       name="details[${index}][satuan]"
       class="form-control form-control-sm text-center">
</td>

<td>
<input type="number" 
       name="details[${index}][real_harga]" 
       class="form-control form-control-sm text-end real_harga">
</td>

<td>
<input type="text"
       class="form-control form-control-sm text-end selisih"
       readonly>
</td>

<td class="text-center">

<input type="file"
       name="details[${index}][bukti_file]"
       class="d-none bukti-input">

<button type="button"
        class="btn btn-sm btn-outline-primary uploadBtn"
        title="Upload Bukti">
    <i class="fas fa-paperclip"></i>
</button>

</td>

<td>
<button type="button" 
        class="btn btn-sm btn-danger removeRow px-2 py-1">
    <i class="fas fa-times"></i>
</button>
</td>

</tr>
`;

            $('#lpjbTable tbody').append(row);
            index++;

            initSelect2(); // 🔥 WAJIB DIPANGGIL SETELAH APPEND
        });

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateGrand();
        });

        $(document).on('click', '.uploadBtn', function() {
            $(this).closest('td').find('.bukti-input').click();
        });

        $(document).on('change', '.bukti-input', function() {
            let btn = $(this).closest('td').find('.uploadBtn');
            btn.removeClass('btn-outline-primary')
                .addClass('btn-success');
        });

        function calculateRow(row) {

            let qty = parseFloat(row.find('.real_qty').val()) || 0;
            let harga = parseFloat(row.find('.real_harga').val()) || 0;

            let real = qty * harga;

            let budgetHarga = parseFloat(
                row.find('input[name*="[budget_harga]"]').val()
            ) || 0;

            let budgetQty = parseFloat(
                row.find('input[name*="[budget_qty]"]').val()
            ) || 0;

            let budget = budgetQty * budgetHarga;

            let selisih = budget - real;

            row.find('.selisih').val(
                selisih.toLocaleString('id-ID')
            );
        }

        $(document).on('keyup change', '.real_qty, .real_harga', function() {
            let row = $(this).closest('tr');
            calculateRow(row);
        });

        // Trigger file dialog
        $(document).on('click', '.uploadBtn', function() {
            $(this).closest('td').find('.bukti-input').click();
        });

        // Saat file dipilih
        $(document).on('change', '.bukti-input', function() {

            let td = $(this).closest('td');
            let btn = td.find('.uploadBtn');
            let badge = td.find('.file-badge');

            btn.removeClass('btn-outline-primary')
                .addClass('btn-success');

            badge.removeClass('d-none');

            let fileName = this.files[0]?.name ?? 'File attached';

            btn.attr('title', fileName);
        });
    </script>
@stop


@section('css')
    <style>
        #lpjbTable {
            table-layout: fixed;
            font-size: 13px;
        }

        #lpjbTable thead th {
            background-color: #f4f6f9;
            font-weight: 600;
        }

        #lpjbTable td {
            padding: 6px;
        }

        #lpjbTable input {
            border-radius: 4px;
        }

        .uploadBtn {
            padding: 4px 6px;
        }

        .removeRow {
            padding: 4px 6px;
        }

        .selisih {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
@stop
