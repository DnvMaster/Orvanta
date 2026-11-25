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

class TaskIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_view_permission_can_open_task_list(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $response = $this->actingAs($user)->get(route('tasks.index'));
        $response->assertOk()->assertViewIs('orvanta.tasks.index');
    }

    public function test_user_without_view_permission_cannot_open_task_list(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $response = $this->actingAs($user)->get(route('tasks.index'));
        $response->assertForbidden();
    }

    public function test_task_list_contains_only_accessible_tasks(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $anotherEmployee = User::factory()->create();
        $visibleTask = $this->createTask($user, $user);
        $hiddenTask = $this->createTask($anotherEmployee, $anotherEmployee);
        $response = $this->actingAs($user)->get(route('tasks.index'));
        $response->assertOk()->assertSee($visibleTask->title)->assertDontSee($hiddenTask->title);
    }

    public function test_my_filter_shows_only_tasks_assigned_to_user(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $anotherEmployee = User::factory()->create();
        $assignedTask = $this->createTask($anotherEmployee, $user);
        $createdTask = $this->createTask($user, $anotherEmployee);
        $response = $this->actingAs($user)->get(route('tasks.index', ['filter' => 'my']));
        $response->assertOk()->assertSee($assignedTask->title)->assertDontSee($createdTask->title);
    }

    public function test_created_filter_shows_only_tasks_created_by_user(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $anotherEmployee = User::factory()->create();
        $createdTask = $this->createTask($user, $anotherEmployee);
        $assignedTask = $this->createTask($anotherEmployee, $user);
        $response = $this->actingAs($user)->get(route('tasks.index', ['filter' => 'created']));
        $response->assertOk()->assertSee($createdTask->title)->assertDontSee($assignedTask->title);
    }

    public function test_participating_filter_shows_only_active_participation_tasks(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $anotherEmployee = User::factory()->create();
        $activeTask = $this->createTask($anotherEmployee, $anotherEmployee);
        $removedTask = $this->createTask($anotherEmployee, $anotherEmployee);
        TaskParticipant::query()->create([
            'task_id' => $activeTask->id,
            'user_id' => $user->id,
            'added_by' => $anotherEmployee->id,
            'added_at' => now(),
        ]);
        TaskParticipant::query()->create([
            'task_id' => $removedTask->id,
            'user_id' => $user->id,
            'added_by' => $anotherEmployee->id,
            'added_at' => now()->subDay(),
            'removed_by' => $anotherEmployee->id,
            'removed_at' => now(),
        ]);
        $response = $this->actingAs($user)->get(route('tasks.index', ['filter' => 'participating']));
        $response->assertOk()->assertSee($activeTask->title)->assertDontSee($removedTask->title);
    }

    public function test_review_filter_shows_only_assigned_tasks_under_review(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $anotherEmployee = User::factory()->create();
        $reviewTask = $this->createTask($anotherEmployee, $user, 'review');
        $newTask = $this->createTask($anotherEmployee, $user, 'new');
        $anotherReviewTask = $this->createTask($anotherEmployee, $anotherEmployee, 'review');
        $response = $this->actingAs($user)->get(route('tasks.index', ['filter' => 'review']));
        $response->assertOk()->assertSee($reviewTask->title)->assertDontSee($newTask->title)->assertDontSee($anotherReviewTask->title);
    }

    public function test_all_filter_shows_all_tasks_within_user_scope(): void
    {
        $user = $this->createUserWithPermission('tasks.view');
        $anotherEmployee = User::factory()->create();
        $ownTask = $this->createTask($user, $user);
        $createdTask = $this->createTask($user, $anotherEmployee);
        $assignedTask = $this->createTask($anotherEmployee, $user);
        $hiddenTask = $this->createTask($anotherEmployee, $anotherEmployee);
        $response = $this->actingAs($user)->get(route('tasks.index', ['filter' => 'all']));
        $response->assertOk()->assertSee($ownTask->title)->assertSee($createdTask->title)->assertSee($assignedTask->title)->assertDontSee($hiddenTask->title);
    }

    private function createUserWithPermission(string $permissionSlug): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::query()->forceCreate([
            'name' => 'Tasks Index Test Role',
            'slug' => 'tasks-index-test-'.$user->id,
        ]);
        $permission = Permission::query()->where('slug', $permissionSlug)->firstOrFail();
        $role->permissions()->attach($permission->id, ['scope' => 'own']);
        $user->roles()->attach($role->id);

        return $user;
    }

    private function createTask(User $creator, User $assignee, string $status = 'new'): Task
    {
        return Task::query()->forceCreate([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'title' => 'Task index test '.$creator->id.'-'.$assignee->id.'-'.uniqid(),
            'status' => $status,
            'priority' => 'normal',
        ]);
    }
}
