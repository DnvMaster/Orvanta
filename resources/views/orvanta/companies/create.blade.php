@extends('orvanta.layouts.app')
@section('title', 'Добавление компании — Orvanta CRM')
@section('breadcrumb', 'Добавление компании')
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КЛИЕНТАМИ</p>
                <h1>Добавление компании</h1>
                <p class="page-description">Создание карточки новой компании в Orvanta.</p>
            </div>
            <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Назад к компаниям</a>
        </div>
        <form action="{{ route('companies.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-12">
                    <section class="panel">
                        <div class="panel-header">
                            <div>
                                <h2>Основная информация</h2>
                                <p>Название и страна регистрации компании.</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Название компании<span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="255">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="country_code" class="form-label">Страна регистрации</label>
                                    <select id="country_code" name="country_code" class="form-select @error('country_code') is-invalid @enderror">
                                        <option value="">Выберите страну</option>
                                        @foreach(config('countries') as $code => $country)
                                            <option value="{{ $code }}" @selected(old('country_code') === $code)>
                                                {{ $country['flag'] }} {{ $country['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-12">
                    <section class="panel">
                        <div class="panel-header">
                            <div>
                                <h2>Юридические реквизиты</h2>
                                <p>Официальные данные регистрации компании.</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="legal_name" class="form-label">Юридическое название</label>
                                    <input type="text" id="legal_name" name="legal_name" class="form-control @error('legal_name') is-invalid @enderror" value="{{ old('legal_name') }}" maxlength="255">
                                    @error('legal_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tax_number_type" class="form-label">Тип налогового номера</label>
                                    <select id="tax_number_type" name="tax_number_type" class="form-select @error('tax_number_type') is-invalid @enderror">
                                        <option value="">Сначала выберите страну</option>
                                    </select>
                                    @error('tax_number_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tax_number" class="form-label">Налоговый номер</label>
                                    <input type="text" id="tax_number" name="tax_number" class="form-control @error('tax_number') is-invalid @enderror" value="{{ old('tax_number') }}" maxlength="255">
                                    @error('tax_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-12">
                    <section class="panel">
                        <div class="panel-header">
                            <div>
                                <h2>Контактная информация</h2>
                                <p>Способы связи с компанией.</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" maxlength="255">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="phone" class="form-label">Телефон</label>
                                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" maxlength="50">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="website" class="form-label">Веб-сайт</label>
                                    <input type="url" id="website" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://example.com" maxlength="255">
                                    @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-12">
                    <section class="panel">
                        <div class="panel-header">
                            <div>
                                <h2>Юридический адрес</h2>
                                <p>Адрес местонахождения компании.</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="address" class="form-label">Адрес</label>
                                    <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror" maxlength="2000">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="address_country_code" class="form-label">Страна</label>
                                    <select id="address_country_code" name="address_country_code"
                                        class="form-select @error('address_country_code') is-invalid @enderror">
                                        <option value="">Выберите страну</option>
                                        @foreach(config('countries') as $code => $country)
                                            <option value="{{ $code }}"@selected(old('address_country_code') === $code)>
                                                {{ $country['flag'] }} {{ $country['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('address_country_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                            <label for="notes" class="form-label">Заметки</label>
                            <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" maxlength="5000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </section>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 pb-4">
                    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    <button type="submit" class="btn btn-success">Сохранить компанию</button>
                </div>
            </div>
        </form>
    </section>

    {{-- Динамический выбор типа налогового номера --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countrySelect = document.getElementById('country_code');
            const taxTypeSelect = document.getElementById('tax_number_type');
            const countries = @json(config('countries'));
            const oldTaxType = @json(old('tax_number_type'));
            function updateTaxTypes(selectedType = '') {
                const countryCode = countrySelect.value;
                const identifiers = countries[countryCode]?.tax_identifiers ?? {};
                taxTypeSelect.innerHTML = '';
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = countryCode ? 'Выберите тип налогового номера' : 'Сначала выберите страну';
                taxTypeSelect.appendChild(defaultOption);
                Object.entries(identifiers).forEach(([key, label]) => {
                    const option = document.createElement('option');
                    option.value = label;
                    option.textContent = label;
                    option.selected = label === selectedType;
                    taxTypeSelect.appendChild(option);
                });
            }
            countrySelect.addEventListener('change', function () {
                updateTaxTypes();
            });
            updateTaxTypes(oldTaxType);
        });
    </script>
@endsection
