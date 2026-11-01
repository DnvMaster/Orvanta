<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

class DealPolicy
{
    /**
     * Просмотр списка сделок.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('deals.view');
    }

    /**
     * Просмотр конкретной сделки.
     */
    public function view(User $user, Deal $deal): bool
    {
        return $this->hasAccessToDeal($user, $deal, 'deals.view');
    }

    /**
     * Создание сделки.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('deals.create');
    }

    /**
     * Изменение сделки.
     */
    public function update(User $user, Deal $deal): bool
    {
        return $this->hasAccessToDeal($user, $deal, 'deals.update');
    }

    /**
     * Удаление сделки.
     */
    public function delete(User $user, Deal $deal): bool
    {
        return $this->hasAccessToDeal($user, $deal, 'deals.delete');
    }

    /**
     * Восстановление сделки.
     */
    public function restore(User $user, Deal $deal): bool
    {
        return false;
    }

    /**
     * Полное удаление сделки.
     */
    public function forceDelete(User $user, Deal $deal): bool
    {
        return false;
    }

    /**
     * Проверяет разрешение и область видимости сделки.
     */
    private function hasAccessToDeal(User $user, Deal $deal, string $permission): bool {
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
        if (!$hasDealAccess) {
            return false;
        }
        // Проверяем доступ к связанной компании.
        if ($deal->company_id !== null) {
            $company = $deal->company;
            if ($company === null || ! $user->can('view', $company)) {
                return false;
            }
        }

        // Проверяем доступ к связанному контакту.
        if ($deal->contact_id !== null) {
            $contact = $deal->contact;
            if ($contact === null || ! $user->can('view', $contact)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Проверяет отдел ответственного за сделку.
     */
    private function belongsToAccessibleDepartment( User $user, Deal $deal): bool {
        $owner = $deal->responsible;
        if ($owner === null || $owner->department_id === null) {
            return false;
        }
        return in_array((int) $owner->department_id, $user->accessibleDepartmentIds(), true);
    }
}
