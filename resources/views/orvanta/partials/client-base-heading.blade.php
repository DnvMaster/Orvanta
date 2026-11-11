@php
    $activeTab = request()->routeIs('contacts.*') ? 'contacts' : 'companies';
@endphp

<section class="client-base-hero">
    <div class="client-base-hero-content">
        <div class="eyebrow">КЛИЕНТСКАЯ БАЗА</div>
        <h1>Клиентская база</h1>
        <p>Компании и контакты ваших клиентов в одном месте.</p>

        <nav class="client-base-tabs" aria-label="Разделы клиентской базы">
            @can('viewAny', \App\Models\Company::class)
                <a href="{{ route('companies.index') }}"
                   class="client-base-tab {{ $activeTab === 'companies' ? 'active' : '' }}">
                    <i class="bi bi-buildings"></i>
                    Компании
                </a>
            @endcan

            @can('viewAny', \App\Models\Contact::class)
                <a href="{{ route('contacts.index') }}"
                   class="client-base-tab {{ $activeTab === 'contacts' ? 'active' : '' }}">
                    <i class="bi bi-person"></i>
                    Контакты
                </a>
            @endcan
        </nav>
    </div>

    <div class="client-base-hero-image" aria-hidden="true">
        <img src="{{ asset('orvanta/images/client-base-hero.png') }}" alt="" loading="eager">
    </div>
</section>
