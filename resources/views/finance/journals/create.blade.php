@extends('adminlte::page')

@section('title', 'Buat Manual Journal')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Buat Manual Journal</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('journals.store') }}" id="journalForm" novalidate>
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="journal_date" class="form-control" required>
                        </div>

                        <table class="table table-bordered" id="journalTable">
                            <thead>
                                <tr>
                                    <th width="40%">Account</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th width="20%">Deskripsi</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="row-template" style="display:none;">
                                    <td>
                                        <select class="form-control accountSelect">
                                            <option value="">-- Pilih Account --</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control debit numberFormat">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control credit numberFormat">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control memo" placeholder="Keterangan...">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger removeRow">
                                            X
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-sm btn-primary" onclick="addRow()">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total Debit:</strong>
                                <span id="totalDebit">0</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Total Credit:</strong>
                                <span id="totalCredit">0</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <span id="balanceStatus" class="text-danger">
                                    TIDAK BALANCE
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-success" id="saveBtn" disabled>
                    Simpan Draft
                </button>
            </div>

        </div>
    </form>

@stop

@section('js')
    <script>
        $(function() {

            let rowIndex = 0;

            function getRawNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            function formatNumber(value) {
                if (!value) return '';
                return new Intl.NumberFormat('id-ID').format(value);
            }

            function addRow() {

                let newRow = $('#row-template').clone().removeAttr('id').show();

                newRow.find('.accountSelect')
                    .attr('required', true)
                    .attr('name', `details[${rowIndex}][account_id]`);

                newRow.find('.debit')
                    .attr('name', `details[${rowIndex}][debit]`)
                    .on('input', calculate);

                newRow.find('.credit')
                    .attr('name', `details[${rowIndex}][credit]`)
                    .on('input', calculate);

                newRow.find('.memo')
                    .attr('name', `details[${rowIndex}][memo]`);

                newRow.find('.removeRow').click(function() {
                    newRow.remove();
                    calculate();
                });

                $('#journalTable tbody').append(newRow);

                newRow.find('.accountSelect').select2({
                    width: '100%',
                    placeholder: "Cari akun...",
                    allowClear: true
                });

                rowIndex++;
            }

            function calculate() {

                let totalDebit = 0;
                let totalCredit = 0;

                $('.debit').each(function() {
                    totalDebit += getRawNumber($(this).val());
                });

                $('.credit').each(function() {
                    totalCredit += getRawNumber($(this).val());
                });

                $('#totalDebit').text(formatNumber(totalDebit));
                $('#totalCredit').text(formatNumber(totalCredit));
                $('#previewDebit').text(formatNumber(totalDebit));
                $('#previewCredit').text(formatNumber(totalCredit));

                if (totalDebit === totalCredit && totalDebit > 0) {
                    $('#balanceStatus').removeClass('text-danger').addClass('text-success').text('BALANCE');
                    $('#previewBalance').removeClass('text-danger').addClass('text-success').text('BALANCED');
                    $('#saveBtn').prop('disabled', false);
                } else {
                    $('#balanceStatus').removeClass('text-success').addClass('text-danger').text('TIDAK BALANCE');
                    $('#previewBalance').removeClass('text-success').addClass('text-danger').text('NOT BALANCED');
                    $('#saveBtn').prop('disabled', true);
                }
            }

            // Exclusive Debit/Credit
            $(document).on('input', '.debit', function() {
                if (getRawNumber($(this).val()) > 0) {
                    $(this).closest('tr').find('.credit').val('');
                }
                calculate();
            });

            $(document).on('input', '.credit', function() {
                if (getRawNumber($(this).val()) > 0) {
                    $(this).closest('tr').find('.debit').val('');
                }
                calculate();
            });

            // Format Number
            $(document).on('input', '.numberFormat', function() {
                let raw = $(this).val().replace(/\D/g, '');
                $(this).val(formatNumber(raw));
            });

            // Auto focus debit after select
            $(document).on('select2:select', '.accountSelect', function() {
                $(this).closest('tr').find('.debit').focus();
            });

            // Highlight active row
            $(document).on('focus', 'input, select', function() {
                $('tr').removeClass('table-active');
                $(this).closest('tr').addClass('table-active');
            });

            // Arrow navigation safe
            $(document).on('keydown', '.debit, .credit', function(e) {

                let currentRow = $(this).closest('tr');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    let nextRow = currentRow.next('tr');
                    if (nextRow.length) {
                        nextRow.find($(this).hasClass('debit') ? '.debit' : '.credit').focus();
                    }
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    let prevRow = currentRow.prev('tr');
                    if (prevRow.length) {
                        prevRow.find($(this).hasClass('debit') ? '.debit' : '.credit').focus();
                    }
                }
            });

            // Enter to next input
            $(document).on('keydown', 'input', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let inputs = $('input:visible');
                    let index = inputs.index(this);
                    if (index > -1 && index + 1 < inputs.length) {
                        inputs.eq(index + 1).focus();
                    }
                }
            });

            // Auto add row on credit Enter
            $(document).on('keydown', '.credit', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    let currentRow = $(this).closest('tr');
                    if (!currentRow.next('tr').length) {
                        addRow();
                    }
                    $('#journalTable tbody tr:last .accountSelect').select2('open');
                }
            });

            // Ctrl + S
            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    if (!$('#saveBtn').prop('disabled')) {
                        $('#journalForm').submit();
                    }
                }
            });

            window.addRow = addRow;

            addRow();
            addRow();
        });
    </script>
@endsection

@push('css')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 4px 10px;
        }

        .select2-selection__rendered {
            line-height: 28px !important;
        }

        .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
@endpush
