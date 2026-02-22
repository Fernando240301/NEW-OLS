@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title', 'Tambah PPJB')

@section('content_header')
    <h1>Permohonan Pengadaan Barang / Jasa</h1>
@stop

@section('content')

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
                        <input type="text" name="kepada" class="form-control" value="Direktur Utama">
                    </div>

                    <div class="col-md-6">
                        <label>Dari</label>
                        <input type="text" name="dari" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>No Project</label>
                        <input type="text" name="project_no" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Refer to Project No</label>
                        <input type="text" name="refer_project" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Tanggal Permohonan</label>
                        <input type="date" name="tanggal_permohonan" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Tanggal Dibutuhkan</label>
                        <input type="date" name="tanggal_dibutuhkan" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>PIC</label>
                        <input type="text" name="pic" class="form-control">
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
            $('.coa-select').select2({
                placeholder: 'Cari kode / nama COA...',
                width: '100%'
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

            row.find('.total').val(total);
            calculateGrandTotal();
        });

        function calculateGrandTotal() {
            let grand = 0;
            $('.total').each(function() {
                grand += parseFloat($(this).val()) || 0;
            });

            $('#grandTotal').text(grand.toLocaleString('id-ID'));
        }

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateGrandTotal();
        });
    </script>
@stop
