<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\Contact;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_own_company_but_not_another_employees_company(): void
    {
        $employee = $this->createUserWithPermission('companies.view', 'own');
        $anotherEmployee = User::factory()->create();
        $ownCompany = $this->createCompanyFor($employee);
        $anotherCompany = $this->createCompanyFor($anotherEmployee);
        $this->assertTrue($employee->can('view', $ownCompany));
        $this->assertFalse($employee->can('view', $anotherCompany));
    }

    public function test_admin_with_all_scope_can_view_any_company(): void
    {
        $admin = $this->createUserWithPermission('companies.view', 'all');
        $employee = User::factory()->create();
        $firstCompany = $this->createCompanyFor($admin);
        $secondCompany = $this->createCompanyFor($employee);
        $this->assertTrue($admin->can('view', $firstCompany));
        $this->assertTrue($admin->can('view', $secondCompany));
    }

    public function test_user_without_permission_cannot_view_company(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompanyFor($user);
        $this->assertFalse($user->can('viewAny', Company::class));
        $this->assertFalse($user->can('view', $company));
    }

    public function test_visible_to_scope_returns_only_accessible_companies(): void
    {
        $employee = $this->createUserWithPermission('companies.view', 'own');
        $anotherEmployee = User::factory()->create();
        $ownCompany = $this->createCompanyFor($employee);
        $this->createCompanyFor($anotherEmployee);
        $companies = Company::query()->visibleTo($employee, 'companies.view')->get();
        $this->assertCount(1, $companies);
        $this->assertTrue($companies->contains('id', $ownCompany->id));
    }

    public function test_employee_can_access_only_own_deals(): void
    {
        $employee = $this->createUserWithPermission('deals.view', 'own');
        $anotherEmployee = User::factory()->create();
        $ownDeal = $this->createDealFor($employee);
        $anotherDeal = $this->createDealFor($anotherEmployee);
        $this->assertTrue($employee->can('view', $ownDeal));
        $this->assertFalse($employee->can('view', $anotherDeal));
        $deals = Deal::query()->visibleTo($employee, 'deals.view')->get();
        $this->assertCount(1, $deals);
        $this->assertTrue($deals->contains('id', $ownDeal->id));
    }

    public function test_deal_is_not_accessible_when_its_company_is_not_accessible(): void
    {
        $employee = $this->createUserWithPermission('deals.view', 'own');
        $companyOwner = User::factory()->create();
        $company = $this->createCompanyFor($companyOwner);
        $deal = $this->createDealFor($employee, ['company_id' => $company->id]);
        $this->assertFalse($employee->can('view', $deal));
    }

    public function test_deal_is_not_accessible_when_its_contact_is_not_accessible(): void
    {
        $employee = $this->createUserWithPermission('deals.view', 'own');
        $this->grantPermission($employee, 'contacts.view', 'own');
        $contactOwner = User::factory()->create();
        $contact = $this->createContactFor($contactOwner);
        $deal = $this->createDealFor($employee, ['contact_id' => $contact->id]);
        $this->assertFalse($employee->can('view', $deal));
    }

    private function grantPermission(User $user, string $permissionSlug, string $scope): void
    {
        $role = Role::query()->forceCreate([
            'name' => 'Test Role',
            'slug' => 'test-role-' . $user->id . '-' . str_replace('.', '-', $permissionSlug) . '-' . $scope
        ]);
        $permission = Permission::query()->forceCreate([
            'name' => 'Test Permission',
            'slug' => $permissionSlug,
            'group' => explode('.', $permissionSlug)[0]
        ]);
        $role->permissions()->attach($permission->id, ['scope' => $scope]);
        $user->roles()->attach($role->id);
    }

    public function test_employee_can_view_only_own_contacts(): void
    {
        $employee = $this->createUserWithPermission('contacts.view', 'own');
        $anotherEmployee = User::factory()->create();
        $ownContact = $this->createContactFor($employee);
        $anotherContact = $this->createContactFor($anotherEmployee);
        $this->assertTrue($employee->can('view', $ownContact));
        $this->assertFalse($employee->can('view', $anotherContact));
    }

    public function test_employee_cannot_update_another_employees_contact(): void
    {
        $employee = $this->createUserWithPermission('contacts.update', 'own');
        $anotherEmployee = User::factory()->create();
        $anotherContact = $this->createContactFor($anotherEmployee);
        $this->assertFalse($employee->can('update', $anotherContact));
    }

    public function test_user_without_permission_cannot_create_contact(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->can('create', Contact::class));
    }

    public function test_contact_is_not_accessible_without_access_to_its_company(): void
    {
        $employee = $this->createUserWithPermission('contacts.view', 'own');
        $companyOwner = User::factory()->create();
        $company = $this->createCompanyFor($companyOwner);
        $contact = Contact::create([
            'first_name' => 'Test',
            'last_name' => 'Contact',
            'company_id' => $company->id,
        ]);
        $contact->responsible()->associate($employee);
        $contact->save();
        $this->assertFalse($employee->can('view', $contact));
    }

    public function test_manager_can_view_contacts_in_own_and_child_departments(): void
    {
        $manager = $this->createUserWithPermission('contacts.view', 'department');
        $mainDepartment = Department::query()->forceCreate(['name' => 'Main Contact Department',]);
        $childDepartment = Department::query()->forceCreate(['name' => 'Child Contact Department','parent_id' => $mainDepartment->id,]);
        $otherDepartment = Department::query()->forceCreate(['name' => 'Other Contact Department',]);
        $manager->update(['department_id' => $mainDepartment->id]);
        $childEmployee = User::factory()->create(['department_id' => $childDepartment->id,]);
        $otherEmployee = User::factory()->create(['department_id' => $otherDepartment->id,]);
        $ownContact = $this->createContactFor($manager);
        $childContact = $this->createContactFor($childEmployee);
        $otherContact = $this->createContactFor($otherEmployee);
        $this->assertTrue($manager->can('view', $ownContact));
        $this->assertTrue($manager->can('view', $childContact));
        $this->assertFalse($manager->can('view', $otherContact));
    }

    public function test_employee_cannot_view_another_employees_company_through_http(): void
    {
        $employee = $this->createUserWithPermission('companies.view', 'own');
        $anotherEmployee = User::factory()->create();
        $company = $this->createCompanyFor($anotherEmployee);
        $this->actingAs($employee)->get(route('companies.show', $company))->assertForbidden();
    }

    public function test_employee_without_management_permissions_cannot_create_update_or_delete_company(): void
    {
        $employee = $this->createUserWithPermission('companies.view', 'own');
        $company = $this->createCompanyFor($employee);
        $this->actingAs($employee);
        $this->get(route('companies.create'))->assertForbidden();
        $this->post(route('companies.store'), [])->assertForbidden();
        $this->get(route('companies.edit', $company))->assertForbidden();
        $this->put(route('companies.update', $company), [])->assertForbidden();
        $this->delete(route('companies.destroy', $company))->assertForbidden();
    }

    private function createUserWithPermission(string $permissionSlug, string $scope): User
    {
        $user = User::factory()->create();
        $this->grantPermission($user, $permissionSlug, $scope);
        return $user;
    }

    private function createCompanyFor(User $user): Company
    {
        $company = Company::create(['name' => 'Test Company ' . $user->id]);
        $company->responsible()->associate($user);
        $company->save();
        return $company;
    }

    private function createContactFor(User $user): Contact
    {
        $contact = Contact::create(['first_name' => 'Test','last_name' => 'Contact ' . $user->id]);
        $contact->responsible()->associate($user);
        $contact->save();
        return $contact;
    }

    private function createDealFor(User $user, array $attributes = []): Deal
    {
        $pipeline = Pipeline::create(['name' => 'Test Pipeline']);
        $stage = PipelineStage::create(['pipeline_id' => $pipeline->id,'name' => 'Test Stage','position' => 1]);
        $deal = Deal::create(array_merge([
            'pipeline_id' => $pipeline->id,
            'pipeline_stage_id' => $stage->id,
            'title' => 'Test Deal',
            'amount' => 1000,
            'currency' => 'RUB'
        ], $attributes));
        $deal->responsible()->associate($user);
        $deal->save();
        return $deal;
    }

    public function test_manager_can_view_companies_in_own_and_child_departments_but_not_other_departments(): void
    {
        $manager = $this->createUserWithPermission('companies.view', 'department');
        $mainDepartment = Department::query()->forceCreate(['name' => 'Main Test Department']);
        $childDepartment = Department::query()->forceCreate(['name' => 'Child Test Department','parent_id' => $mainDepartment->id]);
        $otherDepartment = Department::query()->forceCreate(['name' => 'Other Test Department']);
        $manager->update(['department_id' => $mainDepartment->id,]);
        $childEmployee = User::factory()->create(['department_id' => $childDepartment->id]);
        $otherEmployee = User::factory()->create(['department_id' => $otherDepartment->id,]);
        $ownCompany = $this->createCompanyFor($manager);
        $childCompany = $this->createCompanyFor($childEmployee);
        $otherCompany = $this->createCompanyFor($otherEmployee);
        $this->assertTrue($manager->can('view', $ownCompany));
        $this->assertTrue($manager->can('view', $childCompany));
        $this->assertFalse($manager->can('view', $otherCompany));
    }
}