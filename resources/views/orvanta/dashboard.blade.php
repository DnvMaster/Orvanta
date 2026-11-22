@extends('orvanta.layouts.app')
@section('title', __('Обзор'). ' — Orvanta CRM')
@section('breadcrumb', __('Обзор'))
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('ПАНЕЛЬ УПРАВЛЕНИЯ') }}</p>
                <h1>{{ __('Обзор') }}</h1>
                <p class="page-description">{{ __('Основные показатели вашего рабочего пространства.') }}</p>
            </div>
            <div class="page-date">Orvanta CRM</div>
        </div>
        <div class="stats-grid">
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('Сотрудники') }}</span>
                    <span class="stat-icon">♙</span>
                </div>
                <div class="stat-value">{{ $employeesCount ?? '—' }}</div>
                <div class="stat-caption">{{ $employeesCount !== null ? __('Активные сотрудники') : __('Нет доступа к данным') }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('Компании') }}</span>
                    <span class="stat-icon">▣</span>
                </div>
                <div class="stat-value">{{ $companiesCount ?? '—' }}</div>
                <div class="stat-caption">{{ $companiesCount !== null ? __('Доступные компании') : __('Нет доступа к данным') }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('Контакты') }}</span>
                    <span class="stat-icon">♧</span>
                </div>
                <div class="stat-value">{{ $contactsCount ?? '—' }}</div>
                <div class="stat-caption">{{ $contactsCount !== null ? __('Доступные контакты') : __('Нет доступа к данным') }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">{{ __('Сделки') }}</span>
                    <span class="stat-icon">◇</span>
                </div>
                <div class="stat-value">{{ $dealsCount ?? '—' }}</div>
                <div class="stat-caption">{{ $dealsCount !== null ? __('Доступные сделки') : __('Нет доступа к данным') }}</div>
            </article>
        </div>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>{{ __('Рабочая область') }}</h2>
                    <p>{{ __('Состояние сделок в вашем рабочем пространстве.') }}</p>
                </div>
                <span class="panel-badge">{{ __('В работе') }}</span>
            </div>
            <div class="empty-state">
                <div class="empty-state-icon">◇</div>
                <h3>{{ __('Открытые сделки: :count', ['count' => $openDealsCount ?? '—']) }}</h3>
                @if ($openDealsCount === null)
                    <p>{{ __('Нет доступа к данным о сделках.') }}</p>
                @elseif ($openDealAmounts === [])
                    <p>{{ __('Пока нет открытых сделок.') }}</p>
                @else
                    <p>{{ __('Общая сумма открытых сделок по валютам:') }}</p>
                    @foreach ($openDealAmounts as $amount)
                        <p>{{ number_format((float) $amount['total'], 2, ',', ' ') }} {{ $amount['currency'] ?? __('Без валюты') }}</p>
                    @endforeach
                @endif
            </div>
        </section>
    </section>
@endsection
