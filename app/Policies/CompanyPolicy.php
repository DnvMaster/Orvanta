<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Просмотр списка компаний.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('companies.view');
    }

    /**
     * Просмотр конкретной компании.
     */
    public function view(User $user, Company $company): bool
    {
        return $this->hasAccessToCompany($user, $company, 'companies.view');
    }

    /**
     * Создание компании.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('companies.create');
    }

    /**
     * Изменение компании.
     */
    public function update(User $user, Company $company): bool
    {
        return $this->hasAccessToCompany($user, $company, 'companies.update');
    }

    /**
     * Удаление компании.
     */
    public function delete(User $user, Company $company): bool
    {
        return $this->hasAccessToCompany($user, $company, 'companies.delete');
    }

    /**
     * Восстановление компании.
     */
    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Полное удаление компании.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Проверяет разрешение и область видимости компании.
     */
    private function hasAccessToCompany(User $user, Company $company, string $permission): bool {
        $scope = $user->getPermissionScope($permission);
        if ($scope === null) {
            return false;
        }
        return match ($scope) {
            'all' => true,
            'own' => $company->user_id === $user->id,
            'department' => $company->user_id !== null && in_array(
                $this->getCompanyOwnerDepartmentId($company),
                $user->accessibleDepartmentIds(),
                true
            ),
            default => false,
        };
    }

    /**
     * Получает ID отдела ответственного за компанию.
     */
    private function getCompanyOwnerDepartmentId(Company $company): ?int
    {
        $owner = User::query()->find($company->user_id);
        return $owner?->department_id;
    }
}
