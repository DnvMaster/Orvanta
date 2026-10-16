
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый сотрудник — Orvanta CRM</title>
    <link rel="stylesheet" href="{{ asset('orvanta/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="crm-layout" id="crmLayout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="brand">
                    <span class="brand-mark">O</span>
                    <span class="brand-name">Orvanta</span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle"
                        type="button" aria-label="Свернуть меню"
                        title="Свернуть меню">
                    <span>‹</span>
                </button>
            </div>

            <div class="sidebar-section-label">РАБОЧЕЕ ПРОСТРАНСТВО</div>

            <nav class="sidebar-nav" aria-label="Главное меню">
                <a href="{{ route('dashboard') }}" class="nav-item" title="Обзор">
                    <span class="nav-icon">▦</span>
                    <span class="nav-label">Обзор</span>
                </a>
                <a href="{{ route('employees.index') }}" class="nav-item active" title="Сотрудники">
                    <span class="nav-icon">♙</span>
                    <span class="nav-label">Сотрудники</span>
                </a>
                <a href="#" class="nav-item" title="Сделки">
                    <span class="nav-icon">◇</span>
                    <span class="nav-label">Сделки</span>
                </a>
                <a href="#" class="nav-item" title="Задачи">
                    <span class="nav-icon">☑</span>
                    <span class="nav-label">Задачи</span>
                </a>
                <a href="#" class="nav-item" title="Отчёты">
                    <span class="nav-icon">▤</span>
                    <span class="nav-label">Отчёты</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <a href="#" class="nav-item" title="Настройки">
                    <span class="nav-icon">⚙</span>
                    <span class="nav-label">Настройки</span>
                </a>

                <div class="sidebar-user">
                    <div class="user-avatar">O</div>
                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role">{{ auth()->user()->email }}</span>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
                    @csrf
                    <button type="submit" class="sidebar-logout-button">
                        <span aria-hidden="true">↪</span>
                        <span>Выйти из аккаунта</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <div class="breadcrumb">
                    <span>Рабочее пространство</span>
                    <span class="breadcrumb-separator">/</span>
                    <a href="{{ route('employees.index') }}">Сотрудники</a>
                    <span class="breadcrumb-separator">/</span>
                    <strong>Новый сотрудник</strong>
                </div>
                <div class="topbar-right">
                    <span class="system-status">
                        <span class="status-dot"></span>
                        Система готова
                    </span>
                </div>
            </header>

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
                            <p>Пароль будет сгенерирован автоматически.</p>
                        </div>
                    </div>

<form action="{{ route('employees.store') }}" method="POST"
      class="p-3 p-md-4"
      style="max-width: 900px;">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <h6 class="alert-heading mb-2">Проверьте введённые данные</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="name" class="form-label">
                Имя сотрудника <span class="text-danger">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                maxlength="255"
                required
                autocomplete="name"
                autofocus
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="email" class="form-label">
                Рабочий email <span class="text-danger">*</span>
            </label>
            <input
                id="email"
                name="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                maxlength="255"
                required
                autocomplete="email"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="phone" class="form-label">Телефон</label>
            <input
                id="phone"
                name="phone"
                type="tel"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone') }}"
                maxlength="30"
                autocomplete="tel"
            >
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="position" class="form-label">Должность</label>
            <input
                id="position"
                name="position"
                type="text"
                class="form-control @error('position') is-invalid @enderror"
                value="{{ old('position') }}"
                maxlength="255"
            >
            @error('position')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="department" class="form-label">Отдел</label>
            <input
                id="department"
                name="department"
                type="text"
                class="form-control @error('department') is-invalid @enderror"
                value="{{ old('department') }}"
                maxlength="255"
            >
            @error('department')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="alert alert-light border d-flex align-items-start gap-2 mt-4 mb-0">
        <span aria-hidden="true">🔐</span>
        <div>
            <div class="fw-semibold">Безопасность учётной записи</div>
            <div class="text-secondary small">
                Система автоматически сгенерирует временный пароль.
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
        <button type="submit" class="btn btn-success px-4">
            Создать сотрудника
        </button>

        <a href="{{ route('employees.index') }}"
           class="btn btn-outline-secondary px-4">
            Отмена
        </a>
    </div>
</form>

                </section>
            </section>
        </main>
    </div>

    <script>
        const crmLayout = document.getElementById('crmLayout');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', function () {
            const isCollapsed = crmLayout.classList.toggle('sidebar-collapsed');
            sidebarToggle.setAttribute(
                'aria-label',
                isCollapsed ? 'Развернуть меню' : 'Свернуть меню'
            );
            sidebarToggle.setAttribute(
                'title',
                isCollapsed ? 'Развернуть меню' : 'Свернуть меню'
            );
        });
    </script>
</body>
</html>
