@extends('orvanta.layouts.app')

@section('title', 'Редактирование сделки — Orvanta CRM')
@section('breadcrumb', 'Редактирование сделки')

@section('content')
    <div class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ ПРОДАЖАМИ</p>
                <h1>Редактирование сделки</h1>
                <p class="page-description">Измените данные сделки и сохраните результат.</p>
            </div>
            <a href="{{ route('deals.show', $deal) }}" class="btn btn-outline-secondary">
                ← К сделке
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                Проверьте заполнение полей формы.
            </div>
        @endif

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Данные сделки</h2>
                    <p>Поля со звёздочкой обязательны для заполнения.</p>
                </div>
            </div>

            <form action="{{ route('deals.update', $deal) }}" method="POST" style="padding: 24px;">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="deal-title" class="form-label">Название сделки *</label>
                    <input
                        type="text"
                        id="deal-title"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $deal->title) }}"
                        maxlength="255"
                        required
                        autofocus
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
    <div class="col-md-6 mb-3">
        <label for="deal-company" class="form-label">Компания</label>
        <select
            id="deal-company"
            name="company_id"
            class="form-select @error('company_id') is-invalid @enderror"
        >
            <option value="">Без компании</option>

            @foreach ($companies as $company)
                <option
                    value="{{ $company->id }}"
                    @selected((string) old('company_id', $deal->company_id) === (string) $company->id)
                >
                    {{ $company->name }}
                </option>
            @endforeach
        </select>

        @error('company_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="deal-contact" class="form-label">Контакт</label>
        <select
            id="deal-contact"
            name="contact_id"
            class="form-select @error('contact_id') is-invalid @enderror"
        >
            <option value="">Без контакта</option>

            @foreach ($contacts as $contact)
                <option
                    value="{{ $contact->id }}"
                    @selected((string) old('contact_id', $deal->contact_id) === (string) $contact->id)
                >
                    {{ $contact->last_name }} {{ $contact->first_name }}
                    @if ($contact->company)
                        — {{ $contact->company->name }}
                    @endif
                </option>
            @endforeach
        </select>

        @error('contact_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="deal-amount" class="form-label">Сумма *</label>
                        <input
                            type="number"
                            id="deal-amount"
                            name="amount"
                            class="form-control @error('amount') is-invalid @enderror"
                            value="{{ old('amount', $deal->amount) }}"
                            min="0"
                            max="9999999999999.99"
                            step="0.01"
                            required
                        >
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="deal-currency" class="form-label">Валюта *</label>
                        <select
                            id="deal-currency"
                            name="currency"
                            class="form-select @error('currency') is-invalid @enderror"
                            required
                        >
                            @foreach ([
                                'RUB' => 'RUB — Российский рубль',
                                'EUR' => 'EUR — Евро',
                                'USD' => 'USD — Доллар США',
                                'BYB' => 'BYB — Белорусский рубль',
                            ] as $code => $label)
                                <option
                                    value="{{ $code }}"
                                    @selected(old('currency', $deal->currency) === $code)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deal-pipeline" class="form-label">Воронка *</label>
                    <select
                        id="deal-pipeline"
                        name="pipeline_id"
                        class="form-select @error('pipeline_id') is-invalid @enderror"
                        required
                    >
                        @foreach ($pipelines as $pipeline)
                            <option
                                value="{{ $pipeline->id }}"
                                @selected((string) old('pipeline_id', $deal->pipeline_id) === (string) $pipeline->id)
                            >
                                {{ $pipeline->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pipeline_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deal-stage" class="form-label">Этап воронки *</label>
                    <select
                        id="deal-stage"
                        name="pipeline_stage_id"
                        class="form-select @error('pipeline_stage_id') is-invalid @enderror"
                        required
                    >
                        @foreach ($stages as $stage)
                            <option
                                value="{{ $stage->id }}"
                                @selected((string) old('pipeline_stage_id', $deal->pipeline_stage_id) === (string) $stage->id)
                            >
                                {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pipeline_stage_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deal-responsible" class="form-label">Ответственный сотрудник *</label>
                    <select
                        id="deal-responsible"
                        name="user_id"
                        class="form-select @error('user_id') is-invalid @enderror"
                        required
                    >
                        @foreach ($users as $user)
                            <option
                                value="{{ $user->id }}"
                                @selected((string) old('user_id', $deal->user_id) === (string) $user->id)
                            >
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="deal-description" class="form-label">Описание</label>
                    <textarea
                        id="deal-description"
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="4"
                        maxlength="5000"
                    >{{ old('description', $deal->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success">
                        Сохранить изменения
                    </button>
                    <a href="{{ route('deals.show', $deal) }}" class="btn btn-outline-secondary">
                        Отмена
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
