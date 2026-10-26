<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialPipelineSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('pipelines')->where('name', 'Основные продажи')->exists()) {
            return;
        }
        DB::transaction(function (): void {
            $now = now();
            $pipelineId = DB::table('pipelines')->insertGetId([
                'name' => 'Основные продажи',
                'is_default' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now
            ]);
            $stages = [
                ['name' => 'Новая заявка', 'position' => 1, 'type' => 'open'],
                ['name' => 'Первичный контакт', 'position' => 2, 'type' => 'open'],
                ['name' => 'Переговоры', 'position' => 3, 'type' => 'open'],
                ['name' => 'Успешно закрыта', 'position' => 4, 'type' => 'won'],
                ['name' => 'Закрыта без продажи', 'position' => 5, 'type' => 'lost']
            ];
            foreach ($stages as $stage) {
                DB::table('pipeline_stages')->insert([
                    'pipeline_id' => $pipelineId,
                    'name' => $stage['name'],
                    'position' => $stage['position'],
                    'type' => $stage['type'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        });
    }
}
