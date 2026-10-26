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
                <p>Следующим этапом подключим настоящие данные и функциональность CRM.</p>
            </div>
        </section>
    </section>
@endsection
