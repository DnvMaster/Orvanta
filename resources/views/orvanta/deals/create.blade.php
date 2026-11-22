@extends('orvanta.layouts.app')
@section('title', __('Новая сделка') . ' — Orvanta CRM')
@section('breadcrumb', __('Новая сделка'))
@section('content')
    <div class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('УПРАВЛЕНИЕ ПРОДАЖАМИ') }}</p>
                <h1>{{ __('Новая сделка') }}</h1>
                <p class="page-description">{{ __('Заполните данные, чтобы добавить сделку в воронку продаж.') }}</p>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ __('Проверьте заполнение полей формы.') }}
            </div>
        @endif
        @if ($pipelines->isEmpty())
            <section class="panel">
                <div class="empty-state">
                    <div class="empty-state-icon">↗</div>
                    <h3>{{ __('Нет активных воронок') }}</h3>
                    <p>{{ __('Чтобы создать сделку, сначала нужна активная воронка продаж.') }}</p>
                    <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">{{ __('Вернуться к сделкам') }}</a>
                </div>
            </section>
        @else
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>{{ __('Данные сделки') }}</h2>
                        <p>{{ __('Поля со звёздочкой обязательны для заполнения.') }}</p>
                    </div>
                </div>

                <form action="{{ route('deals.store') }}" method="POST" style="padding: 24px;">
                    @csrf
                    <div class="mb-3">
                        <label for="deal-title" class="form-label">{{ __('Название сделки') }} *</label>
                        <input type="text" id="deal-title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" maxlength="255" required autofocus>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="deal-company" class="form-label">{{ __('Компания') }}</label>
                            <select id="deal-company" name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                                <option value="">{{ __('Без компании') }}</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"@selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact-phone-search" class="form-label">{{ __('Поиск контакта по телефону') }}</label>
                            <div class="input-group">
                                <input type="tel" id="contact-phone-search" class="form-control" placeholder="+7 999 123-45-67 или +375 29 123-45-67" maxlength="30">
                                <button type="button" id="contact-phone-search-button" class="btn btn-outline-primary" data-search-url="{{ route('contacts.search') }}">{{ __('Найти') }}</button>
                            </div>
                            <div id="contact-search-status" class="alert alert-success mt-2" role="status" aria-live="polite" hidden></div>
                            <div id="quick-contact-form" class="border rounded p-3 mt-3" hidden>
                                <h3 class="h6 mb-3">{{ __('Создать новый контакт') }}</h3>
                                <div class="mb-3">
                                    <label for="quick-contact-last-name" class="form-label">{{ __('Фамилия') }} *</label>
                                    <input type="text" id="quick-contact-last-name" class="form-control" maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="quick-contact-first-name" class="form-label">{{ __('Имя') }} *</label>
                                    <input type="text" id="quick-contact-first-name" class="form-control" maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="quick-contact-middle-name" class="form-label">{{ __('Отчество') }}</label>
                                    <input type="text" id="quick-contact-middle-name" class="form-control" maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label for="quick-contact-phone" class="form-label">{{ __('Телефон') }}</label>
                                    <input type="tel" id="quick-contact-phone" class="form-control" maxlength="50">
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" id="quick-contact-save" class="btn btn-success" data-store-url="{{ route('contacts.quick-store') }}">{{ __('Создать контакт') }}</button>
                                    <button type="button" id="quick-contact-cancel" class="btn btn-outline-secondary">{{ __('Отмена') }}</button>
                                </div>
                                <div id="quick-contact-status" class="form-text mt-2" aria-live="polite"></div>
                            </div>
                            <label for="deal-contact" class="form-label mt-3">{{ __('Контакт сделки') }}</label>
                            <select id="deal-contact" name="contact_id" class="form-select @error('contact_id') is-invalid @enderror">
                                <option value="">{{ __('Без контакта') }}</option>
                                @foreach ($contacts as $contact)
                                    <option value="{{ $contact->id }}"@selected((string) old('contact_id') === (string) $contact->id)>
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
                            <label for="deal-amount" class="form-label">{{ __('Сумма') }} *</label>
                            <input type="number" id="deal-amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', '0.00') }}" min="0" max="9999999999999.99" step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deal-currency" class="form-label">{{ __('Валюта') }} *</label>
                            <select id="deal-currency" name="currency" class="form-select @error('currency') is-invalid @enderror" required>
                                <option value="BYB" @selected(old('currency', 'BYB') === 'BYB')>BYB — {{ __('Белорусский рубль') }}</option>
                                <option value="RUB" @selected(old('currency') === 'RUB')>RUB — {{ __('Российский рубль') }}</option>
                                <option value="EUR" @selected(old('currency') === 'EUR')>EUR — {{ __('Евро') }}</option>
                                <option value="USD" @selected(old('currency') === 'USD')>USD — {{ __('Доллар США') }}</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deal-pipeline" class="form-label">{{ __('Воронка') }} *</label>
                        <select id="deal-pipeline" name="pipeline_id" class="form-select @error('pipeline_id') is-invalid @enderror" required onchange="window.location.href = '{{ route('deals.create') }}?pipeline=' + this.value">
                            @foreach ($pipelines as $pipeline)
                                <option value="{{ $pipeline->id }}"
                                    @selected((string) old('pipeline_id', $selectedPipeline?->id) === (string) $pipeline->id)>{{ $pipeline->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pipeline_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="deal-stage" class="form-label">{{ __('Этап воронки') }} *</label>
                        <select id="deal-stage" name="pipeline_stage_id" class="form-select @error('pipeline_stage_id') is-invalid @enderror" required
                            @disabled(!$selectedPipeline || $selectedPipeline->stages->where('type', 'open')->isEmpty())>
                            @if ($selectedPipeline)
                                @foreach ($selectedPipeline->stages->where('type', 'open') as $stage)
                                    <option value="{{ $stage->id }}"
                                        @selected((string) old('pipeline_stage_id', $selectedPipeline->stages->where('type', 'open')->first()?->id) === (string) $stage->id)>{{ $stage->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('pipeline_stage_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="deal-description" class="form-label">{{ __('Описание') }}</label>
                        <textarea id="deal-description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" maxlength="5000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-success"@disabled(!$selectedPipeline || $selectedPipeline->stages->where('type', 'open')->isEmpty())>{{ __('Сохранить сделку') }}</button>
                        <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">{{ __('Отмена') }}</a>
                    </div>
                </form>
            </section>
        @endif
    </div>
@endsection
