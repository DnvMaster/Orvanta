<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;

class TaskActivityService
{
    public function record(Task $task, ?User $actor, string $eventType, ?array $oldValues = null, ?array $newValues = null, ?array $metadata = null): TaskActivity
    {
        return $task->activities()->create([
            'user_id' => $actor?->getKey(),
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]);
    }
}
