<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::query()->select(['id', 'name', 'email', 'role', 'status', 'phone', 'position', 'department', 'avatar_path', 'created_at'])->orderBy('name')->paginate(15);
        return view('orvanta.employees.index', ['employees' => $employees]);
    }

    public function create(): View
    {
        return view('orvanta.employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255']
        ]);
        $temporaryPassword = Str::password(16);
        $employee = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'role' => 'employee',
            'status' => 'active',
            'password' => $temporaryPassword
        ]);
        return redirect()->route('employees.index')->with('success', 'Сотрудник успешно создан.')->with('temporary_password', $temporaryPassword)->with('created_employee_email', $employee->email);
    }

    public function editProfile(Request $request): View
    {
        return view('orvanta.profile.edit', ['employee' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        return $this->saveEmployeeProfile($request, $request->user(), 'profile.edit');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required','confirmed','different:current_password', Password::min(12)]],[
            'current_password.required' => 'Введите текущий пароль.',
            'current_password.current_password' => 'Текущий пароль указан неверно.',
            'password.required' => 'Введите новый пароль.',
            'password.confirmed' => 'Подтверждение нового пароля не совпадает.',
            'password.different' => 'Новый пароль должен отличаться от текущего.',
            'password.min' => 'Новый пароль должен содержать не менее 12 символов.',
        ]);
        $user = $request->user();
        $user->password = $validated['password'];
        $user->save();
        return redirect()->route('profile.edit')->with('password_success', 'Пароль успешно изменён.');
    }

    public function edit(User $employee): View
    {
        return view('orvanta.employees.edit', ['employee' => $employee]);
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        return $this->saveEmployeeProfile($request, $employee, 'employees.edit', true);
    }

    private function saveEmployeeProfile(Request $request, User $employee, string $redirectRoute, bool $isEmployeeEdit = false): RedirectResponse
    {
        $isAdmin = $request->user()->isAdmin();
        $rules = [
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255']
        ];
        if ($isAdmin) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->id)];
            $rules['avatar'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }
        $messages = [
            'name.required' => 'Укажите имя сотрудника.',
            'name.max' => 'Имя не должно превышать 255 символов.',
            'email.required' => 'Укажите электронную почту.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.unique' => 'Этот адрес электронной почты уже используется.',
            'phone.max' => 'Телефон не должен превышать 30 символов.',
            'position.max' => 'Название должности слишком длинное.',
            'department.max' => 'Название отдела слишком длинное.',
            'avatar.image' => 'Загрузите корректное изображение.',
            'avatar.mimes' => 'Допустимые форматы фотографии: JPEG, PNG и WebP.',
            'avatar.max' => 'Размер фотографии не должен превышать 2 МБ.'
        ];

        $validated = $request->validate($rules, $messages);
        $employee->phone = $validated['phone'] ?? null;
        $employee->position = $validated['position'] ?? null;
        $employee->department = $validated['department'] ?? null;
        if ($isAdmin) {
            $employee->name = $validated['name'];
            $employee->email = $validated['email'];
        }
        $oldAvatarPath = $employee->avatar_path;
        $newAvatarPath = null;
        if ($isAdmin && $request->hasFile('avatar')) {
            $newAvatarPath = $request->file('avatar')->store('avatars', 'public');
            if ($newAvatarPath === false) {
                return back()->withInput()->withErrors(['avatar' => 'Не удалось сохранить фотографию. Попробуйте ещё раз.']);
            }
            $employee->avatar_path = $newAvatarPath;
        }
        $employee->save();
        if ($newAvatarPath !== null && $oldAvatarPath !== null && $oldAvatarPath !== $newAvatarPath) {
            Storage::disk('public')->delete($oldAvatarPath);
        }
        return redirect()->route($redirectRoute, $isEmployeeEdit ? $employee->id : [])->with('success', 'Профиль успешно сохранён.');
    }
}
