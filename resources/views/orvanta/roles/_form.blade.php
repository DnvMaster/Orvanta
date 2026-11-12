@extends('orvanta.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            {{ $role->exists ? 'Редактирование роли: ' . $role->name : 'Создание новой роли' }}
        </h1>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            ← Назад к списку
        </a>
    </div>

    <form action="{{ $role->exists ? route('roles.update', $role) : route('roles.store') }}" method="POST">
        @csrf
        @if($role->exists)
            @method('PUT')
        @endif

        {{-- Основные данные роли --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Основные параметры</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Название роли <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $role->name) }}" placeholder="Например: Старший менеджер" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label">Системный код (Slug) <span class="text-danger">*</span></label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $role->slug) }}"
                               placeholder="senior_manager"
                               {{ $role->is_system ? 'disabled' : 'required' }}>
                        @if($role->is_system)
                            <div class="form-text">Системный код встроенной роли нельзя изменить.</div>
                        @endif
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Описание</label>
                        <textarea id="description" name="description" rows="2"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Краткое описание зоны ответственности роли">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Матрица разрешений --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Матрица разрешений и областей видимости</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary" data-set-all="">Сбросить все</button>
                    <button type="button" class="btn btn-outline-primary" data-set-all="own">Всем: Свои (own)</button>
                    <button type="button" class="btn btn-outline-primary" data-set-all="department">Всем: Отдел (department)</button>
                    <button type="button" class="btn btn-outline-primary" data-set-all="all">Всем: Все (all)</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45%;">Разрешение</th>
                            <th style="width: 20%;">Системный ключ</th>
                            <th class="text-center" style="width: 35%;">Область видимости (Scope)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedPermissions as $groupName => $permissions)
                            @php $groupSlug = \Illuminate\Support\Str::slug($groupName); @endphp
                            <tr class="table-secondary">
                                <td colspan="2" class="fw-bold text-uppercase small">
                                    Раздел: {{ $groupName }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-light border" data-group="{{ $groupSlug }}" data-scope="">Нет</button>
                                        <button type="button" class="btn btn-light border" data-group="{{ $groupSlug }}" data-scope="own">Own</button>
                                        <button type="button" class="btn btn-light border" data-group="{{ $groupSlug }}" data-scope="department">Dept</button>
                                        <button type="button" class="btn btn-light border" data-group="{{ $groupSlug }}" data-scope="all">All</button>
                                    </div>
                                </td>
                            </tr>

                            @foreach($permissions as $permission)
                                @php
                                    $currentScope = old("permissions.{$permission->id}", $assignedPermissions[$permission->id] ?? '');
                                @endphp
                                <tr data-permission-group="{{ $groupSlug }}">
                                    <td>
                                        <div class="fw-medium">{{ $permission->name }}</div>
                                        @if($permission->description)
                                            <div class="text-muted small">{{ $permission->description }}</div>
                                        @endif
                                    </td>
                                    <td><code>{{ $permission->slug }}</code></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm w-100" role="group">
                                            @foreach(['' => 'Нет', 'own' => 'Свои', 'department' => 'Отдел', 'all' => 'Все'] as $scopeValue => $scopeLabel)
                                                @php $inputId = "perm_{$permission->id}_" . ($scopeValue ?: 'none'); @endphp
                                                <input type="radio"
                                                       class="btn-check scope-radio"
                                                       name="permissions[{{ $permission->id }}]"
                                                       id="{{ $inputId }}"
                                                       value="{{ $scopeValue }}"
                                                       {{ $currentScope === $scopeValue ? 'checked' : '' }}>
                                                <label class="btn {{ $scopeValue === '' ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                                                       for="{{ $inputId }}">
                                                    {{ $scopeLabel }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('roles.index') }}" class="btn btn-light border">Отмена</a>
            <button type="submit" class="btn btn-success px-4">Сохранить роль</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Массовое переключение прав для конкретной группы (модуля)
    document.querySelectorAll('[data-group][data-scope]').forEach(button => {
        button.addEventListener('click', () => {
            const group = button.dataset.group;
            const targetScope = button.dataset.scope;
            document.querySelectorAll(`tr[data-permission-group="${group}"] input.scope-radio[value="${targetScope}"]`)
                .forEach(radio => {
                    radio.checked = true;
                });
        });
    });

    // Массовое переключение всех прав на странице
    document.querySelectorAll('[data-set-all]').forEach(button => {
        button.addEventListener('click', () => {
            const targetScope = button.dataset.setAll;
            document.querySelectorAll(`input.scope-radio[value="${targetScope}"]`)
                .forEach(radio => {
                    radio.checked = true;
                });
        });
    });
});
</script>
@endsection