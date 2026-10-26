<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" href="{{ asset('orvanta/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('orvanta/images/favicon/orvanta-favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('orvanta/images/favicon/orvanta-favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Orvanta — CRM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('orvanta/app.css') }}">
    <link rel="stylesheet" href="{{ asset('orvanta/preloader.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Start preloader -->
    <div class="orvanta-preloader" id="orvantaPreloader" aria-label="Загрузка Orvanta" role="status">
        <div class="preloader-backdrop"></div>
        <div class="preloader-content">
            <div class="preloader-logo" aria-hidden="true">
                <span>O</span>
            </div>
            <div class="preloader-brand">Orvanta</div>
            <p class="preloader-tagline">CRM</p>
            <p class="preloader-message">Рабочее пространство загружается</p>
            <div class="preloader-progress" role="progressbar" aria-label="Загрузка приложения" aria-valuemin="0" aria-valuemax="100">
                <div class="preloader-progress-bar"></div>
            </div>
            <span class="preloader-caption">Подготавливаем ваше рабочее пространство...</span>
            <div class="preloader-dots" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- End preloader -->


    <div class="crm-layout" id="crmLayout">
        @include('orvanta.partials.sidebar')
        <main class="main-area">
            <header class="topbar">
                <div class="breadcrumb">
                    <span>Рабочее пространство</span>
                    <span class="breadcrumb-separator">/</span>
                    <strong>@yield('breadcrumb', 'Обзор')</strong>
                </div>
                <div class="topbar-right">
                    <span class="system-status">
                        <span class="status-dot"></span>
                        Система готова
                    </span>
                </div>
            </header>
            @yield('content')
            @include('orvanta.partials.footer')
        </main>
    </div>
    <script src="{{ asset('orvanta/js/sidebar.js') }}" defer></script>
    <script src="{{ asset('orvanta/js/preloader.js') }}" defer></script>
</body>
</html>
