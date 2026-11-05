@extends('orvanta.layouts.app')
@section('title', 'Редактирование контакта — Orvanta CRM')
@section('breadcrumb', 'Редактирование контакта')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</p>
                <h1>
                    {{ $contact->last_name }}
                    {{ $contact->first_name }}
                    {{ $contact->middle_name }}
                </h1>
                <p class="page-description">Редактирование контактной информации.</p>
            </div>
            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-secondary">К карточке контакта</a>
        </div>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Информация о контакте</h2>
                    <p>Измените необходимые сведения.</p>
                </div>
            </div>
            <div class="p-4">
                <form action="{{ route('contacts.update', $contact) }}" method="POST">
                    @include('orvanta.contacts._form', ['method' => 'PUT','contact' => $contact,'submitLabel' => 'Сохранить изменения'])
                </form>
            </div>
        </section>
    </section>
@endsection