@extends('orvanta.layouts.app')

@section('title', 'Новый сотрудник — Orvanta CRM')

@section('breadcrumb')
    <a href="{{ route('employees.index') }}">Сотрудники</a>
    <span class="breadcrumb-separator">/</span>
    <strong>Новый сотрудник</strong>
@endsection

@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КОМАНДОЙ</p>
                <h1>Новый сотрудник</h1>
                <p class="page-description">
                    Создание учётной записи в рабочем пространстве Orvanta.
                </p>
            </div>
        </div>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Информация о сотруднике</h2>
                    <p>Заполните данные сотрудника. Пароль будет сгенерирован автоматически.</p>
                </div>
            </div>

            <form
                action="{{ route('employees.store') }}"
                method="POST"
                class="p-4"
                style="width: 100%; max-width: 688px;"
            >
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Проверьте введённые данные</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        Имя сотрудника <span class="text-danger">*</span>
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        maxlength="255"
                        required
                        autocomplete="name"
                        autofocus
                        class="form-control @error('name') is-invalid @enderror"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        Рабочий email <span class="text-danger">*</span>
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        required
                        autocomplete="email"
                        class="form-control @error('email') is-invalid @enderror"
                    >
                    <div class="form-text">
                        Используйте рабочий адрес электронной почты сотрудника.
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Телефон</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        maxlength="30"
                        autocomplete="tel"
                        class="form-control @error('phone') is-invalid @enderror"
                    >
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="position" class="form-label fw-semibold">Должность</label>
                    <input
                        id="position"
                        name="position"
                        type="text"
                        value="{{ old('position') }}"
                        maxlength="255"
                        class="form-control @error('position') is-invalid @enderror"
                    >
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="department" class="form-label fw-semibold">Отдел</label>
                    <input
                        id="department"
                        name="department"
                        type="text"
                        value="{{ old('department') }}"
                        maxlength="255"
                        class="form-control @error('department') is-invalid @enderror"
                    >
                    @error('department')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-light border mb-4" role="note">
                    <strong class="d-block mb-1">🔐 Безопасность учётной записи</strong>
                    <span class="text-secondary">
                        Система автоматически сгенерирует временный пароль после создания сотрудника.
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success">
                        Создать сотрудника
                    </button>

                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                        Отмена
                    </a>
                </div>
            </form>
        </section>
    </section>
@endsection