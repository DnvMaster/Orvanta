<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('companies.view');
    }

    public function view(User $user, Company $company): bool
    {
        return $this->hasAccessToCompany($user, $company, 'companies.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('companies.create');
    }

    public function update(User $user, Company $company): bool
    {
        return $this->hasAccessToCompany($user, $company, 'companies.update');
    }

    public function delete(User $user, Company $company): bool
    {
        return $this->hasAccessToCompany($user, $company, 'companies.delete');
    }

    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }

    private function hasAccessToCompany(User $user, Company $company, string $permission): bool
    {
        $scope = $user->getPermissionScope($permission);
        if ($scope === null) {
            return false;
        }

        return match ($scope) {
            'all' => true,
            'own' => (int) $company->user_id === (int) $user->getKey(),
            'department' => $company->user_id !== null && in_array(
                $this->getCompanyOwnerDepartmentId($company),
                $user->accessibleDepartmentIds(),
                true
            ),
            default => false,
        };
    }

    private function getCompanyOwnerDepartmentId(Company $company): ?int
    {
        $owner = User::query()->find($company->user_id);

        return $owner?->department_id;
    }
}
