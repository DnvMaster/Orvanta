<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;

class DashboardService
{
    /**
     * Собирает показатели дашборда с учётом разрешений пользователя.
     */
    public function getOverview(User $user): array
    {
        return [
            'employeesCount' => $this->getEmployeesCount($user),
            'companiesCount' => $this->getCompaniesCount($user),
            'contactsCount' => $this->getContactsCount($user),
            'dealsCount' => $this->getDealsCount($user),
            'openDealsCount' => $this->getOpenDealsCount($user),
            'openDealAmounts' => $this->getOpenDealAmounts($user),
        ];
    }

    /**
     * Количество активных сотрудников в доступной области.
     */
    private function getEmployeesCount(User $user): ?int
    {
        if (! $user->hasPermissionTo('employees.view')) {
            return null;
        }

        $query = User::query()->where('status', 'active');
        $scope = $user->getPermissionScope('employees.view');
        if ($scope === 'department') {
            $departmentIds = $user->accessibleDepartmentIds();
            if ($departmentIds === []) {
                return 0;
            }
            $query->whereIn('department_id', $departmentIds);
        } elseif ($scope === 'own') {
            $query->whereKey($user->getKey());
        }
        return $query->count();
    }

    /**
     * Количество доступных компаний.
     */
    private function getCompaniesCount(User $user): ?int
    {
        if (! $user->hasPermissionTo('companies.view')) {
            return null;
        }
        return Company::query()->visibleTo($user, 'companies.view')->count();
    }

    /**
     * Количество доступных контактов.
     */
    private function getContactsCount(User $user): ?int
    {
        if (! $user->hasPermissionTo('contacts.view')) {
            return null;
        }
        return Contact::query()->visibleTo($user, 'contacts.view')->count();
    }

    /**
     * Общее количество доступных сделок.
     */
    private function getDealsCount(User $user): ?int
    {
        if (! $user->hasPermissionTo('deals.view')) {
            return null;
        }
        return Deal::query()->visibleTo($user, 'deals.view')->count();
    }

    /**
     * Количество открытых сделок.
     */
    private function getOpenDealsCount(User $user): ?int
    {
        if (! $user->hasPermissionTo('deals.view')) {
            return null;
        }
        return Deal::query()->visibleTo($user, 'deals.view')->whereHas('stage', fn ($query) => $query->where('type', 'open'))->count();
    }

    /**
     * Суммы открытых сделок отдельно по каждой валюте.
     */
    private function getOpenDealAmounts(User $user): array
    {
        if (! $user->hasPermissionTo('deals.view')) {
            return [];
        }
        return Deal::query()->visibleTo($user, 'deals.view')->whereHas('stage', fn ($query) => $query->where('type', 'open'))->selectRaw('currency, SUM(amount) as total')->groupBy('currency')->orderBy('currency')->get()->map(fn ($deal) => [
            'currency' => $deal->currency,
            'total' => (string) $deal->total
        ])->toArray();
    }
}
