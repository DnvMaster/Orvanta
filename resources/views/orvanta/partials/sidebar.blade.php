<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-mark">O</span>
            <span class="brand-name">Orvanta</span>
        </a>
        <button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="{{ __('Свернуть меню') }}" title="{{ __('Свернуть меню') }}">
            <span>‹</span>
        </button>
    </div>
    <div class="sidebar-section-label">{{ __('Рабочее пространство') }}</div>
    <nav class="sidebar-nav" aria-label="{{ __('Главное меню') }}">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="{{ __('Обзор') }}">
            <span class="nav-icon">▦</span>
            <span class="nav-label">{{ __('Обзор') }}</span>
        </a>
        <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}" title="{{ __('Сотрудники') }}">
            <span class="nav-icon">♙</span>
            <span class="nav-label">{{ __('Сотрудники') }}</span>
        </a>
        @php
            $canViewCompanies = auth()->user()->can('viewAny', \App\Models\Company::class);
            $canViewContacts = auth()->user()->can('viewAny', \App\Models\Contact::class);
            $clientBaseActive = request()->routeIs('companies.*') || request()->routeIs('contacts.*');
        @endphp
        @if($canViewCompanies || $canViewContacts)
            <button type="button" class="nav-item nav-parent {{ $clientBaseActive ? 'active' : '' }}" id="clientBaseToggle" aria-expanded="true" aria-controls="clientBaseSubmenu" title="{{ __('Клиентская база') }}">
                <span class="nav-icon">▣</span>
                <span class="nav-label">{{ __('Клиентская база') }}</span>
                <span class="nav-arrow" aria-hidden="true">⌄</span>
            </button>
            <div class="nav-submenu" id="clientBaseSubmenu">
                @if($canViewCompanies)
                    <a href="{{ route('companies.index') }}" class="nav-item nav-subitem {{ request()->routeIs('companies.*') ? 'active' : '' }}" title="{{ __('Компании') }}" aria-current="{{ request()->routeIs('companies.*') ? 'page' : 'false' }}">
                        <span class="nav-icon">▣</span>
                        <span class="nav-label">{{ __('Компании') }}</span>
                    </a>
                @endif
                @if($canViewContacts)
                    <a href="{{ route('contacts.index') }}" class="nav-item nav-subitem {{ request()->routeIs('contacts.*') ? 'active' : '' }}" title="{{ __('Контакты') }}" aria-current="{{ request()->routeIs('contacts.*') ? 'page' : 'false' }}">
                        <span class="nav-icon">♧</span>
                        <span class="nav-label">{{ __('Контакты') }}</span>
                    </a>
                @endif
            </div>
        @endif
        <a href="{{ route('deals.index') }}" class="nav-item {{ request()->routeIs('deals.*') ? 'active' : '' }}" title="{{ __('Сделки') }}">
            <span class="nav-icon">◇</span>
            <span class="nav-label">{{ __('Сделки') }}</span>
        </a>
        @if(auth()->user()->can('tasks.view'))
            <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}" title="{{ __('Задачи') }}">
                <span class="nav-icon">☑</span>
                <span class="nav-label">{{ __('Задачи') }}</span>
            </a>
        @endif
        <a href="#" class="nav-item" title="{{ __('Отчёты') }}">
            <span class="nav-icon">▤</span>
            <span class="nav-label">{{ __('Отчёты') }}</span>
        </a>

    </nav>
    <div class="sidebar-bottom">
        <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="{{ __('Настройки профиля') }}" aria-current="{{ request()->routeIs('profile.*') ? 'page' : 'false' }}">
            <span class="nav-icon">⚙</span>
            <span class="nav-label">{{ __('Настройки') }}</span>
        </a>
        <div class="sidebar-user">
            @if(auth()->user()->avatar_path)
                <img class="user-avatar" src="{{ asset('storage/' . auth()->user()->avatar_path) }}" alt="{{ __('Фото пользователя :name', ['name' => auth()->user()->name]) }}" width="40" height="40">
            @else
                <div class="user-avatar" aria-label="{{ __('Фото отсутствует') }}">
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
                <span>{{ __('Выйти из аккаунта') }}</span>
            </button>
        </form>
    </div>
</aside>
