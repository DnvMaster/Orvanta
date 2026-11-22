@extends('orvanta.layouts.app')
@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ $department->exists ? __('Редактирование отдела: :name', ['name' => $department->name]) : __('Новое подразделение') }}</h1>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">← {{ __('Назад') }}</a>
        </div>
        <div class="card shadow-sm" style="max-width: 700px;">
            <div class="card-body">
                <form action="{{ $department->exists ? route('departments.update', $department) : route('departments.store') }}" method="POST">
                    @csrf
                    @if ($department->exists)
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Название отдела') }} <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">{{ __('Вышестоящее подразделение') }}</label>
                        <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— {{ __('Корневой отдел (без родителя)') }} —</option>
                            @foreach ($departments as $item)
                                <option value="{{ $item->id }}"@selected(old('parent_id', $department->parent_id) == $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('departments.index') }}" class="btn btn-light border">{{ __('Отмена') }}</a>
                        <button type="submit" class="btn btn-success">{{ __('Сохранить') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
