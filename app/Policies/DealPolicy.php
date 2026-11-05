<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('deals.view');
    }

    public function view(User $user, Deal $deal): bool
    {
        return $this->hasAccessToDeal($user, $deal, 'deals.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('deals.create');
    }

    public function update(User $user, Deal $deal): bool
    {
        return $this->hasAccessToDeal($user, $deal, 'deals.update');
    }

    public function delete(User $user, Deal $deal): bool
    {
        return $this->hasAccessToDeal($user, $deal, 'deals.delete');
    }

    public function restore(User $user, Deal $deal): bool
    {
        return false;
    }

    public function forceDelete(User $user, Deal $deal): bool
    {
        return false;
    }

    private function hasAccessToDeal(User $user, Deal $deal, string $permission): bool
    {
        $scope = $user->getPermissionScope($permission);
        if ($scope === null) {
            return false;
        }
        $hasDealAccess = match ($scope) {
            'all' => true,
            'own' => $deal->user_id !== null && (int) $deal->user_id === (int) $user->getKey(),
            'department' => $this->belongsToAccessibleDepartment($user, $deal),
            default => false
        };
        if (! $hasDealAccess) {
            return false;
        }
        if ($deal->company_id !== null) {
            $company = $deal->company;
            if ($company === null || ! $user->can('view', $company)) {
                return false;
            }
        }
        if ($deal->contact_id !== null) {
            $contact = $deal->contact;
            if ($contact === null || ! $user->can('view', $contact)) {
                return false;
            }
        }

        return true;
    }

    private function belongsToAccessibleDepartment(User $user, Deal $deal): bool
    {
        $owner = $deal->responsible;
        if ($owner === null || $owner->department_id === null) {
            return false;
        }

        return in_array((int) $owner->department_id, $user->accessibleDepartmentIds(), true);
    }
}
