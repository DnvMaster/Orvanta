<tr>
    <td style="padding-left: {{ 1 + $level * 1.75 }}rem;">
        @if ($level > 0)
            <span class="text-muted me-1">↳</span>
        @endif
        <span class="fw-semibold">{{ $department->name }}</span>
    </td>
    <td class="text-center">
        <span class="badge bg-light text-dark border">{{ $department->users_count ?? $department->users()->count() }}</span>
    </td>
    <td class="text-end">
        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-primary">{{ __('Редактировать') }}</a>
        <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Удалить подразделение «:name»?', ['name' => $department->name])));">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Удалить') }}</button>
        </form>
    </td>
</tr>
@foreach ($department->descendants as $child)
    @include('orvanta.departments.partials.row', ['department' => $child, 'level' => $level + 1])
@endforeach
