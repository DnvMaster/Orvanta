@extends('orvanta.layouts.app')

@section('title', 'Сделки — Orvanta CRM')
@section('breadcrumb', 'Сделки')

@section('content')
    <div class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ ПРОДАЖАМИ</p>
                <h1>Сделки</h1>
                <p class="page-description">
                    Отслеживайте ход продаж и управляйте сделками по этапам воронки.
                </p>
            </div>

            <a href="{{ route('deals.create') }}" class="btn btn-success">
                <span aria-hidden="true">＋</span>
                Новая сделка
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Воронка продаж</h2>
                    <p>Выберите воронку, чтобы просмотреть её этапы.</p>
                </div>

                @if ($pipelines->isNotEmpty())
                    <span class="panel-badge">
                        {{ $pipelines->count() }} воронок
                    </span>
                @endif
            </div>

            <div style="padding: 20px 24px;">
                @if ($pipelines->isNotEmpty())
                    <label
                        for="pipeline-select"
                        style="display: block; margin-bottom: 8px; font-weight: 600;"
                    >
                        Активная воронка
                    </label>

                    <select
                        id="pipeline-select"
                        class="form-select"
                        style="max-width: 360px;"
                        onchange="if (this.value) window.location.href = this.value;"
                    >
                        @foreach ($pipelines as $pipeline)
                            <option
                                value="{{ route('deals.index', ['pipeline' => $pipeline->id]) }}"
                                @selected($selectedPipeline?->id === $pipeline->id)
                            >
                                {{ $pipeline->name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <p class="page-description">
                        Пока нет активных воронок продаж.
                    </p>
                @endif
            </div>
        </section>

        @if ($selectedPipeline)
            <section style="margin-top: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
                    <h2 style="margin: 0; font-size: 16px; color: var(--orvanta-graphite);">
                        {{ $selectedPipeline->name }}
                    </h2>

                    <span class="panel-badge">
                        {{ $selectedPipeline->stages->count() }} этапов
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(5, minmax(220px, 1fr)); gap: 14px; overflow-x: auto; padding-bottom: 12px;">
                    @foreach ($selectedPipeline->stages as $stage)
                        @php
                            $stageDeals = $deals->where(
                                'pipeline_stage_id',
                                $stage->id
                            );
                        @endphp

                        <div style="min-width: 220px; border: 1px solid var(--orvanta-border); border-radius: 9px; background: #eef2ef; overflow: hidden;">
                            <div style="padding: 15px; background: white; border-bottom: 1px solid var(--orvanta-border);">
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
                                    <strong style="font-size: 12px; line-height: 1.5; color: var(--orvanta-graphite);">
                                        {{ $stage->name }}
                                    </strong>

                                    <span style="font-size: 11px; color: var(--orvanta-muted);">
                                        {{ $stageDeals->count() }}
                                    </span>
                                </div>

                                @if ($stage->type === 'won')
                                    <span style="display: inline-block; margin-top: 8px; color: #16866a; font-size: 10px; font-weight: 600;">
                                        Успешно
                                    </span>
                                @elseif ($stage->type === 'lost')
                                    <span style="display: inline-block; margin-top: 8px; color: #b45353; font-size: 10px; font-weight: 600;">
                                        Без продажи
                                    </span>
                                @endif
                            </div>

                            <div style="min-height: 180px; padding: 12px;">
                                @forelse ($stageDeals as $deal)
                                    <a
                                        href="{{ route('deals.show', ['deal' => $deal->id]) }}"
                                        aria-label="Подробнее о сделке: {{ $deal->title }}"
                                        style="display: block; position: relative; z-index: 1; cursor: pointer; margin-bottom: 10px; padding: 14px; background: #fff; border: 1px solid var(--orvanta-border); border-radius: 8px; text-decoration: none; color: inherit; transition: border-color 0.2s, box-shadow 0.2s;"
                                    >
                                        <h3 style="margin: 0 0 12px; font-size: 13px; line-height: 1.5; color: var(--orvanta-graphite); overflow-wrap: anywhere;">
                                            {{ $deal->title }}
                                        </h3>

                                        <div style="margin-bottom: 10px; font-size: 14px; font-weight: 700; color: #16866a;">
                                            {{ number_format((float) $deal->amount, 2, ',', ' ') }}
                                            {{ $deal->currency }}
                                        </div>

                                        @if ($deal->responsible)
                                            <div style="font-size: 11px; color: var(--orvanta-muted);">
                                                Ответственный: {{ $deal->responsible->name }}
                                            </div>
                                        @endif

                                        @if ($deal->description)
                                            <p style="margin: 10px 0 0; font-size: 11px; line-height: 1.5; color: var(--orvanta-muted); overflow-wrap: anywhere;">
                                                {{ $deal->description }}
                                            </p>
                                        @endif

                                        <div style="margin-top: 12px; font-size: 11px; font-weight: 600; color: #16866a;">
                                            Подробнее о сделке →
                                        </div>
                                    </a>
                                @empty
                                    <div style="padding: 20px 10px; text-align: center; color: var(--orvanta-muted); font-size: 11px; line-height: 1.6;">
                                        Сделок пока нет
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @else
            <section class="panel" style="margin-top: 24px;">
                <div class="empty-state">
                    <div class="empty-state-icon">↗</div>
                    <h3>Нет активных воронок</h3>
                    <p>
                        Создайте воронку продаж, чтобы начать организовывать
                        сделки по этапам.
                    </p>
                </div>
            </section>
        @endif
    </div>
@endsection
