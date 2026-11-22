@extends('orvanta.layouts.app')
@section('title', __('Контакты') . ' — Orvanta CRM')
@section('content')
    <section class="dashboard-content">
        @include('orvanta.partials.client-base-heading')
        <div class="panel">
            <div class="panel-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">{{ __('Список контактов') }}</h5>
                    <div class="text-muted small">{{ __('Всего контактов: :count', ['count' => $contacts->total()]) }}</div>
                </div>
                <form action="{{ route('contacts.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <input type="search" name="search" class="form-control" placeholder="{{ __('Поиск по имени, email или телефону') }}" value="{{ $search }}" aria-label="{{ __('Поиск контактов') }}">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>{{ __('Найти') }}
                    </button>
                    @if ($search)
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">{{ __('Сбросить') }}</a>
                    @endif
                </form>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Контакт') }}</th>
                            <th>{{ __('Компания') }}</th>
                            <th>Email</th>
                            <th>{{ __('Телефон') }}</th>
                            <th class="text-end">{{ __('Действия') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($contacts as $contact)
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $contact->last_name }}
                                    {{ $contact->first_name }}
                                    {{ $contact->middle_name }}
                                </div>
                                @if ($contact->position)
                                    <div class="text-muted small">{{ $contact->position }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($contact->company)
                                    {{ $contact->company->name }}
                                @else
                                    <span class="text-muted">{{ __('Без компании') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($contact->email)
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($contact->phone)
                                    <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @can('view', $contact)
                                        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Открыть контакт') }}"><i class="bi bi-eye"></i></a>
                                    @endcan
                                    @can('update', $contact)
                                        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Редактировать контакт') }}"><i class="bi bi-pencil"></i></a>
                                    @endcan
                                    @can('delete', $contact)
                                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm(@json(__('Вы действительно хотите удалить этот контакт?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Удалить контакт') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    @if ($search)
                                        {{ __('По вашему запросу ничего не найдено.') }}
                                    @else
                                        {{ __('Контактов пока нет.') }}
                                    @endif
                                </div>
                                @can('create', \App\Models\Contact::class)
                                    @if (!$search)
                                        <a href="{{ route('contacts.create') }}" class="btn btn-success btn-sm mt-3">{{ __('Добавить первый контакт') }}</a>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if ($contacts->hasPages())
                <div class="p-3">{{ $contacts->links() }}</div>
            @endif
        </div>
    </section>
@endsection
