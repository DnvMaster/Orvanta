@extends('orvanta.layouts.app')

@section('title', 'Сотрудники — Orvanta CRM')

@section('breadcrumb', 'Сотрудники')

@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">УПРАВЛЕНИЕ КОМАНДОЙ</p>
                <h1>Сотрудники</h1>
                <p class="page-description">
                    Учётные записи и информация о команде Orvanta.
                </p>
            </div>

            <div class="page-date">
                Всего: {{ $employees->total() }}
            </div>
        </div>

        <section class="panel">
            @if (session('success'))
                <div class="alert alert-success m-4 mb-0" role="status">
                    <strong>{{ session('success') }}</strong>

                    @if (session('created_employee_email'))
                        <p class="mb-0 mt-2">
                            Email сотрудника: {{ session('created_employee_email') }}
                        </p>
                    @endif

                    @if (session('temporary_password'))
                        <div class="mt-3">
                            <strong>Временный пароль:</strong>

                            <code class="d-inline-block ms-1 p-2 rounded bg-white">
                                {{ session('temporary_password') }}
                            </code>

                            <p class="small text-secondary mb-0 mt-2">
                                Сохраните пароль и передайте его сотруднику безопасным
                                способом. После закрытия этой страницы повторно показать
                                его не получится.
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="panel-header">
                <div>
                    <h2>Список сотрудников</h2>
                    <p>Сотрудники, зарегистрированные в рабочем пространстве.</p>
                </div>

                <span class="panel-badge">
                    Всего: {{ $employees->total() }}
                </span>
            </div>

            @if ($employees->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">♙</div>
                    <h3>Сотрудников пока нет</h3>
                    <p>Когда появятся учётные записи, они отобразятся здесь.</p>
                </div>
            @else
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Фото</th>
                                <th scope="col">Имя</th>
                                <th scope="col">Email</th>
                                <th scope="col">Должность</th>
                                <th scope="col">Отдел</th>
                                <th scope="col">Роль</th>
                                <th scope="col">Статус</th>
                                <th scope="col">Действия</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>
                                        @if ($employee->avatar_path)
                                            <img
                                                src="{{ asset('storage/' . $employee->avatar_path) }}"
                                                alt="Фото сотрудника {{ $employee->name }}"
                                                width="40"
                                                height="40"
                                                class="rounded-circle object-fit-cover"
                                            >
                                        @else
                                            <div
                                                class="d-flex align-items-center justify-content-center rounded-circle fw-semibold"
                                                style="width: 40px; height: 40px; background: var(--orvanta-green-light); color: var(--orvanta-green);"
                                                role="img"
                                                aria-label="Фото сотрудника отсутствует"
                                            >
                                                {{ mb_strtoupper(mb_substr($employee->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $employee->name }}
                                    </td>

                                    <td>{{ $employee->email }}</td>

                                    <td>{{ $employee->position ?: '—' }}</td>

                                    <td>{{ $employee->department ?: '—' }}</td>

                                    <td>
                                        {{ $employee->role === 'admin' ? 'Администратор' : 'Сотрудник' }}
                                    </td>

                                    <td>
                                        @if ($employee->status === 'active')
                                            <span class="badge text-bg-success">Активен</span>
                                        @else
                                            <span class="badge text-bg-secondary">Заблокирован</span>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">
                                        <a
                                            href="{{ route('employees.edit', $employee) }}"
                                            class="btn btn-sm btn-outline-success"
                                        >
                                            Редактировать
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 pb-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </section>
    </section>
@endsection