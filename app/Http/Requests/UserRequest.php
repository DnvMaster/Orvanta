<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUser?->id)],
            'password' => [$targetUser ? 'nullable' : 'required', 'string', Password::min(8), 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')],
            'manager_id' => ['nullable', 'integer',
                Rule::exists('users', 'id'),
                Rule::notIn($targetUser ? [$targetUser->id] : []),
            ],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }
}
