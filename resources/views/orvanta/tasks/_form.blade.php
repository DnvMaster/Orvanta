<div class="mb-3">
    <label for="title" class="form-label">{{ __('Название задачи') }}</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $task?->title) }}" maxlength="255" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="description" class="form-label">{{ __('Описание') }}</label>
    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $task?->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="priority" class="form-label">{{ __('Приоритет') }}</label>
        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
            @foreach (['low' => __('Низкий'), 'normal' => __('Обычный'), 'high' => __('Высокий'), 'urgent' => __('Срочный') ] as $value => $label)
                <option value="{{ $value }}"@selected(old('priority', $task?->priority ?? 'normal') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('priority')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @if (! $isEdit || auth()->user()->hasPermissionTo('tasks.assign'))
        <div class="col-md-6 mb-3">
            <label for="assigned_to" class="form-label">{{ __('Исполнитель') }}</label>
            <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror" required>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}"@selected((string) old('assigned_to', $task?->assigned_to ?? auth()->id()) === (string) $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
            @error('assigned_to')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="starts_at" class="form-label">{{ __('Начало') }}</label>
        <input type="datetime-local" name="starts_at" id="starts_at" class="form-control @error('starts_at') is-invalid @enderror" value="{{ old('starts_at', $task?->starts_at?->format('Y-m-d\TH:i')) }}">
        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="due_at" class="form-label">{{ __('Срок выполнения') }}</label>
        <input type="datetime-local" name="due_at" id="due_at" class="form-control @error('due_at') is-invalid @enderror" value="{{ old('due_at', $task?->due_at?->format('Y-m-d\TH:i')) }}">
        @error('due_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $isEdit ? __('Сохранить изменения') : __('Создать задачу') }}</button>
    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">{{ __('Отмена') }}</a>
</div>
