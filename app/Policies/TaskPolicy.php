<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskVisibilityService;

class TaskPolicy
{
    public function __construct(private readonly TaskVisibilityService $visibility)
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->isActive() && $user->hasPermissionTo('tasks.view');
    }

    public function view(User $user, Task $task): bool
    {
        return $this->visibility->canView($user, $task);
    }

    public function create(User $user): bool
    {
        return $user->isActive() && $user->hasPermissionTo('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        return $this->canManageTask($user, $task, 'tasks.update');
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->canManageTask($user, $task, 'tasks.delete');
    }

    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

    private function canManageTask(User $user, Task $task, string $permission): bool
    {
        return $user->isActive() && $user->hasPermissionTo($permission) && $this->visibility->canAccess($user, $task, $permission) && in_array($task->status, ['new', 'in_progress'], true);
    }
}
