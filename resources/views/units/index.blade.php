@extends('adminlte::page')

@section('title', 'Units')

@section('plugins.Datatables', true)

@section('content_header')
    <h1 style="text-align: center; color: blue;">
        {{ $workflowdata['projectname'] }}
    </h1>
@endsection

@section('content')

    <x-project-menu :workflowid="$workflowid" />

    <table id="unitsTable" class="table table-bordered table-striped">

        <thead>
            <tr>
                <th width="15">No</th>
                <th>Leader</th>
                <th>Anggota</th>
                <th width="150">Lokasi</th>
                <th width="75">Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($data as $i => $row)
                <tr>

                    <td align="center">{{ $i + 1 }}</td>

                    <td>
                        {!! str_replace(' (', '<br>(', $row['leader']) !!}
                    </td>

                    <td>
                        @foreach ($row['anggota'] as $a)
                            @php
                                $nama = explode(' (', $a)[0];
                                $sik = isset(explode(' (', $a)[1]) ? '(' . explode(' (', $a)[1] : '';
                            @endphp

                            <div style="padding-left:10px">
                                - {{ $nama }} <br>
                                <div style="padding-left: 10px;">{{ $sik }}</div>
                            </div>
                        @endforeach
                    </td>

                    <td>{{ $row['lokasi'] }}</td>

                    <td>
                        <a href="{{ route('units.detail', [$workflowid, $row['id']]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

@stop


@section('js')

    <script>
        $(document).ready(function() {

            $('#unitsTable').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                ordering: true,
                columnDefs: [{
                    orderable: false,
                    targets: [2, 4]
                }],
                language: {
                    search: "Pencarian:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        next: "›",
                        previous: "‹"
                    }
                }
            });

        });
    </script>

@endsection
