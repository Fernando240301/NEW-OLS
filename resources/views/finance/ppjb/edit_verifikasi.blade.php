@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Verifikasi PPJB')

@section('content_header')
    <h1>Verifikasi PPJB - {{ $ppjb->no_ppjb }}</h1>
@stop

@section('content')

<form method="POST" action="{{ route('ppjb-new.update', $ppjb->id) }}">
    @csrf
    @method('PUT')

    <input type="hidden" name="mode" value="verifikasi">

    {{-- ================= HEADER ================= --}}
    <div class="card">
        <div class="card-header bg-info">
            <strong>Header PPJB</strong>
        </div>

        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <label>Kepada</label>
                    <input type="text" class="form-control"
                        value="{{ $ppjb->kepada }}" readonly>
                </div>

                <div class="col-md-6">
                    <label>Dari</label>
                    <input type="text" class="form-control"
                        value="{{ $ppjb->dari }}" readonly>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label>Tanggal Permohonan</label>
                    <input type="text" class="form-control"
                        value="{{ $ppjb->tanggal_permohonan }}" readonly>
                </div>

                <div class="col-md-6">
                    <label>Pekerjaan</label>
                    <input type="text" class="form-control"
                        value="{{ $ppjb->pekerjaan }}" readonly>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label>PIC</label>
                    <input type="text" class="form-control"
                        value="{{ $ppjb->pic ?? '-' }}" readonly>
                </div>

                <div class="col-md-6">
                    <label>No. Project</label>
                    <div class="form-control" style="height: auto;">
                        {{ $ppjb->refer_project ?? '-' }}
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

                    @foreach ($ppjb->details ?? [] as $i => $detail)
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
        Update (Verifikasi)
    </button>

</form>

@stop

@section('js')
<script>
    let COA_PPH = {{ $coas->firstWhere('code','1115-002')->id ?? 67 }};
    let COA_PPN = {{ $coas->firstWhere('code','1115-004')->id ?? 69 }};
</script>

<script>
    let index = {{ count($ppjb->details ?? []) }};

    function initSelect2(el = null) {
        if (el) {
            el.find('.coa-select').select2({
                width: '100%',
                placeholder: "Cari kode / nama COA..."
            });
        } else {
            $('.coa-select').select2({
                width: '100%',
                placeholder: "Cari kode / nama COA..."
            });
        }
    }

    function getBaseSubtotal() {
        let subtotal = 0;

        $('#detailTable tbody tr').each(function () {
            if ($(this).hasClass('ppn-row')) return;
            if ($(this).hasClass('pph23-row')) return;

            let qty = parseFloat($(this).find('.qty').val()) || 0;
            let harga = parseFloat($(this).find('.harga').val()) || 0;

            subtotal += qty * harga;
        });

        return subtotal;
    }

    function calculateGrandTotal() {

        let total = 0;

        $('#detailTable tbody tr').each(function () {

            let val = $(this).find('.total').val();

            if (!val) return;

            val = val.toString().replace(/[^\d\-]/g, '');

            total += parseFloat(val) || 0;
        });

        $('#grandTotal').text(total.toLocaleString('id-ID'));
    }

    $(document).ready(function() {
        initSelect2();
        calculateGrandTotal();

        $('#detailTable tbody tr').each(function () {

            let uraian = $(this).find('input[name*="[uraian]"]').val()?.toLowerCase() || '';
            let ket = $(this).find('input[name*="[keterangan]"]').val()?.toLowerCase() || '';

            if (uraian.includes('ppn') || ket.includes('ppn')) {
                $(this).addClass('ppn-row');
                $(this).find('.harga').addClass('ppn-harga');
                $(this).find('.total').addClass('ppn-total');
            }

            if (uraian.includes('pph') || ket.includes('pph')) {
                $(this).addClass('pph23-row');
                $(this).find('.harga').addClass('pph23-harga');
                $(this).find('.total').addClass('pph23-total');
            }
        });

        refreshTaxStatus();

        setTimeout(() => {
            calculatePPN();
            calculatePPH23();
        }, 100);
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

        row.find('.total').val(qty * harga);

        recalcAll();
    });

    function recalcAll() {
        console.log('RECALC JALAN');
        calculatePPN();
        calculatePPH23();
        calculateGrandTotal();
    }

    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
        calculateGrandTotal();
    });

    let pph23Index = null;
    let ppnIndex = null;

    $('#pph23Checkbox').change(function () {
        if ($(this).is(':checked')) {
            addPPH23Row();
        } else {
            removePPH23Row();
        }
    });

    $('#ppnCheckbox').change(function () {
        if ($(this).is(':checked')) {
            addPPNRow();
        } else {
            removePPNRow();
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
            <td><input type="number" name="details[${index}][qty]" value="1" class="form-control qty" readonly></td>
            <td><input type="text" name="details[${index}][satuan]" value="ls" class="form-control" readonly></td>
            <td><input type="text" name="details[${index}][uraian]" value="Potongan PPH 23" class="form-control" readonly></td>
            <td><input type="number" name="details[${index}][harga]" class="form-control harga pph23-harga" readonly></td>
            <td><input type="text" class="form-control total pph23-total" readonly></td>
            <td><input type="text" name="details[${index}][keterangan]" value="PPH 23" class="form-control" readonly></td>
            <td class="text-center text-muted">Auto</td>
        </tr>
        `;

        $('#detailTable tbody').append(row);
        index++;


        recalcAll();
        calculatePPH23();
        refreshTaxStatus();
    }

    function addPPNRow() {

        if ($('#detailTable tbody tr').filter('.ppn-row').length) return;

        let row = `
        <tr class="ppn-row">
            <td>
                <input type="hidden" name="details[${index}][coa_id]" value="${COA_PPN}">
                <input type="text" class="form-control" value="1115-004 - PPN Masukan" readonly>
            </td>
            <td><input type="number" name="details[${index}][qty]" value="1" class="form-control qty" readonly></td>
            <td><input type="text" name="details[${index}][satuan]" value="ls" class="form-control" readonly></td>
            <td><input type="text" name="details[${index}][uraian]" value="PPN Masukan 11%" class="form-control" readonly></td>
            <td><input type="number" name="details[${index}][harga]" class="form-control harga ppn-harga" readonly></td>
            <td><input type="text" class="form-control total ppn-total" readonly></td>
            <td><input type="text" name="details[${index}][keterangan]" value="PPN" class="form-control" readonly></td>
            <td class="text-center text-muted">Auto</td>
        </tr>
        `;

        $('#detailTable tbody').append(row);
        index++;

        recalcAll();
        calculatePPN();
        refreshTaxStatus();
    }

    function calculatePPN() {
        let subtotal = getBaseSubtotal();
        let ppn = subtotal * 0.11;

        console.log('PPN:', ppn);

        $('.ppn-harga').val(ppn);
        $('.ppn-total').val(ppn);
    }

    function calculatePPH23() {
        let subtotal = getBaseSubtotal();
        let pph = subtotal * 0.02;

        console.log('PPH:', pph);

        $('.pph23-harga').val(pph);
        $('.pph23-total').val(-pph);
    }

    function removePPH23Row() {
        $('.pph23-row').remove();
        recalcAll();
        refreshTaxStatus();
        calculateGrandTotal();
    }

    function removePPNRow() {
        $('.ppn-row').remove();
        recalcAll();
        refreshTaxStatus();
        calculateGrandTotal();
    }

    function refreshTaxStatus() {
        $('#ppnCheckbox').prop('checked', $('.ppn-row').length > 0);
        $('#pph23Checkbox').prop('checked', $('.pph23-row').length > 0);
    }
</script>
@stop

@section('css')
<style>
    #detailTable tbody tr.ppn-row td {
        background-color: #e8f7ff !important;
    }

    #detailTable tbody tr.pph23-row td {
        background-color: #fff3cd !important;
    }
</style>
@endsection