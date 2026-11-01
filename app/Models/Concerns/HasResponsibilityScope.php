<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasResponsibilityScope
{
    public function scopeVisibleTo(Builder $query, User $user, string $permission): Builder
    {
        $scope = $user->getPermissionScope($permission);

        return match ($scope) {
            'all' => $query,
            'department' => $this->applyDepartmentScope($query, $user),
            'own' => $query->where('user_id', $user->getKey()),
            default => $query->whereRaw('1 = 0')
        };
    }

    private function applyDepartmentScope(Builder $query, User $user): Builder
    {
        $departmentIds = $user->accessibleDepartmentIds();

        if ($departmentIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('user_id', User::query()->select('id')->whereIn('department_id', $departmentIds));
    }
}
