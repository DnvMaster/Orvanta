<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Просмотр главной страницы', 'slug' => 'dashboard.view', 'group' => 'dashboard'],
            ['name' => 'Просмотр сотрудников', 'slug' => 'employees.view', 'group' => 'employees'],
            ['name' => 'Просмотр компаний', 'slug' => 'companies.view', 'group' => 'companies'],
            ['name' => 'Создание компаний', 'slug' => 'companies.create', 'group' => 'companies'],
            ['name' => 'Редактирование компаний', 'slug' => 'companies.update', 'group' => 'companies'],
            ['name' => 'Удаление компаний', 'slug' => 'companies.delete', 'group' => 'companies'],
            ['name' => 'Просмотр контактов', 'slug' => 'contacts.view', 'group' => 'contacts'],
            ['name' => 'Создание контактов', 'slug' => 'contacts.create', 'group' => 'contacts'],
            ['name' => 'Редактирование контактов', 'slug' => 'contacts.update', 'group' => 'contacts'],
            ['name' => 'Удаление контактов', 'slug' => 'contacts.delete', 'group' => 'contacts'],
            ['name' => 'Просмотр сделок', 'slug' => 'deals.view', 'group' => 'deals'],
            ['name' => 'Создание сделок', 'slug' => 'deals.create', 'group' => 'deals'],
            ['name' => 'Редактирование сделок', 'slug' => 'deals.update', 'group' => 'deals'],
            ['name' => 'Удаление сделок', 'slug' => 'deals.delete', 'group' => 'deals'],
            ['name' => 'Просмотр задач', 'slug' => 'tasks.view', 'group' => 'tasks'],
            ['name' => 'Создание задач', 'slug' => 'tasks.create', 'group' => 'tasks'],
            ['name' => 'Редактирование задач', 'slug' => 'tasks.update', 'group' => 'tasks'],
            ['name' => 'Удаление задач', 'slug' => 'tasks.delete', 'group' => 'tasks'],
            ['name' => 'Восстановление задач', 'slug' => 'tasks.restore', 'group' => 'tasks'],
            ['name' => 'Назначение задач другим сотрудникам', 'slug' => 'tasks.assign', 'group' => 'tasks'],
            ['name' => 'Переназначение задач', 'slug' => 'tasks.reassign', 'group' => 'tasks'],
            ['name' => 'Управление участниками задач', 'slug' => 'tasks.manage_participants', 'group' => 'tasks'],
            ['name' => 'Проверка выполнения задач', 'slug' => 'tasks.review', 'group' => 'tasks'],
            ['name' => 'Отмена задач', 'slug' => 'tasks.cancel', 'group' => 'tasks'],
            ['name' => 'Повторное открытие задач', 'slug' => 'tasks.reopen', 'group' => 'tasks'],
            ['name' => 'Комментирование задач', 'slug' => 'tasks.comment', 'group' => 'tasks'],
            ['name' => 'Удаление комментариев', 'slug' => 'tasks.delete_comments', 'group' => 'tasks'],
            ['name' => 'Добавление вложений к задачам', 'slug' => 'tasks.attachments', 'group' => 'tasks'],
            ['name' => 'Удаление вложений задач', 'slug' => 'tasks.delete_attachments', 'group' => 'tasks'],
            ['name' => 'Просмотр истории задач', 'slug' => 'tasks.view_activity', 'group' => 'tasks'],
            ['name' => 'Просмотр удалённых задач', 'slug' => 'tasks.view_deleted', 'group' => 'tasks'],
            ['name' => 'Просмотр отчётов по задачам', 'slug' => 'tasks.view_reports', 'group' => 'tasks'],
        ];

        $now = now();

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'description' => null,
                    'group' => $permission['group'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }
}
