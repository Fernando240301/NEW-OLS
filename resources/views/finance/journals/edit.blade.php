@extends('adminlte::page')

@section('title', 'Edit Manual Journal')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Edit Manual Journal</h1>
@stop

@section('content')

    <form method="POST" action="{{ route('journals.update', $journal) }}" id="journalForm" novalidate>
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="journal_date" value="{{ old('journal_date', $journal->journal_date) }}"
                        class="form-control" required>
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

                        @foreach ($journal->details as $index => $detail)
                            <tr>
                                <td>
                                    <select name="details[{{ $index }}][account_id]"
                                        class="form-control accountSelect" required>
                                        <option value="">-- Pilih Account --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ $detail->account_id == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $index }}][debit]"
                                        value="{{ number_format($detail->debit, 0, ',', '.') }}"
                                        class="form-control debit numberFormat">
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $index }}][credit]"
                                        value="{{ number_format($detail->credit, 0, ',', '.') }}"
                                        class="form-control credit numberFormat">
                                </td>

                                <td>
                                    <input type="text" name="details[{{ $index }}][memo]"
                                        value="{{ $detail->memo }}" class="form-control">
                                </td>

                                <td>
                                    <button type="button" class="btn btn-sm btn-danger removeRow">
                                        X
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        {{-- TEMPLATE --}}
                        <tr id="row-template" style="display:none;">
                            <td>
                                <select class="form-control accountSelect" required>
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
                    + Tambah Baris
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

            <div class="card-footer">
                <button class="btn btn-success" id="saveBtn">
                    Update Draft
                </button>
            </div>
        </div>

    </form>

@stop

@section('js')
    <script>
        $(function() {

            let rowIndex = {{ $journal->details->count() }};

            function getRawNumber(value) {
                return parseFloat(value.replace(/\./g, '')) || 0;
            }

            function formatNumber(value) {
                if (!value) return '';
                return new Intl.NumberFormat('id-ID').format(value);
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

                if (totalDebit === totalCredit && totalDebit > 0) {
                    $('#balanceStatus')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .text('BALANCE');
                } else {
                    $('#balanceStatus')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .text('TIDAK BALANCE');
                }
            }

            window.addRow = function() {

                let newRow = $('#row-template').clone().removeAttr('id').show();

                newRow.find('.accountSelect')
                    .attr('name', `details[${rowIndex}][account_id]`);

                newRow.find('.debit')
                    .attr('name', `details[${rowIndex}][debit]`);

                newRow.find('.credit')
                    .attr('name', `details[${rowIndex}][credit]`);

                newRow.find('.memo')
                    .attr('name', `details[${rowIndex}][memo]`);

                $('#journalTable tbody').append(newRow);

                newRow.find('.accountSelect').select2({
                    width: '100%'
                });

                rowIndex++;
            }

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

            $(document).on('input', '.numberFormat', function() {
                let raw = $(this).val().replace(/\D/g, '');
                $(this).val(formatNumber(raw));
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                calculate();
            });

            $('.accountSelect').select2({
                width: '100%'
            });

            calculate();
        });
    </script>
@stop
