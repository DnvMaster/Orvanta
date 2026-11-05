@extends('orvanta.layouts.app')
@section('title', 'Контакты — Orvanta CRM')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <div class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</div>
                <h1>Контакты</h1>
                <p class="page-description"> Контактные лица и информация о взаимодействии с клиентами.</p>
            </div>
            @can('create', \App\Models\Contact::class)
                <a href="{{ route('contacts.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Добавить контакт</a>
            @endcan
        </div>
        <div class="panel">
            <div class="panel-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">Список контактов</h5>
                    <div class="text-muted small">Всего контактов: {{ $contacts->total() }}</div>
                </div>
                <form action="{{ route('contacts.index') }}" method="GET"
                    class="d-flex flex-wrap gap-2">
                    <input type="search" name="search" class="form-control" placeholder="Поиск по имени, email или телефону" value="{{ $search }}" aria-label="Поиск контактов">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>Найти</button>
                    @if ($search)
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary"> Сбросить</a>
                    @endif
                </form>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Контакт</th>
                            <th>Компания</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th class="text-end">Действия</th>
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
                                        <div class="text-muted small">
                                            {{ $contact->position }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($contact->company)
                                        {{ $contact->company->name }}
                                    @else
                                        <span class="text-muted">Без компании</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($contact->email)
                                        <a href="mailto:{{ $contact->email }}">
                                            {{ $contact->email }}
                                        </a>
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
                                            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-sm btn-outline-primary" title="Открыть контакт">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan
                                        @can('update', $contact)
                                            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary" title="Редактировать контакт">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $contact)
                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Вы действительно хотите удалить этот контакт?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить контакт">
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
                                            По вашему запросу ничего не найдено.
                                        @else
                                            Контактов пока нет.
                                        @endif
                                    </div>
                                    @can('create', \App\Models\Contact::class)
                                        @if (!$search)
                                            <a href="{{ route('contacts.create') }}" class="btn btn-success btn-sm mt-3">Добавить первый контакт</a>
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