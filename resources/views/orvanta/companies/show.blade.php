@extends('orvanta.layouts.app')
@section('title', $company->name . ' — Orvanta CRM')
@section('breadcrumb', __('Карточка компании'))
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('УПРАВЛЕНИЕ КЛИЕНТАМИ') }}</p>
                <h1>{{ $company->name }}</h1>
                <p class="page-description">{{ __('Карточка компании и связанные с ней данные.') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">{{ __('К списку компаний') }}</a>
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-success">{{ __('Редактировать') }}</a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="row g-4">
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>{{ __('Основная информация') }}</h2>
                            <p>{{ __('Общие сведения о компании.') }}</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('Название компании') }}</div>
                                <div class="fw-semibold">{{ $company->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('Страна регистрации') }}</div>
                                <div class="fw-semibold d-flex align-items-center gap-2">
                                    @if ($company->country_code)
                                        <img src="https://flagcdn.com/w40/{{ strtolower($company->country_code) }}.png" alt="{{ config('countries.' . $company->country_code . '.name', $company->country_code) }}" class="company-country-flag" width="24" height="18" loading="lazy">
                                        <span>{{ config('countries.' . $company->country_code . '.name', $company->country_code) }}</span>
                                    @else
                                        <span class="text-muted">{{ __('Не указана') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('Юридическое название') }}</div>
                                <div>{{ $company->legal_name ?: __('Не указано') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">{{ __('Налоговый номер') }}</div>
                                <div>
                                    @if ($company->tax_number)
                                        {{ $company->tax_number_type }}
                                        {{ $company->tax_number }}
                                    @else
                                        {{ __('Не указан') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-12 col-lg-6">
                <section class="panel h-100">
                    <div class="panel-header">
                        <div>
                            <h2>{{ __('Контактная информация') }}</h2>
                            <p>{{ __('Способы связи с компанией.') }}</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Email</div>
                            <div>
                                @if ($company->email)
                                    <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                                @else
                                    {{ __('Не указан') }}
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('Телефон') }}</div>
                            <div>
                                @if ($company->phone)
                                    <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                                @else
                                    {{ __('Не указан') }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">{{ __('Веб-сайт') }}</div>
                            <div>
                                @if ($company->website)
                                    <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer">{{ $company->website }}</a>
                                @else
                                    {{ __('Не указан') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-12 col-lg-6">
                <section class="panel h-100">
                    <div class="panel-header">
                        <div>
                            <h2>{{ __('Юридический адрес') }}</h2>
                            <p>{{ __('Местонахождение компании.') }}</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="text-muted small mb-1">{{ __('Адрес') }}</div>
                        <div class="mb-3">{{ $company->address ?: __('Не указан') }}</div>
                        <div class="text-muted small mb-1">{{ __('Страна') }}</div>
                        <div>{{ $countries[$company->address_country_code] ?? __('Не указана') }}</div>
                    </div>
                </section>
            </div>
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>{{ __('Контактные лица') }}</h2>
                            <p>{{ __('Люди, связанные с этой компанией.') }}</p>
                        </div>
                        <span class="badge text-bg-light">{{ $company->contacts->count() }}</span>
                    </div>
                    <div class="p-4">
                        @forelse ($company->contacts as $contact)
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $contact->last_name }}
                                        {{ $contact->first_name }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <p class="mb-0">{{ __('У этой компании пока нет контактных лиц.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>{{ __('Сделки') }}</h2>
                            <p>{{ __('Сделки, связанные с этой компанией.') }}</p>
                        </div>
                        <span class="badge text-bg-light">{{ $company->deals->count() }}</span>
                    </div>
                    <div class="p-4">
                        @forelse ($company->deals as $deal)
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">
                                        <a href="{{ route('deals.show', $deal) }}">{{ $deal->title }}</a>
                                    </div>
                                </div>
                                <div class="fw-semibold">
                                    {{ number_format((float) $deal->amount, 2, ',', ' ') }}
                                    {{ $deal->currency }}
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <p class="mb-0">{{ __('У этой компании пока нет связанных сделок.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>{{ __('Дополнительная информация') }}</h2>
                            <p>{{ __('Внутренние заметки о компании.') }}</p>
                        </div>
                    </div>
                    <div class="p-4">
                        @if ($company->notes)
                            <div class="text-break">{!! nl2br(e($company->notes)) !!}</div>
                        @else
                            <div class="text-muted">{{ __('Заметки отсутствуют.') }}</div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
