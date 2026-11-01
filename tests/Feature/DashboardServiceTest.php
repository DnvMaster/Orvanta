<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Department;
use App\Models\Permission;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Role;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_dashboard_statistics(): void
    {
        $admin = User::factory()->create();
        foreach (['employees.view','companies.view','contacts.view','deals.view'] as $permission) {
            $this->grantPermission($admin, $permission, 'all');
        }
        $employee = User::factory()->create();
        $this->createCompanyFor($employee);
        $this->createCompanyFor($admin);
        $this->createContactFor($employee);
        $this->createContactFor($admin);
        $this->createDealFor($employee, ['amount' => 100000,'currency' => 'USD']);
        $this->createDealFor($admin, ['amount' => 50000,'currency' => 'EUR']);
        $statistics = app(DashboardService::class)->getOverview($admin);
        $this->assertSame(2, $statistics['employeesCount']);
        $this->assertSame(2, $statistics['companiesCount']);
        $this->assertSame(2, $statistics['contactsCount']);
        $this->assertSame(2, $statistics['dealsCount']);
        $this->assertSame(2, $statistics['openDealsCount']);
        $this->assertSame(['EUR', 'USD'], array_column($statistics['openDealAmounts'], 'currency'));
        $this->assertEquals([50000, 100000], array_map('floatval', array_column($statistics['openDealAmounts'], 'total')));
    }

    public function test_employee_can_view_only_own_dashboard_data(): void
    {
        $employee = User::factory()->create();
        $anotherEmployee = User::factory()->create();
        foreach (['companies.view','contacts.view','deals.view'] as $permission) {
            $this->grantPermission($employee, $permission, 'own');
        }
        $this->createCompanyFor($employee);
        $this->createCompanyFor($anotherEmployee);
        $this->createContactFor($employee);
        $this->createContactFor($anotherEmployee);
        $this->createDealFor($employee);
        $this->createDealFor($anotherEmployee);
        $statistics = app(DashboardService::class)->getOverview($employee);
        $this->assertNull($statistics['employeesCount']);
        $this->assertSame(1, $statistics['companiesCount']);
        $this->assertSame(1, $statistics['contactsCount']);
        $this->assertSame(1, $statistics['dealsCount']);
        $this->assertSame(1, $statistics['openDealsCount']);
        $this->assertCount(1, $statistics['openDealAmounts']);
    }

    public function test_manager_can_view_dashboard_data_from_own_and_child_departments(): void
    {
        $manager = User::factory()->create();
        $mainDepartment = Department::query()->forceCreate(['name' => 'Dashboard Main Department']);
        $childDepartment = Department::query()->forceCreate(['name' => 'Dashboard Child Department','parent_id' => $mainDepartment->id]);
        $otherDepartment = Department::query()->forceCreate(['name' => 'Dashboard Other Department',]);
        $manager->update(['department_id' => $mainDepartment->id]);
        $childEmployee = User::factory()->create(['department_id' => $childDepartment->id]);
        $otherEmployee = User::factory()->create(['department_id' => $otherDepartment->id]);
        foreach (['employees.view','companies.view','contacts.view','deals.view'] as $permission) {
            $this->grantPermission($manager, $permission, 'department');
        }
        $this->createCompanyFor($manager);
        $this->createCompanyFor($childEmployee);
        $this->createCompanyFor($otherEmployee);
        $this->createContactFor($manager);
        $this->createContactFor($childEmployee);
        $this->createContactFor($otherEmployee);
        $this->createDealFor($manager);
        $this->createDealFor($childEmployee);
        $this->createDealFor($otherEmployee);
        $statistics = app(DashboardService::class)->getOverview($manager);
        $this->assertSame(2, $statistics['employeesCount']);
        $this->assertSame(2, $statistics['companiesCount']);
        $this->assertSame(2, $statistics['contactsCount']);
        $this->assertSame(2, $statistics['dealsCount']);
        $this->assertSame(2, $statistics['openDealsCount']);
    }

    public function test_user_without_deals_permission_cannot_see_deal_statistics(): void
    {
        $employee = User::factory()->create();
        $this->grantPermission($employee, 'companies.view', 'own');
        $this->grantPermission($employee, 'contacts.view', 'own');
        // Сделка существует и принадлежит сотруднику.
        $this->createDealFor($employee, ['amount' => 75000,'currency' => 'EUR']);
        $statistics = app(DashboardService::class)->getOverview($employee);
        $this->assertNull($statistics['dealsCount']);
        $this->assertNull($statistics['openDealsCount']);
        $this->assertSame([], $statistics['openDealAmounts']);
    }

    public function test_manager_without_department_sees_zero_employees(): void
    {
        $manager = User::factory()->create();
        $this->grantPermission($manager, 'employees.view', 'department');
        User::factory()->count(3)->create();
        $statistics = app(DashboardService::class)->getOverview($manager);
        $this->assertSame(0, $statistics['employeesCount']);
    }

    private function grantPermission(User $user, string $permissionSlug, string $scope): void
    {
        $role = Role::query()->forceCreate(['name' => 'Dashboard Test Role','slug' => 'dashboard-test-' . $user->id . '-' . str_replace('.', '-', $permissionSlug)]);
        $permission = Permission::query()->forceCreate([
            'name' => 'Dashboard Test Permission',
            'slug' => $permissionSlug,
            'group' => explode('.', $permissionSlug)[0]
        ]);
        $role->permissions()->attach($permission->id, ['scope' => $scope]);
        $user->roles()->attach($role->id);
    }

    private function createCompanyFor(User $user): Company
    {
        $company = Company::create(['name' => 'Dashboard Test Company ' . uniqid(),]);
        $company->responsible()->associate($user);
        $company->save();
        return $company;
    }

    private function createContactFor(User $user): Contact
    {
        $contact = Contact::create(['first_name' => 'Test','last_name' => 'Contact ' . $user->id . ' ' . uniqid()]);
        $contact->responsible()->associate($user);
        $contact->save();
        return $contact;
    }

    private function createDealFor(User $user, array $attributes = []): Deal {
        $pipeline = Pipeline::create([
            'name' => 'Dashboard Test Pipeline ' . uniqid(),
        ]);
        $stage = PipelineStage::create([
            'pipeline_id' => $pipeline->id,
            'name' => 'Test Stage',
            'position' => 1,
        ]);
        $deal = Deal::create(array_merge([
            'pipeline_id' => $pipeline->id,
            'pipeline_stage_id' => $stage->id,
            'title' => 'Dashboard Test Deal',
            'amount' => 1000,
            'currency' => 'RUB',
        ], $attributes));
        $deal->responsible()->associate($user);
        $deal->save();
        return $deal;
    }
}
