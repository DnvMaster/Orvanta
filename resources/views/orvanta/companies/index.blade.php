@extends('orvanta.layouts.app')
@section('title', 'Компании — Orvanta CRM')
@section('breadcrumb', 'Компании')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</p>
                <h1>Компании</h1>
                <p class="page-description">Компании, их реквизиты и деловые отношения в Orvanta.</p>
            </div>
            <div class="page-date">Всего: {{ $companies->total() }}</div>
        </div>
        <section class="panel">
            @if (session('success'))
                <div class="alert alert-success m-4 mb-0" role="status">
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif
            <div class="panel-header">
                <div>
                    <h2>Список компаний</h2>
                    <p>Зарегистрированные компании и связанные с ними данные.</p>
                </div>
                <a href="{{ route('companies.create') }}" class="btn btn-success">Добавить компанию</a>
            </div>
            <div class="px-4 pb-3">
                <form action="{{ route('companies.index') }}" method="GET">
                    <div class="row g-2">
                        <div class="col-md-9">
                            <input type="search" name="search" class="form-control" placeholder="Поиск по названию, email или телефону..." value="{{ $search }}" aria-label="Поиск компаний">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-success w-100">Найти</button>
                            @if ($search)
                                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Сбросить</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if ($companies->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">▤</div>
                    <h3>{{ $search ? 'Компании не найдены' : 'Компаний пока нет' }}</h3>
                    <p>{{ $search ? 'Попробуйте изменить поисковый запрос.' : 'Добавьте первую компанию, чтобы начать работу с клиентами.'}}</p>
                    @if (!$search)
                        <a href="{{ route('companies.create') }}" class="btn btn-success mt-2">Добавить компанию</a>
                    @endif
                </div>
            @else
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Компания</th>
                                <th scope="col">Страна</th>
                                <th scope="col">Налоговый номер</th>
                                <th scope="col">Email</th>
                                <th scope="col">Телефон</th>
                                <th scope="col">Контакты</th>
                                <th scope="col">Сделки</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td>
                                        <a href="{{ route('companies.show', $company) }}" class="fw-semibold text-decoration-none">{{ $company->name }}</a>
                                        @if ($company->legal_name)
                                            <div class="small text-secondary">{{ $company->legal_name }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $company->country_code ?: '—' }}</td>
                                    <td>
                                        {{ $company->tax_number ?: '—' }}
                                        @if ($company->tax_number_type)
                                            <div class="small text-secondary">{{ $company->tax_number_type }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $company->email ?: '—' }}</td>
                                    <td>{{ $company->phone ?: '—' }}</td>
                                    <td>{{ $company->contacts_count }}</td>
                                    <td>{{ $company->deals_count }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-success">Открыть</a>
                                        <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary">Изменить</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 pb-4">{{ $companies->links() }}</div>
            @endif
        </section>
    </section>
@endsection
