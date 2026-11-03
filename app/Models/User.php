<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'must_change_password', 'role', 'status', 'phone', 'position', 'department', 'department_id', 'manager_id', 'avatar_path', 'invited_at'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'invited_at' => 'datetime', 'password' => 'hashed', 'must_change_password' => 'boolean'];
    }

    public function organizationalDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->getPermissionScope($permission) !== null;
    }

    public function getPermissionScope(string $permission): ?string
    {
        $scopes = $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('permissions.slug', $permission);
        })->with(['permissions' => function ($query) use ($permission) {
            $query->where('permissions.slug', $permission);
        }])->get()->flatMap(fn ($role) => $role->permissions->pluck('pivot.scope'))->unique()->all();
        foreach (['all', 'department', 'own'] as $scope) {
            if (in_array($scope, $scopes, true)) {
                return $scope;
            }
        }

        return null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function accessibleDepartmentIds(): array
    {
        if (! $this->department_id) {
            return [];
        }
        $department = $this->organizationalDepartment;
        if (! $department) {
            return [];
        }

        return array_merge([$department->id], $department->descendantIds());
    }
}
