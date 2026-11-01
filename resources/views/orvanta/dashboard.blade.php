@extends('orvanta.layouts.app')
@section('title', 'Обзор — Orvanta CRM')
@section('breadcrumb', 'Обзор')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">ПАНЕЛЬ УПРАВЛЕНИЯ</p>
                <h1>Обзор</h1>
                <p class="page-description">Основные показатели вашего рабочего пространства.</p>
            </div>
            <div class="page-date">Orvanta CRM</div>
        </div>
        <div class="stats-grid">
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Сотрудники</span>
                    <span class="stat-icon">♙</span>
                </div>
                <div class="stat-value">{{ $employeesCount ?? '—' }}</div>
                <div class="stat-caption">{{ $employeesCount !== null ? 'Активные сотрудники' : 'Нет доступа к данным' }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Компании</span>
                    <span class="stat-icon">▣</span>
                </div>
                <div class="stat-value">{{ $companiesCount ?? '—' }}</div>
                <div class="stat-caption">{{ $companiesCount !== null ? 'Доступные компании' : 'Нет доступа к данным' }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Контакты</span>
                    <span class="stat-icon">♧</span>
                </div>
                <div class="stat-value">{{ $contactsCount ?? '—' }}</div>
                <div class="stat-caption">{{ $contactsCount !== null ? 'Доступные контакты' : 'Нет доступа к данным' }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-card-top">
                    <span class="stat-label">Сделки</span>
                    <span class="stat-icon">◇</span>
                </div>
                <div class="stat-value">{{ $dealsCount ?? '—' }}</div>
                <div class="stat-caption">{{ $dealsCount !== null ? 'Доступные сделки' : 'Нет доступа к данным' }}</div>
            </article>
        </div>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Рабочая область</h2>
                    <p>Состояние сделок в вашем рабочем пространстве.</p>
                </div>
                <span class="panel-badge">В работе</span>
            </div>
            <div class="empty-state">
                <div class="empty-state-icon">◇</div>
                <h3>Открытые сделки: {{ $openDealsCount ?? '—' }}</h3>
                @if ($openDealsCount === null)
                    <p>Нет доступа к данным о сделках.</p>
                @elseif ($openDealAmounts === [])
                    <p>Пока нет открытых сделок.</p>
                @else
                    <p>Общая сумма открытых сделок по валютам:</p>
                    @foreach ($openDealAmounts as $amount)
                        <p>{{ number_format((float) $amount['total'], 2, ',', ' ') }} {{ $amount['currency'] ?? 'Без валюты' }}</p>
                    @endforeach
                @endif
            </div>
        </section>
    </section>
@endsection