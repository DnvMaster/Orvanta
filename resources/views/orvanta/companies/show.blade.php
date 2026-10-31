@extends('orvanta.layouts.app')
@section('title', $company->name . ' — Orvanta CRM')
@section('breadcrumb', 'Карточка компании')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</p>
                <h1>{{ $company->name }}</h1>
                <p class="page-description">Карточка компании и связанные с ней данные.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">К списку компаний</a>
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-success">Редактировать</a>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="row g-4">
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Основная информация</h2>
                            <p>Общие сведения о компании.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Название компании</div>
                                <div class="fw-semibold">{{ $company->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Страна регистрации</div>
                                <div class="fw-semibold">
                                    @php
                                        $countries = [
                                            'DE' => '🇩🇪 Германия',
                                            'FR' => '🇫🇷 Франция',
                                            'PL' => '🇵🇱 Польша',
                                            'GB' => '🇬🇧 Великобритания',
                                            'RU' => '🇷🇺 Россия',
                                             'BY' => 'by Беларусь',
                                            'KZ' => '🇰🇿 Казахстан',
                                            'US' => '🇺🇸 США',
                                        ];
                                    @endphp
                                    {{ $countries[$company->country_code] ?? 'Не указана' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Юридическое название</div>
                                <div>{{ $company->legal_name ?: 'Не указано' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Налоговый номер</div>
                                <div>
                                    @if($company->tax_number)
                                        {{ $company->tax_number_type }}
                                        {{ $company->tax_number }}
                                    @else
                                        Не указан
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
                            <h2>Контактная информация</h2>
                            <p>Способы связи с компанией.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Email</div>
                            <div>
                                @if($company->email)
                                    <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                                @else
                                    Не указан
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Телефон</div>
                            <div>
                                @if($company->phone)
                                    <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                                @else
                                    Не указан
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Веб-сайт</div>
                            <div>
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer">{{ $company->website }}</a>
                                @else
                                    Не указан
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
                            <h2>Юридический адрес</h2>
                            <p>Местонахождение компании.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="text-muted small mb-1">Адрес</div>
                        <div class="mb-3">{{ $company->address ?: 'Не указан' }}</div>
                        <div class="text-muted small mb-1">Страна</div>
                        <div>{{ $countries[$company->address_country_code] ?? 'Не указана' }}</div>
                    </div>
                </section>
            </div>
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Контактные лица</h2>
                            <p>Люди, связанные с этой компанией.</p>
                        </div>
                        <span class="badge text-bg-light">{{ $company->contacts->count() }}</span>
                    </div>

                    <div class="p-4">
                        @forelse($company->contacts as $contact)
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
                                <p class="mb-0">У этой компании пока нет контактных лиц.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Сделки</h2>
                            <p>Сделки, связанные с этой компанией.</p>
                        </div>
                        <span class="badge text-bg-light">{{ $company->deals->count() }}</span>
                    </div>
                    <div class="p-4">
                        @forelse($company->deals as $deal)
                            <div class="d-flex align-items-cente justify-content-between flex-wrap gap-3 py-3 border-bottom">
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
                                <p class="mb-0">У этой компании пока нет связанных сделок.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <div class="col-12">
                <section class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Дополнительная информация</h2>
                            <p>Внутренние заметки о компании.</p>
                        </div>
                    </div>
                    <div class="p-4">
                        @if($company->notes)
                            <div class="text-break">{!! nl2br(e($company->notes)) !!}</div>
                        @else
                            <div class="text-muted">Заметки отсутствуют.</div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
