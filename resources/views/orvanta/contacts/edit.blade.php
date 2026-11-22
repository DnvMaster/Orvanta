@extends('orvanta.layouts.app')
@section('title', __('Редактирование контакта') . ' — Orvanta CRM')
@section('breadcrumb', __('Редактирование контакта'))
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('УПРАВЛЕНИЕ КЛИЕНТАМИ') }}</p>
                <h1>
                    {{ $contact->last_name }}
                    {{ $contact->first_name }}
                    {{ $contact->middle_name }}
                </h1>
                <p class="page-description">{{ __('Редактирование контактной информации.') }}</p>
            </div>
            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-secondary">{{ __('К карточке контакта') }}</a>
        </div>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>{{ __('Информация о контакте') }}</h2>
                    <p>{{ __('Измените необходимые сведения.') }}</p>
                </div>
            </div>
            <div class="p-4">
                <form action="{{ route('contacts.update', $contact) }}" method="POST">
                    @include('orvanta.contacts._form', [
                        'method' => 'PUT',
                        'contact' => $contact,
                        'submitLabel' => __('Сохранить изменения')
                    ])
                </form>
            </div>
        </section>
    </section>
@endsection
