<tr>
    <td style="padding-left: {{ $level * 25 }}px;">
        {{ $account->code }}
    </td>
    <td>{{ $account->name }}</td>
    <td>{{ $account->type->name }}</td>
    <td>{{ $account->category->name ?? '-' }}</td>
    <td>
        @if ($account->is_header)
            <span class="badge badge-secondary">Header</span>
        @else
            <span class="badge badge-success">Postable</span>
        @endif
    </td>
    <td>
        <div class="d-flex gap-1">
            <a href="{{ route('chart-of-accounts.edit', $account->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-edit"></i>
            </a>

            <form action="{{ route('chart-of-accounts.destroy', $account->id) }}" method="POST"
                onsubmit="return confirm('Yakin hapus akun ini?')">
                @csrf
                @method('DELETE')

                <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>

@if ($account->children)
    @foreach ($account->children as $child)
        @include('finance.chart_of_accounts.partials.row', ['account' => $child, 'level' => $level + 1])
    @endforeach
@endif
