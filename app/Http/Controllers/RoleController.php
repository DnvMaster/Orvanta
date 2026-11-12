<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount(['users','permissions'])->orderByDesc('is_system')->orderBy('name')->get();
        return view('orvanta.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $groupedPermissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
        return view('orvanta.roles._form', [
            'role' => new Role(),
            'groupedPermissions' => $groupedPermissions, 
            'assignedPermissions' => [] 
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $role = Role::create([
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug'),
                'description' => $request->validated('description'),
                'is_system' => false
            ]);
            $this->syncPermissions($role, $request->validated('permissions', []));
        });
        return redirect()->route('roles.index')->with('success', 'Роль успешно создана.');
    }

    public function edit(Role $role): View
    {
        $groupedPermissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $assignedPermissions = $role->permissions()->pluck('role_permissions.scope', 'permissions.id')->toArray();
        return view('orvanta.roles._form', compact('role', 'groupedPermissions', 'assignedPermissions'));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        DB::transaction(function () use ($request, $role): void {
            $data = [
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
            ];
            if (! $role->is_system && $request->filled('slug')) {
                $data['slug'] = $request->validated('slug');
            }
            $role->update($data);
            $this->syncPermissions($role, $request->validated('permissions', []));
        });
        return redirect()->route('roles.index')->with('success', 'Настройки роли обновлены.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'Системную роль нельзя удалить.');
        }
        if ($role->users()->exists()) {
            return back()->with('error', 'Нельзя удалить роль, пока она назначена сотрудникам.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Роль удалена.');
    }

    private function syncPermissions(Role $role, array $permissions): void
    {
        $syncData = [];
        foreach($permissions as $permissionId => $scope) {
            if(! empty($scope)) {
                $syncData[(int) $permissionId] = ['scope' => $scope];
            }
        }
        $role->permissions()->sync($syncData);
    }
}
