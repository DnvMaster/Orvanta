<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $department = $this->route('department');
        $excludedIds = $department ? array_merge([$department->id], $department->descendantIds()) : [];

        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id'),
                Rule::notIn($excludedIds),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название подразделения.',
            'parent_id.not_in' => 'Нельзя выбрать текущее или дочернее подразделение в качестве родительского.',
        ];
    }
}
