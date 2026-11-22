@extends('orvanta.layouts.app')
@section('title', __('Сотрудники') . ' — Orvanta CRM')
@section('breadcrumb', __('Сотрудники'))
@section('content')
    <section class="dashboard-content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">{{ __('УПРАВЛЕНИЕ КОМАНДОЙ') }}</p>
                <h1>{{ __('Сотрудники') }}</h1>
                <p class="page-description">{{ __('Учётные записи и информация о команде Orvanta.') }}</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="page-date">{{ __('Всего: :count', ['count' => $employees->total()]) }}</div>
                <a href="{{ route('employees.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>{{ __('Создать сотрудника') }}</a>
            </div>
        </div>
        <section class="panel">
            @if (session('success'))
                <div class="alert alert-success m-4 mb-0" role="status">
                    <strong>{{ session('success') }}</strong>
                    @if (session('created_employee_email'))
                        <p class="mb-0 mt-2">
                            {{ __('Email сотрудника:') }}
                            {{ session('created_employee_email') }}
                        </p>
                    @endif
                    @if (session('temporary_password'))
                        <div class="mt-3">
                            <strong>{{ __('Временный пароль:') }}</strong>
                            <code class="d-inline-block ms-1 p-2 rounded bg-white">{{ session('temporary_password') }}</code>
                            <p class="small text-secondary mb-0 mt-2">{{ __('Сохраните пароль и передайте его сотруднику безопасным способом. После закрытия этой страницы повторно показать его не получится.') }}</p>
                        </div>
                    @endif
                </div>
            @endif
            <div class="panel-header">
                <div>
                    <h2>{{ __('Список сотрудников') }}</h2>
                    <p>{{ __('Сотрудники, зарегистрированные в рабочем пространстве.') }}</p>
                </div>
                <span class="panel-badge">{{ __('Всего: :count', ['count' => $employees->total()]) }}</span>
            </div>
            @if ($employees->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">♙</div>
                    <h3>{{ __('Сотрудников пока нет') }}</h3>
                    <p>{{ __('Когда появятся учётные записи, они отобразятся здесь.') }}</p>
                </div>
            @else
                <div class="table-responsive px-4 pb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th scope="col">{{ __('Фото') }}</th>
                            <th scope="col">{{ __('Имя') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            <th scope="col">{{ __('Должность') }}</th>
                            <th scope="col">{{ __('Отдел') }}</th>
                            <th scope="col">{{ __('Роль') }}</th>
                            <th scope="col">{{ __('Статус') }}</th>
                            <th scope="col">{{ __('Действия') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>
                                    @if ($employee->avatar_path)
                                        <img src="{{ asset('storage/' . $employee->avatar_path) }}" alt="{{ __('Фото сотрудника :name', ['name' => $employee->name]) }}" width="40" height="40" class="rounded-circle object-fit-cover">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-circle fw-semibold" style="width: 40px; height: 40px; background: var(--orvanta-green-light); color: var(--orvanta-green);" role="img" aria-label="{{ __('Фото сотрудника отсутствует') }}">{{ mb_strtoupper(mb_substr($employee->name, 0, 1)) }}</div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->position ?: '—' }}</td>
                                <td>{{ $employee->department ?: '—' }}</td>
                                <td>{{ $employee->role === 'admin' ? __('Администратор') : __('Сотрудник') }}</td>
                                <td>
                                    @if ($employee->status !== 'active')
                                        <span class="badge text-bg-danger">{{ __('Заблокирован') }}</span>
                                    @elseif ($employee->is_online)
                                        <span class="badge text-bg-success">{{ __('В сети') }}</span>
                                    @else
                                        <span class="badge text-bg-secondary">{{ __('Не в сети') }}</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-success">{{ __('Редактировать') }}</a>
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm(@js(__('Вы действительно хотите удалить сотрудника :name? Это действие нельзя отменить.', ['name' => $employee->name])));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Удалить') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 pb-4">{{ $employees->links() }}</div>
            @endif
        </section>
    </section>
@endsection
