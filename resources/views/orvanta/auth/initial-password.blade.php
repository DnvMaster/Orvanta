
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" href="{{ asset('orvanta/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('orvanta/images/favicon/orvanta-favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('orvanta/images/favicon/orvanta-favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Смена первоначального пароля — Orvanta CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/orvanta-auth.css', 'resources/js/app.js', 'resources/js/orvanta-auth.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <header class="text-center mb-4">
                <div class="login-brand mb-4">
                    <span class="login-brand-mark">O</span>
                    <span>Orvanta</span>
                </div>
                <h1 class="h3 fw-semibold mb-2">Создайте новый пароль</h1>
                <p class="login-description mb-0">Для продолжения работы необходимо изменить первоначальный пароль.</p>
            </header>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>{{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.initial.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-medium">Первоначальный пароль</label>
                    <div class="input-group login-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock" aria-hidden="true"></i>
                        </span>
                        <input id="current_password" type="password" name="current_password" class="form-control" placeholder="Введите первоначальный пароль" autocomplete="current-password" aria-controls="toggleCurrentPassword" required autofocus>
                        <button class="btn btn-outline-secondary login-password-toggle" type="button" id="toggleCurrentPassword" aria-label="Показать пароль" aria-controls="current_password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-medium"> Новый пароль</label>
                    <div class="input-group login-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-shield-lock" aria-hidden="true"></i>
                        </span>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Придумайте новый пароль" autocomplete="new-password" required>
                        <button class="btn btn-outline-secondary login-password-toggle" type="button" aria-label="Показать пароль" aria-controls="password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="form-text mt-2">Пароль должен содержать не менее 12 символов.</div>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-medium">Подтвердите новый пароль</label>
                    <div class="input-group login-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Повторите новый пароль" autocomplete="new-password" required>
                        <button class="btn btn-outline-secondary login-password-toggle" type="button" aria-label="Показать пароль" aria-controls="password_confirmation" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button class="btn btn-success login-submit w-100" type="submit">Сохранить новый пароль
                    <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                </button>
            </form>
            <footer class="login-footer text-center mt-4 pt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none p-0">
                        <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Выйти из учётной записи
                    </button>
                </form>
                <div class="mt-2">
                    Безопасность вашего рабочего пространства Orvanta
                </div>
            </footer>
        </section>
    </main>
</body>
</html>
