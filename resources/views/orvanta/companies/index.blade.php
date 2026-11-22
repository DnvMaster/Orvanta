@extends('orvanta.layouts.app')
@section('title', __('Компании') . ' — Orvanta CRM')
@section('breadcrumb', __('Компании'))
@section('content')
    <section class="dashboard-content">
        @include('orvanta.partials.client-base-heading')
        <section class="panel">
            @if (session('success'))
                <div class="alert alert-success m-4 mb-0" role="status">
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif
            <div class="panel-header">
                <div>
                    <h2>{{ __('Список компаний') }}</h2>
                    <p>{{ __('Зарегистрированные компании и связанные с ними данные.') }}</p>
                </div>
                <a href="{{ route('companies.create') }}" class="btn btn-success">{{ __('Добавить компанию') }}</a>
            </div>
            <div class="px-4 pb-3">
                <form action="{{ route('companies.index') }}" method="GET">
                    <div class="row g-2">
                        <div class="col-md-9">
                            <input type="search" name="search" class="form-control" placeholder="{{ __('Поиск по названию, email или телефону...') }}" value="{{ $search }}" aria-label="{{ __('Поиск компаний') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-success w-100">{{ __('Найти') }}</button>
                            @if ($search)
                                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">{{ __('Сбросить') }}</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            @if ($companies->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">▤</div>
                    <h3>{{ $search ? __('Компании не найдены') : __('Компаний пока нет') }}</h3>
                    <p>{{ $search ? __('Попробуйте изменить поисковый запрос.') : __('Добавьте первую компанию, чтобы начать работу с клиентами.') }}</p>
                    @if (!$search)
                        <a href="{{ route('companies.create') }}" class="btn btn-success mt-2">{{ __('Добавить компанию') }}</a>
                    @endif
                </div>
            @else
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">{{ __('Компания') }}</th>
                            <th scope="col">{{ __('Страна') }}</th>
                            <th scope="col">{{ __('Налоговый номер') }}</th>
                            <th scope="col">Email</th>
                            <th scope="col">{{ __('Телефон') }}</th>
                            <th scope="col">{{ __('Контакты') }}</th>
                            <th scope="col">{{ __('Сделки') }}</th>
                            <th scope="col">{{ __('Действия') }}</th>
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
                                <td>
                                    @if ($company->country_code)
                                        <span class="company-country">
                                            <img src="https://flagcdn.com/w40/{{ strtolower($company->country_code) }}.png" alt="{{ config('countries.' . $company->country_code . '.name', $company->country_code) }}" class="company-country-flag" width="24" height="18" loading="lazy">
                                            <span>{{ config('countries.' . $company->country_code . '.name', $company->country_code) }}</span>
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
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
                                    <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-success">{{ __('Открыть') }}</a>
                                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary">{{ __('Изменить') }}</a>
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
