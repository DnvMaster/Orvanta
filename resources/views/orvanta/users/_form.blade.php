@extends('orvanta.layouts.app')
@section('title', $user->exists ? __('Редактирование сотрудника: :name', ['name' => $user->name]) . ' — Orvanta CRM' : __('Новый сотрудник') . ' — Orvanta CRM')
@section('breadcrumb', __('Сотрудники'))
@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ $user->exists ? __('Редактирование сотрудника: :name', ['name' => $user->name]) : __('Новый сотрудник') }}</h1>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">{{ __('← Назад к списку') }}</a>
        </div>
        <form action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" method="POST">
            @csrf
            @if ($user->exists)
                @method('PUT')
            @endif
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-semibold">{{ __('Учетные данные и контакты') }}</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">{{ __('ФИО') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('Email (логин)') }} <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">{{ __('Телефон') }}</label>
                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">{{ __('Статус учетной записи') }}</label>
                                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="active" @selected(old('status', $user->status) === 'active')>{{ __('Активен') }}</option>
                                        <option value="inactive" @selected(old('status', $user->status) === 'inactive')>{{ __('Заблокирован (неактивен)') }}</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('Пароль') }}
                                        @if ($user->exists)
                                            <span class="text-muted small">({{ __('оставьте пустым, чтобы не менять') }})</span>
                                        @else
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"{{ $user->exists ? '' : 'required' }}>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">{{ __('Подтверждение пароля') }}</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-semibold">{{ __('Организационная структура') }}</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="position" class="form-label">{{ __('Должность') }}</label>
                                    <input type="text" id="position" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $user->position) }}" placeholder="{{ __('Менеджер по продажам') }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="department_id" class="form-label">{{ __('Подразделение (отдел)') }}</label>
                                    <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                        <option value="">{{ __('— Не назначен —') }}</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" @selected(old('department_id', $user->department_id) == $dept->id)>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="manager_id" class="form-label">{{ __('Непосредственный руководитель') }}</label>
                                    <select id="manager_id" name="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                                        <option value="">{{ __('— Нет руководителя —') }}</option>
                                        @foreach ($managers as $mgr)
                                            <option value="{{ $mgr->id }}" @selected(old('manager_id', $user->manager_id) == $mgr->id)>{{ $mgr->name }} {{ $mgr->position ? "({$mgr->position})" : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-semibold">{{ __('Роли доступа (RBAC)') }}</div>
                        <div class="card-body">
                            <p class="text-muted small">{{ __('Сотруднику можно назначить одну или несколько ролей. Итоговые права вычисляются по приоритету: all → department → own.') }}</p>
                            @php
                                $selectedRoles = old('roles', $assignedRoleIds);
                            @endphp
                            @forelse ($roles as $role)
                                <div class="form-check mb-2 p-2 border rounded">
                                    <input class="form-check-input ms-1" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"@checked(in_array($role->id, $selectedRoles))>
                                    <label class="form-check-label ms-2 d-block" for="role_{{ $role->id }}">
                                        <span class="fw-semibold">{{ $role->name }}</span>
                                        <code class="small ms-1">{{ $role->slug }}</code>
                                        @if ($role->description)
                                            <div class="text-muted small">{{ $role->description }}</div>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted small">{{ __('Роли еще не созданы.') }}</div>
                            @endforelse
                            @error('roles')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-light border">{{ __('Отмена') }}</a>
                <button type="submit" class="btn btn-success px-4">{{ __('Сохранить сотрудника') }}</button>
            </div>
        </form>
    </div>
@endsection
