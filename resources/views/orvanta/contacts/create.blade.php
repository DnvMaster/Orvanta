@extends('orvanta.layouts.app')
@section('title', 'Создание контакта — Orvanta CRM')
@section('breadcrumb', 'Создание контакта')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</p>
                <h1>Создание контакта</h1>
                <p class="page-description">Добавление нового контактного лица в клиентскую базу Orvanta.</p>
            </div>
            <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">К списку контактов</a>
        </div>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Информация о контакте</h2>
                    <p>Заполните основные сведения о контактном лице.</p>
                </div>
            </div>
            <div class="p-4">
                <form action="{{ route('contacts.store') }}" method="POST">
                    @include('orvanta.contacts._form', ['method' => 'POST','contact' => null,'submitLabel' => 'Создать контакт'])
                </form>
            </div>
        </section>
    </section>
@endsection