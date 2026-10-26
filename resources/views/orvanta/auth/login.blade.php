<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" href="{{ asset('orvanta/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('orvanta/images/favicon/orvanta-favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('orvanta/images/favicon/orvanta-favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход — Orvanta CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css','resources/css/orvanta-auth.css','resources/js/app.js','resources/js/orvanta-auth.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <header class="text-center mb-4">
                <h1 class="h3 fw-semibold mt-4 mb-2">Добро пожаловать</h1>
                <p class="login-description mb-0">Войдите в рабочее пространство вашей компании.</p>
            </header>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>
                    {{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-medium">Рабочий email</label>
                    <div class="input-group login-input-group">
                        <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="name@company.com" autocomplete="username" aria-describedby="email-help" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-medium">Пароль</label>
                    <div class="input-group login-input-group">
                        <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Введите пароль" autocomplete="current-password" required>
                        <button class="btn btn-outline-secondary login-password-toggle" type="button" id="togglePassword" aria-label="Показать пароль" aria-controls="password" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-success login-submit w-100" type="submit">Войти<i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button>
            </form>

            <footer class="login-footer text-center mt-4 pt-3">
                <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                Доступ предоставляется администратором
            </footer>
        </section>
    </main>
</body>
</html>