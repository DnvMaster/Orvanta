
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orvanta — CRM</title>
    <link rel="stylesheet" href="{{ asset('orvanta/app.css') }}">
</head>
<body>
    <div class="crm-layout" id="crmLayout">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/" class="brand">
                    <span class="brand-mark">O</span>
                    <span class="brand-name">Orvanta</span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Свернуть меню" title="Свернуть меню">
                    <span>‹</span>
                </button>
            </div>
            <div class="sidebar-section-label">РАБОЧЕЕ ПРОСТРАНСТВО</div>
            <nav class="sidebar-nav" aria-label="Главное меню">
                <a href="#" class="nav-item active" title="Обзор">
                    <span class="nav-icon">▦</span>
                    <span class="nav-label">Обзор</span>
                </a>
                <a href="#" class="nav-item" title="Клиенты">
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
                    <strong>Обзор</strong>
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
                        <p class="eyebrow">ПАНЕЛЬ УПРАВЛЕНИЯ</p>
                        <h1>Обзор</h1>
                        <p class="page-description">
                            Основные показатели вашего рабочего пространства.
                        </p>
                    </div>
                    <div class="page-date">Orvanta CRM</div>
                </div>

                <div class="stats-grid">
                    <article class="stat-card">
                        <div class="stat-card-top">
                            <span class="stat-label">Сотрудники</span>
                            <span class="stat-icon">♙</span>
                        </div>
                        <div class="stat-value">—</div>
                        <div class="stat-caption">Данные ещё не подключены</div>
                    </article>

                    <article class="stat-card">
                        <div class="stat-card-top">
                            <span class="stat-label">Сделки</span>
                            <span class="stat-icon">◇</span>
                        </div>
                        <div class="stat-value">—</div>
                        <div class="stat-caption">Данные ещё не подключены</div>
                    </article>

                    <article class="stat-card">
                        <div class="stat-card-top">
                            <span class="stat-label">Задачи</span>
                            <span class="stat-icon">☑</span>
                        </div>
                        <div class="stat-value">—</div>
                        <div class="stat-caption">Данные ещё не подключены</div>
                    </article>

                    <article class="stat-card">
                        <div class="stat-card-top">
                            <span class="stat-label">Выручка</span>
                            <span class="stat-icon">€</span>
                        </div>
                        <div class="stat-value">—</div>
                        <div class="stat-caption">Данные ещё не подключены</div>
                    </article>
                </div>

                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Рабочая область</h2>
                            <p>Здесь появятся последние события и данные CRM.</p>
                        </div>
                        <span class="panel-badge">Подготовка</span>
                    </div>
                    <div class="empty-state">
                        <div class="empty-state-icon">▦</div>
                        <h3>Рабочее пространство готово к настройке</h3>
                        <p>
                            Следующим этапом подключим настоящие данные
                            и функциональность CRM.
                        </p>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <script>
        const crmLayout = document.getElementById('crmLayout');
        const sidebarToggle = document.getElementById('sidebarToggle');
        sidebarToggle.addEventListener('click', function () {
            const isCollapsed = crmLayout.classList.toggle('sidebar-collapsed');
            sidebarToggle.setAttribute('aria-label', isCollapsed ? 'Развернуть меню' : 'Свернуть меню');
            sidebarToggle.setAttribute('title', isCollapsed ? 'Развернуть меню' : 'Свернуть меню');
        });
    </script>

</body>
</html>
