@extends('orvanta.layouts.app')
@section('title', 'Сотрудники — Orvanta CRM')
@section('breadcrumb', 'Сотрудники')
@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">{{ __('Сотрудники') }}</h1>
                <p class="text-muted mb-0">{{ __('Управление учетными записями, структурой подчинения и ролями доступа') }}</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary">{{ __('+ Добавить сотрудника') }}</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Сотрудник') }}</th>
                            <th>{{ __('Должность / Отдел') }}</th>
                            <th>{{ __('Руководитель') }}</th>
                            <th>{{ __('Назначенные роли') }}</th>
                            <th class="text-center">{{ __('Статус') }}</th>
                            <th class="text-end">{{ __('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td>
                                <div>{{ $user->position ?? '—' }}</div>
                                <div class="text-muted small">{{ $user->organizationalDepartment?->name ?? __('Без отдела') }}</div>
                            </td>
                            <td class="small">{{ $user->manager?->name ?? '—' }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted small">{{ __('Роли не назначены') }}</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                @if ($user->isActive())
                                    <span class="badge bg-success">{{ __('Активен') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Неактивен') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">{{ __('Редактировать') }}</a>
                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('Удалить сотрудника «:name»?', ['name' => $user->name])));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Удалить') }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('Сотрудники не найдены.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="card-footer bg-white">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
@endsection
