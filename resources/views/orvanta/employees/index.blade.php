
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сотрудники — Orvanta CRM</title>
    <link rel="stylesheet" href="{{ asset('orvanta/app.css') }}">
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
                    <strong>Сотрудники</strong>
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
                        <h1>Сотрудники</h1>
                        <p class="page-description">
                            Учётные записи и информация о команде Orvanta.
                        </p>
                    </div>
                    <div class="page-date">
                        Всего: {{ $employees->total() }}
                    </div>
                </div>


            <section class="panel">
                @if (session('success'))
                    <div role="status"
                        style="margin: 20px 24px 0; padding: 14px 16px; border: 1px solid #a7dfc8; border-radius: 10px; background: #effaf5; color: #176b4d;">
                        <strong>{{ session('success') }}</strong>

                        @if (session('created_employee_email'))
                            <p style="margin: 6px 0 0;">
                                Email сотрудника: {{ session('created_employee_email') }}
                            </p>
                        @endif

                        @if (session('temporary_password'))
                            <div style="margin-top: 12px;">
                                <strong>Временный пароль:</strong>
                                <code style="display: inline-block; margin-left: 6px; padding: 5px 8px; border-radius: 5px; background: #fff; color: #176b4d;">{{ session('temporary_password') }}</code>
                                <p style="margin: 8px 0 0; font-size: 13px;">
                                    Сохраните пароль и передайте его сотруднику безопасным способом.
                                    После закрытия этой страницы повторно показать его не получится.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="panel-header">
                    <div>
                        <h2>Список сотрудников</h2>
                        <p>Сотрудники, зарегистрированные в рабочем пространстве.</p>
                    </div>
                    <span class="panel-badge">Всего: {{ $employees->total() }}</span>
                </div>

                @if ($employees->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">♙</div>
                        <h3>Сотрудников пока нет</h3>
                        <p>Когда появятся учётные записи, они отобразятся здесь.</p>
                    </div>
                @else
                    <div style="overflow-x: auto; padding: 0 24px 20px;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr>
                                    <th style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">Имя</th>
                                    <th style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">Email</th>
                                    <th style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">Должность</th>
                                    <th style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">Отдел</th>
                                    <th style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">Роль</th>
                                    <th style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">
                                            {{ $employee->name }}
                                        </td>
                                        <td style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">
                                            {{ $employee->email }}
                                        </td>
                                        <td style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">
                                            {{ $employee->position ?: '—' }}
                                        </td>
                                        <td style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">
                                            {{ $employee->department ?: '—' }}
                                        </td>
                                        <td style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">
                                            {{ $employee->role === 'admin' ? 'Администратор' : 'Сотрудник' }}
                                        </td>
                                        <td style="padding: 14px 10px; border-bottom: 1px solid var(--orvanta-border);">
                                            {{ $employee->status === 'active' ? 'Активен' : 'Заблокирован' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="padding: 0 24px 24px;">
                        {{ $employees->links() }}
                    </div>
                @endif
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
                'title', isCollapsed ? 'Развернуть меню' : 'Свернуть меню'
            );
        });
    </script>
</body>
</html>
