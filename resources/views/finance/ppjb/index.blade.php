@extends('adminlte::page')

@section('title', 'PPJB')

@section('content_header')
    <h1>Daftar PPJB</h1>
@stop

@section('content')
    <style>
        .btn-sm {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <a href="{{ route('ppjb-new.create') }}" class="btn btn-primary mb-3">
        Buat PPJB
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-light text-center">
            <tr>
                <th>No PPJB</th>
                <th>Tanggal</th>
                <th>PIC</th>
                <th>Project No</th>
                <th>Status PPJB</th>
                <th>Status LPJB</th>
                <th width="180">Action PPJB</th>
                <th width="180">Action LPBJ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ppjbs as $ppjb)
                @php
                    $lpjb = $ppjb->lpjbs->first();
                @endphp

                <tr>
                    <td>{{ $ppjb->no_ppjb }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($ppjb->tanggal_permohonan)->format('d-m-Y') }}
                    </td>

                    <td>{{ $ppjb->pic }}</td>

                    {{-- PROJECT NO --}}
                    <td>{{ $ppjb->project_no ?? '-' }}</td>

                    {{-- STATUS PPJB --}}
                    <td class="text-center">
                        @if ($ppjb->status == 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($ppjb->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">{{ ucfirst($ppjb->status) }}</span>
                        @endif
                    </td>

                    {{-- STATUS LPJB --}}
                    <td class="text-center">
                        @if ($lpjb)
                            @if ($lpjb->status == 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @elseif($lpjb->status == 'waiting_pcc')
                                <span class="badge bg-warning">Waiting PCC</span>
                            @elseif($lpjb->status == 'waiting_manager')
                                <span class="badge bg-warning">Waiting Manager</span>
                            @elseif($lpjb->status == 'waiting_finance')
                                <span class="badge bg-warning">Waiting Finance</span>
                            @elseif($lpjb->status == 'waiting_director')
                                <span class="badge bg-warning">Waiting Director</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($lpjb->status) }}</span>
                            @endif
                        @else
                            <span class="badge bg-light text-dark">Belum Ada</span>
                        @endif
                    </td>

                    {{-- ===================== --}}
                    {{-- ACTION PPJB --}}
                    {{-- ===================== --}}
                    @php
                        $user = auth()->user();
                        $usernameShort = substr($user->username, 0, -1);
                    @endphp

                    <td class="text-left">

                        {{-- Preview --}}
                        <a href="{{ route('ppjb-new.pdf', $ppjb->id) }}" target="_blank" class="btn btn-sm btn-info"
                            title="Preview">
                            <i class="fas fa-eye"></i>
                        </a>

                        {{-- Edit --}}
                        @if ($ppjb->status == 'draft')
                            <a href="{{ route('ppjb-new.edit', $ppjb->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif

                        {{-- Revisi --}}
                        @if ($ppjb->status == 'approved')
                            <form action="{{ route('ppjb-new.revise', $ppjb->id) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Revisi PPJB ini?')">
                                @csrf
                                <button class="btn btn-sm btn-warning" title="Revisi">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                        @endif

                        {{-- Approve hanya jika dia termasuk PIC --}}
                        @if ($ppjb->status == 'draft' && str_contains(strtolower($ppjb->pic), strtolower($usernameShort)))
                            <form action="{{ route('ppjb-new.approve', $ppjb->id) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Approve PPJB ini?')">
                                @csrf
                                <button class="btn btn-sm btn-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif

                    </td>

                    {{-- ===================== --}}
                    {{-- ACTION LPBJ --}}
                    {{-- ===================== --}}
                    <td class="text-left">

                        @if ($lpjb)
                            {{-- Detail --}}
                            <a href="{{ route('lpjb.pdf', $lpjb->id) }}" target="_blank" class="btn btn-sm btn-info"
                                title="Detail LPBJ">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            @if ($lpjb->status == 'draft')
                                <a href="{{ route('lpjb.edit', $lpjb->id) }}" class="btn btn-sm btn-primary"
                                    title="Edit LPBJ">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif

                            {{-- Approve --}}
                            @if ($lpjb->status == 'draft')
                                <form action="{{ route('lpjb.approve', $lpjb->id) }}" method="POST"
                                    style="display:inline-block" onsubmit="return confirm('Approve LPBJ ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Approve LPBJ">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Revisi --}}
                            @if ($lpjb->status == 'approved')
                                <form action="{{ route('lpjb.revise', $lpjb->id) }}" method="POST"
                                    style="display:inline-block" onsubmit="return confirm('Revisi LPBJ ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-warning" title="Revisi LPBJ">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            @endif
                        @else
                            @if ($ppjb->status == 'approved')
                                <a href="{{ route('lpjb.create', $ppjb->id) }}" class="btn btn-sm btn-secondary"
                                    title="Buat LPBJ">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        Belum ada data PPJB
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $ppjbs->links() }}

@stop
