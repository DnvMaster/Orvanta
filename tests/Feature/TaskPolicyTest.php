<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_view_permission_can_view_task_list(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $this->assertTrue($user->can('viewAny', Task::class));
    }

    public function test_user_without_view_permission_cannot_view_task_list(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->can('viewAny', Task::class));
    }

    public function test_user_with_create_permission_can_create_task(): void
    {
        $user = $this->createUserWithPermission('tasks.create');
        $this->assertTrue($user->can('create', Task::class));
    }

    public function test_user_with_update_permission_can_update_accessible_active_task(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $this->grantPermission($user, 'tasks.update');
        $task = $this->createTask($user, $user);
        $this->assertTrue($user->can('update', $task));
    }

    public function test_user_cannot_update_task_in_review_status(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $this->grantPermission($user, 'tasks.update');
        $task = $this->createTask($user, $user);
        $task->forceFill(['status' => 'review'])->save();
        $this->assertFalse($user->can('update', $task));
    }

    public function test_user_cannot_update_another_employees_task_with_own_scope(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $this->grantPermission($user, 'tasks.update');
        $anotherEmployee = User::factory()->create();
        $task = $this->createTask($anotherEmployee, $anotherEmployee);
        $this->assertFalse($user->can('update', $task));
    }

    private function createUserWithPermission(string $permissionSlug): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $this->grantPermission($user, $permissionSlug);

        return $user;
    }

    private function grantPermission(User $user, string $permissionSlug, string $scope = 'own'): void
    {
        $role = Role::query()->forceCreate([
            'name' => 'Tasks Policy Test Role',
            'slug' => 'tasks-policy-test-'.$user->id.'-'.str_replace('.', '-', $permissionSlug),
        ]);
        $permission = Permission::query()->firstOrCreate(
            ['slug' => $permissionSlug],
            [
                'name' => 'Tasks Policy Test Permission',
                'group' => 'tasks',
            ]
        );
        $role->permissions()->attach($permission->id, ['scope' => $scope]);
        $user->roles()->attach($role->id);
    }

    public function test_update_uses_its_own_permission_scope(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $this->grantPermission($user, 'tasks.update', 'all');
        $anotherEmployee = User::factory()->create();
        $task = $this->createTask($anotherEmployee, $anotherEmployee);
        $this->assertTrue($user->can('update', $task));
    }

    public function test_participant_cannot_update_task_based_on_membership_alone(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $this->grantPermission($user, 'tasks.update');
        $anotherEmployee = User::factory()->create();
        $task = $this->createTask($anotherEmployee, $anotherEmployee);
        TaskParticipant::query()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'added_by' => $anotherEmployee->id,
            'added_at' => now(),
        ]);
        $this->assertTrue($user->can('view', $task));
        $this->assertFalse($user->can('update', $task));
    }

    private function createTask(User $creator, User $assignee): Task
    {
        return Task::query()->forceCreate([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'title' => 'Policy test task',
            'status' => 'new',
            'priority' => 'normal',
        ]);
    }
}
