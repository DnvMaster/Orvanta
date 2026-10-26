
@extends('orvanta.layouts.app')

@section('title', 'Новая сделка — Orvanta CRM')
@section('breadcrumb', 'Новая сделка')

@section('content')
    <div class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ ПРОДАЖАМИ</p>
                <h1>Новая сделка</h1>
                <p class="page-description">Заполните данные, чтобы добавить сделку в воронку продаж.</p>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">Проверьте заполнение полей формы.</div>
        @endif
        @if ($pipelines->isEmpty())
            <section class="panel">
                <div class="empty-state">
                    <div class="empty-state-icon">↗</div>
                    <h3>Нет активных воронок</h3>
                    <p>Чтобы создать сделку, сначала нужна активная воронка продаж.</p>
                    <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">Вернуться к сделкам</a>
                </div>
            </section>
        @else
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Данные сделки</h2>
                        <p>Поля со звёздочкой обязательны для заполнения.</p>
                    </div>
                </div>
                <form action="{{ route('deals.store') }}" method="POST" style="padding: 24px;">
                    @csrf
                    <div class="mb-3">
                        <label for="deal-title" class="form-label">Название сделки *</label>
                        <input type="text" id="deal-title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255" required autofocus>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="deal-amount" class="form-label">Сумма *</label>
                            <input type="number" id="deal-amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', '0.00') }}" min="0" max="9999999999999.99" step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deal-currency" class="form-label">Валюта *</label>
                            <select id="deal-currency" name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                <option value="BYB" @selected(old('currency', 'BYB') === 'BYB')>BYB — Белоруский рубль</option>
                                 <option value="RUB" @selected(old('currency') === 'RUB')>RUB — Российский рубль</option>
                                <option value="EUR" @selected(old('currency') === 'EUR')>EUR — Евро</option>
                                <option value="USD" @selected(old('currency') === 'USD')>USD — Доллар США</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deal-pipeline" class="form-label">Воронка *</label>
                        <select id="deal-pipeline" name="pipeline_id" class="form-select @error('pipeline_id') is-invalid @enderror" required onchange="window.location.href = '{{ route('deals.create') }}?pipeline=' + this.value">
                            @foreach ($pipelines as $pipeline)
                                <option value="{{ $pipeline->id }}" @selected((string) old('pipeline_id', $selectedPipeline?->id) === (string) $pipeline->id)>{{ $pipeline->name }}</option>
                            @endforeach
                        </select>
                        @error('pipeline_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="deal-stage" class="form-label">Этап воронки *</label>
                        <select id="deal-stage" name="pipeline_stage_id" class="form-select @error('pipeline_stage_id') is-invalid @enderror" required @disabled(!$selectedPipeline || $selectedPipeline->stages->where('type', 'open')->isEmpty())>
                            @if ($selectedPipeline)
                                @foreach ($selectedPipeline->stages->where('type', 'open') as $stage)
                                    <option value="{{ $stage->id }}" @selected((string) old('pipeline_stage_id', $selectedPipeline->stages->where('type', 'open')->first()?->id) === (string) $stage->id)>{{ $stage->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('pipeline_stage_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="deal-description" class="form-label">Описание</label>
                        <textarea id="deal-description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" maxlength="5000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-success" @disabled(!$selectedPipeline || $selectedPipeline->stages->where('type', 'open')->isEmpty())>Сохранить сделку</button>
                        <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </div>
                </form>
            </section>
        @endif
    </div>
@endsection
