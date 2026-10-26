
@extends('orvanta.layouts.app')

@section('title', $deal->title . ' — Orvanta CRM')
@section('breadcrumb', 'Просмотр сделки')

@section('content')
    <div class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ ПРОДАЖАМИ</p>
                <h1>{{ $deal->title }}</h1>
                <p class="page-description">Подробная информация о сделке.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('deals.edit', $deal) }}" class="btn btn-success">Редактировать</a>
                <a href="{{ route('deals.index', ['pipeline' => $deal->pipeline_id]) }}" class="btn btn-outline-secondary">← К сделкам</a>
            </div>
        </div>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Информация о сделке</h2>
                    <p>Основные сведения и текущий статус.</p>
                </div>

                @if ($deal->stage?->type === 'won')
                    <span class="panel-badge">Успешно закрыта</span>
                @elseif ($deal->stage?->type === 'lost')
                    <span class="panel-badge">Закрыта без продажи</span>
                @else
                    <span class="panel-badge">В работе</span>
                @endif
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-1 text-muted small">Название сделки</div>
                        <div class="fw-semibold">{{ $deal->title }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1 text-muted small">Сумма сделки</div>
                        <div class="fw-bold fs-4" style="color: #16866a;">{{ number_format((float) $deal->amount, 2, ',', ' ') }} {{ $deal->currency }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1 text-muted small">Воронка продаж</div>
                        <div class="fw-semibold">{{ $deal->pipeline?->name ?? 'Не указана' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1 text-muted small">Этап сделки</div>
                        <div class="fw-semibold">{{ $deal->stage?->name ?? 'Не указан' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1 text-muted small">Ответственный сотрудник</div>
                        <div class="fw-semibold">{{ $deal->responsible?->name ?? 'Не назначен' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-1 text-muted small">Дата создания</div>
                        <div class="fw-semibold">{{ $deal->created_at?->format('d.m.Y H:i') ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="mb-2 text-muted small">Описание</div>
                        @if ($deal->description)
                            <div style="white-space: pre-wrap; overflow-wrap: anywhere;">{{ $deal->description }}</div>
                        @else
                            <p class="text-muted mb-0">Описание не добавлено.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
