@extends('orvanta.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">{{ __('Задачи') }}</h1>
                <p class="text-muted mb-0">{{ __('Управление рабочими задачами') }}</p>
            </div>
            @can('create', \App\Models\Task::class)
                <a href="{{ route('tasks.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('Создать задачу') }}</a>
            @endcan
        </div>
        <div class="mb-4">
            <ul class="nav nav-pills flex-wrap gap-2">
                <li class="nav-item"><a href="{{ route('tasks.index', ['filter' => 'my']) }}" class="nav-link {{ $activeFilter === 'my' ? 'active' : '' }}">{{ __('Мои задачи') }}</a></li>
                <li class="nav-item"><a href="{{ route('tasks.index', ['filter' => 'created']) }}" class="nav-link {{ $activeFilter === 'created' ? 'active' : '' }}">{{ __('Созданные мной') }}</a></li>
                <li class="nav-item"><a href="{{ route('tasks.index', ['filter' => 'participating']) }}" class="nav-link {{ $activeFilter === 'participating' ? 'active' : '' }}">{{ __('Участвую') }}</a></li>
                <li class="nav-item"><a href="{{ route('tasks.index', ['filter' => 'review']) }}" class="nav-link {{ $activeFilter === 'review' ? 'active' : '' }}">{{ __('На проверке') }}</a></li>
                <li class="nav-item"><a href="{{ route('tasks.index', ['filter' => 'all']) }}" class="nav-link {{ $activeFilter === 'all' ? 'active' : '' }}">{{ __('Все доступные') }}</a></li>
            </ul>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($tasks->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-clipboard-check fs-1 d-block mb-3"></i>
                        <p class="mb-0">{{ __('Задачи не найдены') }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Задача') }}</th>
                                    <th>{{ __('Постановщик') }}</th>
                                    <th>{{ __('Исполнитель') }}</th>
                                    <th>{{ __('Приоритет') }}</th>
                                    <th>{{ __('Срок выполнения') }}</th>
                                    <th>{{ __('Статус') }}</th>
                                    <th>{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td><span class="fw-semibold">{{ $task->title }}</span></td>
                                    <td>{{ $task->creator?->name ?? '—' }}</td>
                                    <td>{{ $task->assignee?->name ?? '—' }}</td>
                                    <td>
                                        @switch($task->priority)
                                            @case('low')
                                                {{ __('Низкий') }}
                                                @break
                                            @case('normal')
                                                {{ __('Обычный') }}
                                                @break
                                            @case('high')
                                                {{ __('Высокий') }}
                                                @break
                                            @case('urgent')
                                                {{ __('Срочный') }}
                                                @break
                                            @default
                                                —
                                        @endswitch
                                    </td>
                                    <td>{{ $task->due_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                    <td>
                                        @switch($task->status)
                                            @case('new')
                                                {{ __('Новая') }}
                                                @break
                                            @case('in_progress')
                                                {{ __('В работе') }}
                                                @break
                                            @case('review')
                                                {{ __('На проверке') }}
                                                @break
                                            @case('completed')
                                                {{ __('Завершена') }}
                                                @break
                                            @case('cancelled')
                                                {{ __('Отменена') }}
                                                @break
                                            @default
                                                —
                                        @endswitch
                                    </td>
                                    <td>
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i>{{ __('Изменить') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $tasks->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
