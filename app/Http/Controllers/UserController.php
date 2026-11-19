<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with(['organizationalDepartment', 'manager', 'roles'])->orderBy('name')->paginate(20);

        return view('orvanta.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('orvanta.users._form', [
            'user' => new User(['status' => 'active']),
            'departments' => Department::orderBy('name')->get(),
            'managers' => User::where('status', 'active')->orderBy('name')->get(),
            'roles' => Role::orderByDesc('is_system')->orderBy('name')->get(),
            'assignedRoleIds' => [],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->safe()->except(['password', 'roles']);
            $data['password'] = $request->validated('password');
            $user = User::create($data);
            $user->roles()->sync($request->validated('roles', []));
        });

        return redirect()->route('users.index')->with('success', 'Сотрудник успешно добавлен.');
    }

    public function edit(User $user): View
    {
        return view('orvanta.users._form', [
            'user' => $user,
            'departments' => Department::orderBy('name')->get(),
            'managers' => User::where('id', '!=', $user->id)->orderBy('name')->get(),
            'roles' => Role::orderByDesc('is_system')->orderBy('name')->get(),
            'assignedRoleIds' => $user->roles()->pluck('roles.id')->toArray(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user): void {
            $data = $request->safe()->except(['password', 'roles']);
            if ($request->filled('password')) {
                $data['password'] = $request->validated('password');
            }
            $user->update($data);
            $user->roles()->sync($request->validated('roles', []));
        });

        return redirect()->route('users.index')->with('success', 'Данные сотрудника и его роли обновлены.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Нельзя удалить собственную учетную запись.');
        }
        if ($user->employees()->exists()) {
            return back()->with('error', 'У этого сотрудника есть подчиненные. Сначала переназначьте им руководителя.');
        }
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Сотрудник удален.');
    }
}
