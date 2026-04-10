@if (isset($scopes) && $scopes->count())
    @foreach ($scopes as $i => $row)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $row->lokasi }}</td>
            <td>{{ $row->jenis_nama }}</td>
            <td>{{ $row->tipe_nama }}</td>
            <td>{{ $row->kategori_nama }}</td>
            <td class="text-center">{{ $row->jumlah }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center text-muted">
            Belum ada data scope
        </td>
    </tr>
@endif
