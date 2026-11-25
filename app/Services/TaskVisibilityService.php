<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TaskVisibilityService
{
    public function queryFor(User $user, string $permission = 'tasks.view'): Builder
    {
        $query = Task::query();
        $scope = $user->getPermissionScope($permission);
        if ($scope === null || ! $user->isActive()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $visibilityQuery) use ($user, $scope, $permission): void {
            $visibilityQuery->where(function (Builder $scopeQuery) use ($user, $scope): void {
                if ($scope === 'all') {
                    $scopeQuery->whereNotNull('tasks.id');

                    return;
                }

                $scopeQuery->where(function (Builder $ownQuery) use ($user, $scope): void {
                    $ownQuery->where('created_by', $user->getKey())->orWhere('assigned_to', $user->getKey());
                    if ($scope === 'department') {
                        $departmentIds = $user->accessibleDepartmentIds();
                        if ($departmentIds !== []) {
                            $ownQuery->orWhereHas('assignee', function (Builder $assigneeQuery) use ($departmentIds): void {
                                $assigneeQuery->whereIn('department_id', $departmentIds);
                            });
                        }
                    }
                });
            });

            if ($permission === 'tasks.view') {
                $visibilityQuery->orWhereHas('participants', function (Builder $participantQuery) use ($user): void {
                    $participantQuery->where('user_id', $user->getKey())->whereNull('removed_at');
                });
            }
        });
    }

    public function queryForList(User $user, string $filter = 'all'): Builder
    {
        $query = $this->queryFor($user);

        return match ($filter) {
            'my' => $query->where('assigned_to', $user->getKey()),
            'created' => $query->where('created_by', $user->getKey()),
            'participating' => $query->whereHas('participants', function (Builder $participantQuery) use ($user): void {
                $participantQuery->where('user_id', $user->getKey())->whereNull('removed_at');
            }),
            'review' => $query->where('assigned_to', $user->getKey())->where('status', 'review'),
            'all' => $query,
            default => throw new \InvalidArgumentException('Неизвестный фильтр задач.'),
        };
    }

    public function canAccess(User $user, Task $task, string $permission): bool
    {
        return $this->queryFor($user, $permission)->whereKey($task->getKey())->exists();
    }

    public function canView(User $user, Task $task): bool
    {
        return $this->canAccess($user, $task, 'tasks.view');
    }
}
