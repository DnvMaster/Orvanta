<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-mark">O</span>
            <span class="brand-name">Orvanta</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Свернуть меню" title="Свернуть меню">
            <span>‹</span>
        </button>
    </div>
    <div class="sidebar-section-label">РАБОЧЕЕ ПРОСТРАНСТВО</div>
    <nav class="sidebar-nav" aria-label="Главное меню">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Обзор">
            <span class="nav-icon">▦</span>
            <span class="nav-label">Обзор</span>
        </a>
        <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}" title="Сотрудники">
            <span class="nav-icon">♙</span>
            <span class="nav-label">Сотрудники</span>
        </a>
        <a href="{{ route('companies.index') }}"
           class="nav-item {{ request()->routeIs('companies.*') ? 'active' : '' }}"
           title="Компании">
            <span class="nav-icon">▣</span>
            <span class="nav-label">Компании</span>
        </a>
        <a href="{{ route('deals.index') }}" class="nav-item {{ request()->routeIs('deals.*') ? 'active' : '' }}" title="Сделки">
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
        <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Настройки профиля" aria-current="{{ request()->routeIs('profile.*') ? 'page' : 'false' }}">
            <span class="nav-icon">⚙</span>
            <span class="nav-label">Настройки</span>
        </a>
        <div class="sidebar-user">
            @if(auth()->user()->avatar_path)
                <img class="user-avatar" src="{{ asset('storage/' . auth()->user()->avatar_path) }}" alt="Фото пользователя {{ auth()->user()->name }}" width="40" height="40">
            @else
                <div class="user-avatar" aria-label="Фото отсутствует">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
            @endif
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
