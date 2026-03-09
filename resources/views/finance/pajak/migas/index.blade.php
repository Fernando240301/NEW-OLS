@extends('adminlte::page')

@section('title', 'Pajak MIGAS')

@section('content_header')
    <h1>Pajak Project MIGAS</h1>
@stop

@section('content')

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>No PPJB</th>
                <th>PIC</th>
                <th>Total</th>
                <th width="140">PPH21</th>
                <th width="140">PPH23</th>
                <th width="140">PPH29</th>
                <th width="140">Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($ppjbs as $ppjb)
                <form method="POST" action="{{ route('pajak.migas.process') }}">
                    @csrf

                    <tr>

                        <td>{{ $ppjb->no_ppjb }}</td>

                        <td>{{ $ppjb->pic }}</td>

                        <td class="total" data-total="{{ $ppjb->total }}">
                            {{ number_format($ppjb->total, 0, ',', '.') }}
                        </td>

                        <input type="hidden" name="ppjb_id" value="{{ $ppjb->id }}">

                        <td>
                            <input type="number" name="pph21" class="form-control">
                        </td>

                        <td>
                            <input type="number" name="pph23" class="form-control pph23">
                        </td>

                        <td>
                            <input type="number" name="pph29" class="form-control">
                        </td>

                        <td>
                            <button type="submit" class="btn btn-success btn-sm">
                                Proses Pajak
                            </button>
                        </td>

                    </tr>

                </form>
            @endforeach

        </tbody>

    </table>

@stop


@section('js')

    <script>
        $(document).ready(function() {

            /*
            =====================================
            AUTO HITUNG PPH23 (2%)
            =====================================
            */

            $('.pph23').on('focus', function() {

                let row = $(this).closest('tr');

                let total = parseFloat(row.find('.total').data('total'));

                let pph23 = total * 0.02;

                $(this).val(Math.round(pph23));

            });

        });
    </script>

@stop
