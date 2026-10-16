<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::query()
            ->select([
                'id',
                'name',
                'email',
                'role',
                'status',
                'phone',
                'position',
                'department',
                'created_at',
            ])
            ->orderBy('name')
            ->paginate(15);

        return view('orvanta.employees.index', [
            'employees' => $employees,
        ]);
    }

    public function create(): View
    {
        return view('orvanta.employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
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
            'password' => $temporaryPassword,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Сотрудник успешно создан.')
            ->with('temporary_password', $temporaryPassword)
            ->with('created_employee_email', $employee->email);
    }
}
