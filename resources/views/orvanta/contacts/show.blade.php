@extends('orvanta.layouts.app')
@section('title', $contact->last_name . ' ' . $contact->first_name . ' — Orvanta CRM')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <div class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</div>
                <h1>
                    {{ $contact->last_name }}
                    {{ $contact->first_name }}
                    {{ $contact->middle_name }}
                </h1>
                <p class="page-description">{{ $contact->position ?: 'Карточка контактного лица' }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>К списку</a>
                @can('update', $contact)
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Редактировать</a>
                @endcan
                @can('delete', $contact)
                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Вы действительно хотите удалить этот контакт?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i>Удалить</button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="panel h-100">
                    <div class="panel-header">
                        <h5 class="mb-0">Контактная информация</h5>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Фамилия</div>
                                <div class="fw-semibold">{{ $contact->last_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Имя</div>
                                <div class="fw-semibold">{{ $contact->first_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Отчество</div>
                                <div class="fw-semibold">{{ $contact->middle_name ?: 'Не указано' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Должность</div>
                                <div class="fw-semibold">{{ $contact->position ?: 'Не указана' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Email</div>
                                @if ($contact->email)
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                @else
                                    <span class="text-muted">Не указан</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Телефон</div>
                                @if ($contact->phone)
                                    <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                @else
                                    <span class="text-muted">Не указан</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel h-100">
                    <div class="panel-header">
                        <h5 class="mb-0">Связи</h5>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <div class="text-muted small mb-2">Компания</div>
                            @if ($contact->company)
                                @can('view', $contact->company)
                                    <a href="{{ route('companies.show', $contact->company) }}" class="fw-semibold text-decoration-none">
                                        {{ $contact->company->name }}
                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                    </a>
                                @else
                                    <span class="text-muted">Компания недоступна</span>
                                @endcan
                            @else
                                <span class="text-muted">Не привязана</span>
                            @endif
                        </div>
                        <hr>
                        <div>
                            <div class="text-muted small mb-2">Ответственный сотрудник</div>
                            @if ($contact->responsible)
                                <div class="fw-semibold">{{ $contact->responsible->name }}</div>
                                <div class="text-muted small">{{ $contact->responsible->email }}</div>
                            @else
                                <span class="text-muted">Не назначен</span>
                            @endif
                        </div>
                        <hr>
                        <div>
                            <div class="text-muted small mb-2">Дата создания</div>
                            <div class="fw-semibold">
                                {{ $contact->created_at?->format('d.m.Y H:i') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="panel">
                    <div class="panel-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Связанные сделки</h5>
                            <div class="text-muted small">
                                Всего доступных сделок: {{ $contact->deals->count() }}
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Название сделки</th>
                                    <th>Воронка</th>
                                    <th>Этап</th>
                                    <th>Сумма</th>
                                    <th>Ответственный</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contact->deals as $deal)
                                    <tr>
                                        <td>
                                            <a href="{{ route('deals.show', $deal) }}" class="fw-semibold text-decoration-none">{{ $deal->title }}</a>
                                        </td>
                                        <td>{{ $deal->pipeline?->name ?? '—' }}</td>
                                        <td>{{ $deal->stage?->name ?? '—' }}</td>
                                        <td>
                                            @if ($deal->amount !== null)
                                                {{ number_format((float) $deal->amount, 2, ',', ' ') }}
                                                {{ $deal->currency }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $deal->responsible?->name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">С этим контактом пока не связано ни одной доступной сделки.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="panel">
                    <div class="panel-header">
                        <h5 class="mb-0">Дополнительная информация</h5>
                    </div>
                    <div class="p-4">
                        @if ($contact->notes)
                            <div class="text-break">{!! nl2br(e($contact->notes)) !!}</div>
                        @else
                            <span class="text-muted"> Дополнительная информация отсутствует.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection