
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход — Orvanta</title>
    @vite(['resources/css/app.css','resources/css/orvanta-auth.css','resources/js/app.js'])
</head>
<body>
    <main class="login-page">
        <section class="login-card">
            <div class="login-brand">Orvanta</div>

            <h1>Добро пожаловать</h1>
            <p class="login-description">
                Войдите в рабочее пространство вашей компании.
            </p>

            @if ($errors->any())
                <div class="login-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="login-field">
                    <label for="email">Рабочий email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="login-field">
                    <label for="password">Пароль</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button class="login-submit" type="submit">
                    Войти
                </button>
            </form>

            <div class="login-footer">
                Доступ предоставляется администратором
            </div>
        </section>
    </main>
</body>
</html>
