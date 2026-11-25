<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskParticipant;
use App\Models\User;
use App\Services\TaskVisibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskVisibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private TaskVisibilityService $visibility;

    protected function setUp(): void
    {
        parent::setUp();
        $this->visibility = app(TaskVisibilityService::class);
    }

    public function test_own_scope_returns_tasks_created_by_or_assigned_to_user(): void
    {
        $employee = $this->createUserWithPermission('own');
        $anotherEmployee = User::factory()->create(['status' => 'active']);
        $createdByMe = $this->createTask($employee, $anotherEmployee);
        $assignedToMe = $this->createTask($anotherEmployee, $employee);
        $unrelated = $this->createTask($anotherEmployee, User::factory()->create());
        $tasks = $this->visibility->queryFor($employee)->get();
        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains('id', $createdByMe->id));
        $this->assertTrue($tasks->contains('id', $assignedToMe->id));
        $this->assertFalse($tasks->contains('id', $unrelated->id));
    }

    public function test_department_scope_includes_own_and_child_department_tasks(): void
    {
        $manager = $this->createUserWithPermission('department');
        $department = Department::query()->forceCreate([
            'name' => 'Main Department',
        ]);
        $childDepartment = Department::query()->forceCreate([
            'name' => 'Child Department',
            'parent_id' => $department->id,
        ]);
        $otherDepartment = Department::query()->forceCreate([
            'name' => 'Other Department',
        ]);
        $manager->update(['department_id' => $department->id]);
        $childEmployee = User::factory()->create([
            'department_id' => $childDepartment->id,
            'status' => 'active',
        ]);
        $otherEmployee = User::factory()->create([
            'department_id' => $otherDepartment->id,
            'status' => 'active',
        ]);

        $externalCreator = User::factory()->create([
            'status' => 'active',
        ]);

        $ownTask = $this->createTask($manager, $manager);
        $childTask = $this->createTask($externalCreator, $childEmployee);
        $otherTask = $this->createTask($externalCreator, $otherEmployee);

        $tasks = $this->visibility->queryFor($manager)->get();
        $this->assertTrue($tasks->contains('id', $ownTask->id));
        $this->assertTrue($tasks->contains('id', $childTask->id));
        $this->assertFalse($tasks->contains('id', $otherTask->id));
    }

    public function test_all_scope_returns_all_tasks(): void
    {
        $manager = $this->createUserWithPermission('all');
        $anotherEmployee = User::factory()->create();
        $firstTask = $this->createTask($manager, $manager);
        $secondTask = $this->createTask($anotherEmployee, $anotherEmployee);
        $tasks = $this->visibility->queryFor($manager)->get();
        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains('id', $firstTask->id));
        $this->assertTrue($tasks->contains('id', $secondTask->id));
    }

    public function test_active_participant_can_view_only_task_they_participate_in(): void
    {
        $employee = $this->createUserWithPermission('own');
        $anotherEmployee = User::factory()->create();
        $participatingTask = $this->createTask($anotherEmployee, $anotherEmployee);
        $unrelatedTask = $this->createTask($anotherEmployee, $anotherEmployee);
        TaskParticipant::query()->create([
            'task_id' => $participatingTask->id,
            'user_id' => $employee->id,
            'added_by' => $anotherEmployee->id,
            'added_at' => now(),
        ]);
        $this->assertTrue($this->visibility->canView($employee, $participatingTask));
        $this->assertFalse($this->visibility->canView($employee, $unrelatedTask));
    }

    public function test_removed_participant_cannot_view_task_through_membership(): void
    {
        $employee = $this->createUserWithPermission('own');
        $anotherEmployee = User::factory()->create();
        $task = $this->createTask($anotherEmployee, $anotherEmployee);
        TaskParticipant::query()->create([
            'task_id' => $task->id,
            'user_id' => $employee->id,
            'added_by' => $anotherEmployee->id,
            'added_at' => now(),
            'removed_by' => $anotherEmployee->id,
            'removed_at' => now(),
        ]);

        $this->assertFalse($this->visibility->canView($employee, $task));
    }

    public function test_user_without_permission_cannot_view_tasks(): void
    {
        $employee = User::factory()->create();
        $task = $this->createTask($employee, $employee);
        $this->assertFalse($this->visibility->canView($employee, $task));
        $this->assertCount(0, $this->visibility->queryFor($employee)->get());
    }

    public function test_inactive_user_cannot_view_tasks(): void
    {
        $employee = $this->createUserWithPermission('all');
        $employee->update(['status' => 'inactive']);
        $task = $this->createTask($employee, $employee);
        $this->assertFalse($this->visibility->canView($employee, $task));
        $this->assertCount(0, $this->visibility->queryFor($employee)->get());
    }

    private function createUserWithPermission(string $scope): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::query()->forceCreate([
            'name' => 'Tasks Test Role',
            'slug' => 'tasks-test-role-'.$user->id,
        ]);
        $permission = Permission::query()->firstOrCreate(
            ['slug' => 'tasks.view'],
            [
                'name' => 'View Tasks',
                'group' => 'tasks',
            ]
        );
        $role->permissions()->attach($permission->id, ['scope' => $scope]);
        $user->roles()->attach($role->id);

        return $user;
    }

    private function createTask(User $creator, User $assignee): Task
    {
        return Task::query()->forceCreate([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'title' => 'Test task',
            'status' => 'new',
            'priority' => 'normal',
        ]);
    }
}
