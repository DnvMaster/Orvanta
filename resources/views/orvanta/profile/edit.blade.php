@extends('orvanta.layouts.app')

@section('title', 'Настройки профиля — Orvanta')
@section('breadcrumb', 'Настройки профиля')

@section('content')
    @php
        $isAdmin = auth()->user()->isAdmin();
        $initials = collect(preg_split('/\s+/u', trim($employee->name)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    @endphp
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">ЛИЧНЫЙ КАБИНЕТ</p>
                <h1>Настройки профиля</h1>
                <p class="page-description">Управляйте личными данными и контактной информацией</p>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Готово!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-2">Не удалось сохранить изменения</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Карточка пользователя --}}
        <div class="panel mb-4">
            <div class="p-3 p-md-4">
                <div class="d-flex flex-column flex-sm-row align-items-center gap-3">
                    @if ($employee->avatar_path)
                        <img src="{{ asset('storage/' . $employee->avatar_path) }}" alt="Фотография пользователя" width="96" height="96" class="rounded-circle border object-fit-cover flex-shrink-0">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 96px; height: 96px; background: #e4f3ed; color: #167653; font-size: 30px; font-weight: 600;" role="img" aria-label="Инициалы пользователя: {{ $initials }}">{{ $initials ?: '?' }}</div>
                    @endif
                    <div class="text-center text-sm-start">
                        <h2 class="h4 mb-1">{{ $employee->name }}</h2>
                        <p class="text-secondary mb-2">
                            {{ $employee->position ?: 'Должность не указана' }}
                            @if ($employee->department)
                                <span aria-hidden="true"> · </span>
                                {{ $employee->department }}
                            @endif
                        </p>
                        <div class="d-flex flex-wrap justify-content-center justify-content-sm-start gap-2">
                            <span class="badge rounded-pill text-bg-light border">
                                {{ $isAdmin ? 'Администратор' : 'Сотрудник' }}
                            </span>
                            @if ($employee->isActive())
                                <span class="badge rounded-pill text-bg-success">Активен</span>
                            @else
                                <span class="badge rounded-pill text-bg-secondary">Не активен</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Личные данные --}}
            <div class="panel mb-4">
                <div class="panel-header">
                    <h2 class="h5 mb-1">Личные данные</h2>
                    <p class="text-secondary small mb-0">Основная информация вашей учётной записи</p>
                </div>
                <div class="card-body p-4">
                    @if ($isAdmin)
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя пользователя</label>
                            <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" maxlength="255" autocomplete="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Электронная почта</label>
                            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}" maxlength="255" autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Фотография профиля</label>
                            <input id="avatar" type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <div class="form-text">
                                Форматы JPEG, PNG или WebP. Максимальный размер — 2 МБ.
                            </div>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label" for="profile-name">Имя пользователя</label>
                            <input id="profile-name" type="text" class="form-control" value="{{ $employee->name }}" readonly aria-describedby="personal-data-note">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="profile-email">Электронная почта</label>
                            <input id="profile-email" type="email" class="form-control" value="{{ $employee->email }}" readonly aria-describedby="personal-data-note">
                        </div>
                        <p id="personal-data-note" class="form-text mb-0">Имя, электронную почту и фотографию может изменять только администратор.</p>
                    @endif
                </div>
            </div>

            {{-- Контактная информация --}}
            <div class="card-body p-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 mb-1">Контактная информация</h2>
                    <p class="text-secondary small mb-0">Данные, которые помогают команде взаимодействовать</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input id="phone" type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}" maxlength="30" autocomplete="tel" placeholder="+375(XX) XXX XX XX">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Должность</label>
                        <input id="position" type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $employee->position) }}" maxlength="255" autocomplete="organization-title" placeholder="Например, менеджер по продажам">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-1">
                        <label for="department" class="form-label">Отдел</label>
                        <input id="department" type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $employee->department) }}" maxlength="255" placeholder="Например, отдел продаж">
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <button type="submit" class="btn btn-success px-4">Сохранить изменения</button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Отмена</a>
            </div>

        </form>

        {{-- Смена пароля --}}
        <div class="panel mb-4">
            <div class="panel-header">
                <h2 class="h5 mb-1">Безопасность и пароль</h2>
                <p class="text-secondary small mb-0">
                    Для смены пароля сначала подтвердите текущий пароль
                </p>
            </div>

            <div class="p-3 p-md-4">
                @if (session('password_success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('password_success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
                    </div>
                @endif

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль</label>
                        <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" minlength="12" required>
                        <div class="form-text">Минимум 12 символов.</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Повторите новый пароль</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" minlength="12" required>
                    </div>
                    <button type="submit" class="btn btn-success px-4">Изменить пароль</button>
                </form>
            </div>
        </div>
    </section>
@endsection