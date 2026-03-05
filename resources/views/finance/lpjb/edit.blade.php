@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Edit LPBJ')

@section('content_header')
    <h1>Edit LPBJ - {{ $lpjb->no_lpjb }}</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('lpjb.update', $lpjb->id) }}" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-4">
                        <label>Tanggal LPBJ</label>
                        <input type="date" name="tanggal" value="{{ $lpjb->tanggal }}" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>PPJB</label>
                        <input type="text" class="form-control" value="{{ $lpjb->ppjb->no_ppjb }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Status</label>
                        <input type="text" class="form-control" value="{{ strtoupper($lpjb->status) }}" readonly>
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
                            <th width="20%">COA</th>
                            <th width="15%">Uraian</th>
                            <th width="8%">Real Qty</th>
                            <th width="8%">Satuan</th> {{-- TAMBAHAN --}}
                            <th width="8%">Budget</th>
                            <th width="10%">Real Harga</th>
                            <th width="10%">Realisasi</th>
                            <th width="8%">Selisih</th>
                            <th width="6%">Bukti</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($lpjb->details as $i => $detail)
                            <tr>

                                <td>
                                    <input type="hidden" name="details[{{ $i }}][coa_id]"
                                        value="{{ $detail->coa_id }}">
                                    {{ $detail->coa->code }} - {{ $detail->coa->name }}
                                </td>

                                <td>
                                    {{ $detail->uraian }}
                                    <input type="hidden" name="details[{{ $i }}][uraian]"
                                        value="{{ $detail->uraian }}">
                                </td>

                                {{-- REAL QTY --}}
                                <td>
                                    <input type="number" name="details[{{ $i }}][real_qty]"
                                        value="{{ $detail->real_qty }}"
                                        class="form-control form-control-sm text-center real_qty">
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $i }}][satuan]"
                                        value="{{ $detail->satuan ?? optional($detail->ppjbDetail)->satuan }}"
                                        class="form-control form-control-sm text-center">
                                </td>

                                <td class="text-end">
                                    {{ number_format($detail->budget_subtotal, 2, ',', '.') }}
                                    <input type="hidden" name="details[{{ $i }}][budget_qty]"
                                        value="{{ $detail->budget_qty }}">
                                    <input type="hidden" name="details[{{ $i }}][budget_harga]"
                                        value="{{ $detail->budget_harga }}">
                                </td>

                                {{-- REAL HARGA --}}
                                <td>
                                    <input type="number" name="details[{{ $i }}][real_harga]"
                                        value="{{ $detail->real_harga }}"
                                        class="form-control form-control-sm text-end real_harga">
                                </td>

                                {{-- REALISASI --}}
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end real_total"
                                        value="{{ $detail->real_subtotal }}" readonly>
                                </td>

                                {{-- SELISIH --}}
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end selisih"
                                        value="{{ $detail->budget_subtotal - $detail->real_subtotal }}" readonly>
                                </td>

                                {{-- BUKTI --}}
                                <td class="text-center">

                                    <input type="file" name="details[{{ $i }}][bukti_file]"
                                        class="d-none bukti-input">

                                    <button type="button"
                                        class="btn btn-sm {{ $detail->bukti_file ? 'btn-success' : 'btn-outline-primary' }} uploadBtn"
                                        title="{{ $detail->bukti_file ? 'File sudah terupload' : 'Upload Bukti' }}">
                                        <i class="fas fa-paperclip"></i>
                                    </button>

                                </td>

                                {{-- DELETE --}}
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger removeRow px-2 py-1">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <hr>

                <button type="button" class="btn btn-success mb-3" id="addRow">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>

                <h5 class="text-end">
                    Total Realisasi:
                    Rp <span id="grandTotal">
                        {{ number_format($lpjb->total_realisasi, 2, ',', '.') }}
                    </span>
                </h5>

            </div>
        </div>

        <button class="btn btn-primary mt-3">
            Update LPBJ
        </button>

    </form>

@stop

@section('js')
    <script>
        function calculateGrand() {
            let grand = 0;

            $('.real_total').each(function() {
                grand += parseFloat($(this).val()) || 0;
            });

            $('#grandTotal').text(grand.toLocaleString('id-ID'));
        }

        $(document).on('keyup change', '.real_qty,.real_harga', function() {

            let row = $(this).closest('tr');

            let qty = parseFloat(row.find('.real_qty').val()) || 0;
            let harga = parseFloat(row.find('.real_harga').val()) || 0;

            let realTotal = qty * harga;

            let budget = parseFloat(
                row.find('input[name*="[budget_harga]"]').val()
            ) * parseFloat(
                row.find('input[name*="[budget_qty]"]').val()
            );

            let selisih = budget - realTotal;

            row.find('.real_total').val(realTotal);
            row.find('.selisih').val(selisih);

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

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateGrand();
        });

        let index = {{ count($lpjb->details) }};
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

<td>
<input type="text"
       name="details[${index}][satuan]"
       class="form-control form-control-sm text-center">
</td>

<td class="text-center">
-
<input type="hidden" name="details[${index}][budget_qty]" value="0">
<input type="hidden" name="details[${index}][budget_harga]" value="0">
</td>

<td>
<input type="number"
       name="details[${index}][real_qty]"
       class="form-control form-control-sm text-center real_qty">
</td>

<td>
<input type="number"
       name="details[${index}][real_harga]"
       class="form-control form-control-sm text-end real_harga">
</td>

<td>
<input type="text"
       class="form-control form-control-sm text-end real_total"
       readonly>
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

<td class="text-center">
<button type="button"
        class="btn btn-sm btn-danger removeRow px-2 py-1">
    <i class="fas fa-times"></i>
</button>
</td>

</tr>
`;

            $('#lpjbTable tbody').append(row);

            index++;

            initSelect2();

        });

        function initSelect2() {
            $('.coa-select').select2({
                placeholder: 'Pilih COA',
                width: 'resolve',
                dropdownAutoWidth: true
            });
        }

        $(document).ready(function() {
            initSelect2();
        });
    </script>
@stop
