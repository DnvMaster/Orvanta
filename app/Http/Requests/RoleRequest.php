<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function autorize(): bool
    {
        // При необходимости замените на 
        // $this->user()->hasPermissionTo('settings.edit')
        return true;
    }

    public function rules(): array
    {
        $role = $this->route('role');
        return [
            'name' => ['required','string','max:255'],
            'slug' => [$role?->is_system ? 'nullable':'required','string','max:100','alpha_dash', Rule::unique('roles','slug')->ignore($role?->id)],
            'description' => ['nullable','string','max:1000'],
            'permissions' => ['nullable','array'],
            'permissions.*' => ['nullable','string', Rule::in(['own','department','all'])]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название роли.',
            'slug.required' => 'Укажите системный код (slug) роли.',
            'slug.unique' => 'Роль с таким системным кодом уже существует.',
            'slug.alpha_dash' => 'Системный код может содержать только латинские буквы, цифры, дефис и подчеркивание.',
        ];
    }
}