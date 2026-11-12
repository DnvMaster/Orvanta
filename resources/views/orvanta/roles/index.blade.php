@extends('orvanta.layouts.app')
@section('title', 'Роли — Orvanta CRM')
@section('breadcrumb', 'Роли и права')

@section('content')
<div class="dashboard-content py-4">
    <div class="page-heading mb-4">
        <div>
            <p class="eyebrow">РОЛИ И ПРАВА</p>
            <h1>Роли и права доступа</h1>
            <p class="page-description mb-0">Управление ролями сотрудников и областями видимости данных</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-success">+ Создать роль</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light mb-5">
                    <tr>
                        <th>Название роли</th>
                        <th>Системный код</th>
                        <th>Описание</th>
                        <th class="text-center">Сотрудники</th>
                        <th class="text-center">Активных прав</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="fw-semibold">
                                {{ $role->name }}
                                @if($role->is_system)
                                    <span class="badge bg-secondary ms-1">Системная</span>
                                @endif
                            </td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td class="text-muted small">{{ $role->description ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $role->users_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $role->permissions_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                    Настроить права
                                </a>

                                @unless($role->is_system)
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Удалить роль «{{ $role->name }}»?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Удалить
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Роли еще не созданы.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection