@csrf
@if ($method === 'PUT')
    @method('PUT')
@endif
@if ($errors->any())
    <div class="alert alert-danger" role="alert">Проверьте правильность заполнения формы.</div>
@endif
<div class="row g-4">
    <div class="col-12">
        <label for="company_id" class="form-label">Компания</label>
        <select name="company_id" id="company_id" class="form-select @error('company_id') is-invalid @enderror">
            <option value="">Без привязки к компании</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}"
                    @selected(old('company_id', $contact?->company_id) == $company->id)>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
        @error('company_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="last_name" class="form-label">Фамилия *</label>
        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $contact?->last_name) }}" maxlength="255" required>
        @error('last_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="first_name" class="form-label">Имя *</label>
        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $contact?->first_name) }}" maxlength="255" required>
        @error('first_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4">
        <label for="middle_name" class="form-label">Отчество</label>
        <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name', $contact?->middle_name) }}" maxlength="255">
        @error('middle_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $contact?->email) }}" maxlength="255">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label for="phone" class="form-label">Телефон</label>
        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $contact?->phone) }}" maxlength="50">
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="position" class="form-label">Должность</label>
        <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $contact?->position) }}" maxlength="255">
        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Дополнительная информация</label>
        <textarea name="notes" id="notes" rows="5" maxlength="5000" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $contact?->notes) }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-success">{{ $submitLabel }}</button>
    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Отмена</a>
</div>