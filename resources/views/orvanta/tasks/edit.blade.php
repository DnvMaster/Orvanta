@extends('orvanta.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 mb-1">{{ __('Редактирование задачи') }}</h1>
            <p class="text-muted mb-0">{{ $task->title }}</p>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf
                    @method('PUT')
                    @include('orvanta.tasks._form', ['task' => $task, 'isEdit' => true])
                </form>
            </div>
        </div>
    </div>
@endsection
