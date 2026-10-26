@extends('orvanta.layouts.app')

@section('title', 'Редактирование сотрудника — Orvanta CRM')

@section('breadcrumb')
    <a href="{{ route('employees.index') }}">Сотрудники</a>
    <span class="breadcrumb-separator">/</span>
    <strong>Редактирование сотрудника</strong>
@endsection

@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КОМАНДОЙ</p>
                <h1>Редактирование сотрудника</h1>
                <p class="page-description">
                    Обновление информации о сотруднике Orvanta.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Информация о сотруднике</h2>
                    <p>Измените необходимые данные и сохраните результат.</p>
                </div>
            </div>

            <form
                action="{{ route('employees.update', $employee) }}"
                method="POST"
                enctype="multipart/form-data"
                class="p-4"
                style="width: 100%; max-width: 688px;"
            >
                @csrf
                @method('PUT')

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

                @if (auth()->user()->isAdmin())
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Имя сотрудника <span class="text-danger">*</span>
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $employee->name) }}"
                            maxlength="255"
                            required
                            autocomplete="name"
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
                            value="{{ old('email', $employee->email) }}"
                            maxlength="255"
                            required
                            autocomplete="email"
                            class="form-control @error('email') is-invalid @enderror"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="avatar" class="form-label fw-semibold">
                            Фотография сотрудника
                        </label>

                        @if ($employee->avatar_path)
                            <div class="mb-3">
                                <img
                                    src="{{ asset('storage/' . $employee->avatar_path) }}"
                                    alt="Фотография сотрудника {{ $employee->name }}"
                                    width="88"
                                    height="88"
                                    class="rounded-circle object-fit-cover"
                                >
                            </div>
                        @endif

                        <input
                            id="avatar"
                            name="avatar"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="form-control @error('avatar') is-invalid @enderror"
                        >

                        <div class="form-text">
                            JPEG, PNG или WebP. Максимальный размер — 2 МБ.
                        </div>

                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Телефон</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone', $employee->phone) }}"
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
                        value="{{ old('position', $employee->position) }}"
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
                        value="{{ old('department', $employee->department) }}"
                        maxlength="255"
                        class="form-control @error('department') is-invalid @enderror"
                    >
                    @error('department')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="btn btn-success">
                        Сохранить изменения
                    </button>

                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                        Отмена
                    </a>
                </div>
            </form>
        </section>
    </section>
@endsection