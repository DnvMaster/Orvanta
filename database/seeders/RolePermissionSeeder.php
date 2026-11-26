<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'slug');
        $permissions = DB::table('permissions')->pluck('id', 'slug');
        foreach (['admin', 'employee', 'manager', 'technical'] as $slug) {
            if (! $roles->has($slug)) {
                throw new RuntimeException("Системная роль  [{$slug}] не найдена.");
            }
        }

        $allPermissions = $permissions->keys()->all();
        $crmPermissions = ['companies.view', 'companies.create', 'companies.update', 'companies.delete', 'contacts.view', 'contacts.create', 'contacts.update', 'contacts.delete', 'deals.view', 'deals.create', 'deals.update', 'deals.delete'];
        $employeeTasks = ['tasks.view', 'tasks.create', 'tasks.update', 'tasks.comment', 'tasks.attachments', 'tasks.view_activity'];

        $managerTasks = [...$employeeTasks, 'tasks.view_reports'];

        $assignments = [
            'admin' => [
                'permissions' => $allPermissions,
                'scope' => 'all',
            ],
            'employee' => [
                'permissions' => [
                    'dashboard.view',
                    ...$crmPermissions,
                    ...$employeeTasks,
                ],
                'scope' => 'own',
            ],
            'manager' => [
                'permissions' => [
                    'dashboard.view',
                    ...$crmPermissions,
                    ...$managerTasks,
                ],
                'scope' => 'department',
            ],
            'technical' => [
                'permissions' => [
                    'dashboard.view',
                    ...$employeeTasks,
                ],
                'scope' => 'own',
            ],
        ];

        DB::transaction(function () use ($assignments, $roles, $permissions): void {
            foreach ($assignments as $roleSlug => $assignment) {
                foreach ($assignment['permissions'] as $permissionSlug) {
                    if (! $permissions->has($permissionSlug)) {
                        throw new RuntimeException(
                            "Permission [{$permissionSlug}] was not found."
                        );
                    }

                    DB::table('role_permissions')->updateOrInsert(
                        [
                            'role_id' => $roles[$roleSlug],
                            'permission_id' => $permissions[$permissionSlug],
                        ],
                        [
                            'scope' => $assignment['scope'],
                            'updated_at' => now(),
                            'created_at' => now(),
                        ],
                    );
                }
            }
        });
    }
}
