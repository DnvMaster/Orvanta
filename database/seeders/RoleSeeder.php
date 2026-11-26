<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Администратор',
                'slug' => 'admin',
                'description' => 'Полный доступ к системе и управлению CRM.',
            ],
            [
                'name' => 'Сотрудник',
                'slug' => 'employee',
                'description' => 'Работа со своими клиентами, сделками и задачами.',
            ],
            [
                'name' => 'Руководитель',
                'slug' => 'manager',
                'description' => 'Работа с данными и задачами своего отдела и дочерних отделов.',
            ],
            [
                'name' => 'Технический специалист',
                'slug' => 'technical',
                'description' => 'Работа с назначенными техническими задачами.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_system' => true,
                ],
            );
        }
    }
}
