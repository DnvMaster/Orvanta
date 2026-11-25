<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $permissions = [
            ['name' => 'Просмотр задач', 'slug' => 'tasks.view'],
            ['name' => 'Создание задач', 'slug' => 'tasks.create'],
            ['name' => 'Редактирование задач', 'slug' => 'tasks.update'],
            ['name' => 'Удаление задач', 'slug' => 'tasks.delete'],
            ['name' => 'Восстановление задач', 'slug' => 'tasks.restore'],
            ['name' => 'Назначение задач', 'slug' => 'tasks.assign'],
            ['name' => 'Переназначение задач', 'slug' => 'tasks.reassign'],
            ['name' => 'Управление участниками задач', 'slug' => 'tasks.manage_participants'],
            ['name' => 'Проверка задач', 'slug' => 'tasks.review'],
            ['name' => 'Отмена задач', 'slug' => 'tasks.cancel'],
            ['name' => 'Повторное открытие задач', 'slug' => 'tasks.reopen'],
            ['name' => 'Добавление комментариев к задачам', 'slug' => 'tasks.comment'],
            ['name' => 'Удаление комментариев к задачам', 'slug' => 'tasks.delete_comments'],
            ['name' => 'Работа с вложениями задач', 'slug' => 'tasks.attachments'],
            ['name' => 'Удаление вложений задач', 'slug' => 'tasks.delete_attachments'],
            ['name' => 'Просмотр истории задач', 'slug' => 'tasks.view_activity'],
            ['name' => 'Просмотр удалённых задач', 'slug' => 'tasks.view_deleted'],
            ['name' => 'Просмотр отчётов по задачам', 'slug' => 'tasks.view_reports'],
        ];
        foreach ($permissions as &$permission) {
            $permission['group'] = 'tasks';
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }
        unset($permission);
        DB::table('permissions')->insertOrIgnore($permissions);
        $adminRoleId = DB::table('roles')->where('slug', 'admin')->value('id');
        if ($adminRoleId !== null) {
            $permissionIds = DB::table('permissions')->whereIn('slug', array_column($permissions, 'slug'))->pluck('id');
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $adminRoleId,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'scope' => 'all',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                );
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')->where('group', 'tasks')->whereIn('slug', ['tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete', 'tasks.restore', 'tasks.assign', 'tasks.reassign', 'tasks.manage_participants', 'tasks.review', 'tasks.cancel', 'tasks.reopen', 'tasks.comment', 'tasks.delete_comments', 'tasks.attachments', 'tasks.delete_attachments', 'tasks.view_activity', 'tasks.view_deleted', 'tasks.view_reports'])->pluck('id');
        DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
