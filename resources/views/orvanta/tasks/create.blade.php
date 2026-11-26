@extends('orvanta.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-4">
            <h1 class="h3 mb-1">{{ __('Создание задачи') }}</h1>
            <p class="text-muted mb-0">{{ __('Заполните информацию о новой задаче') }}</p>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf
                    @include('orvanta.tasks._form', ['task' => null, 'isEdit' => false])
                </form>
            </div>
        </div>
    </div>
@endsection
