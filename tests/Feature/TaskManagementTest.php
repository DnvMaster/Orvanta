<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_personal_task_with_create_permission(): void
    {
        $user = $this->createUserWithPermissions('tasks.create');
        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Личная задача',
            'description' => 'Описание задачи',
            'priority' => 'high',
            'assigned_to' => $user->id,
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Личная задача',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'new',
        ]);
    }

    public function test_user_cannot_assign_task_to_another_employee_without_assign_permission(): void
    {
        $user = $this->createUserWithPermissions('tasks.create');
        $anotherEmployee = User::factory()->create(['status' => 'active']);
        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Чужая задача',
            'priority' => 'normal',
            'assigned_to' => $anotherEmployee->id,
        ]);
        $response->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Чужая задача',
        ]);
    }

    public function test_user_with_assign_permission_can_assign_task_to_another_employee(): void
    {
        $user = $this->createUserWithPermissions('tasks.create', 'tasks.assign');
        $anotherEmployee = User::factory()->create(['status' => 'active']);
        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Задача сотруднику',
            'priority' => 'normal',
            'assigned_to' => $anotherEmployee->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Задача сотруднику',
            'created_by' => $user->id,
            'assigned_to' => $anotherEmployee->id,
            'assigned_department_id' => $anotherEmployee->department_id,
            'status' => 'new',
        ]);
    }

    public function test_create_ignores_spoofed_system_fields(): void
    {
        $user = $this->createUserWithPermissions('tasks.create');
        $anotherEmployee = User::factory()->create(['status' => 'active']);
        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Защищённая задача',
            'priority' => 'normal',
            'assigned_to' => $user->id,
            'created_by' => $anotherEmployee->id,
            'status' => 'completed',
            'assigned_department_id' => 999,
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Защищённая задача',
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'new',
            'assigned_department_id' => $user->department_id,
        ]);
    }

    public function test_inactive_employee_cannot_be_assigned_to_new_task(): void
    {
        $user = $this->createUserWithPermissions('tasks.create');
        $inactiveEmployee = User::factory()->create(['status' => 'blocked']);
        $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
            'title' => 'Задача неактивному сотруднику',
            'priority' => 'normal',
            'assigned_to' => $inactiveEmployee->id,
        ]);
        $response->assertSessionHasErrors('assigned_to');
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Задача неактивному сотруднику',
        ]);
    }

    public function test_user_can_update_task_without_assign_permission_when_assignee_is_unchanged(): void
    {
        $user = $this->createUserWithPermissions('tasks.update');
        $task = $this->createTask($user, $user);
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Изменённое название',
            'description' => 'Новое описание',
            'priority' => 'high',
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Изменённое название',
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ]);
    }

    public function test_user_cannot_change_assignee_without_assign_permission(): void
    {
        $user = $this->createUserWithPermissions('tasks.update');
        $anotherEmployee = User::factory()->create(['status' => 'active']);
        $task = $this->createTask($user, $user);
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Попытка переназначения',
            'priority' => 'normal',
            'assigned_to' => $anotherEmployee->id,
        ]);
        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'assigned_to' => $user->id,
        ]);
    }

    public function test_user_with_assign_permission_can_change_assignee(): void
    {
        $user = $this->createUserWithPermissions('tasks.update', 'tasks.assign');
        $anotherEmployee = User::factory()->create(['status' => 'active']);
        $task = $this->createTask($user, $user);
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => $task->title,
            'priority' => 'normal',
            'assigned_to' => $anotherEmployee->id,
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'assigned_to' => $anotherEmployee->id,
            'assigned_department_id' => $anotherEmployee->department_id,
        ]);
    }

    public function test_user_without_update_permission_cannot_edit_task(): void
    {
        $user = $this->createUserWithPermissions('tasks.view');
        $task = $this->createTask($user, $user);
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Недопустимое изменение',
            'priority' => 'normal',
        ]);
        $response->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Недопустимое изменение',
        ]);
    }

    public function test_task_under_review_cannot_be_edited(): void
    {
        $user = $this->createUserWithPermissions('tasks.update');
        $task = $this->createTask($user, $user, 'review');
        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Изменение задачи на проверке',
            'priority' => 'normal',
        ]);
        $response->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Изменение задачи на проверке',
        ]);
    }

    public function test_due_date_cannot_be_earlier_than_start_date(): void
    {
        $user = $this->createUserWithPermissions('tasks.create');
        $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
            'title' => 'Задача с неверным сроком',
            'priority' => 'normal',
            'assigned_to' => $user->id,
            'starts_at' => '2026-11-27T12:00',
            'due_at' => '2026-11-26T12:00',
        ]);
        $response->assertSessionHasErrors('due_at');
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Задача с неверным сроком',
        ]);
    }

    private function createUserWithPermissions(string ...$permissionSlugs): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::query()->forceCreate([
            'name' => 'Task Management Test Role',
            'slug' => 'task-management-test-'.$user->id,
        ]);
        foreach ($permissionSlugs as $permissionSlug) {
            $permission = Permission::query()->where('slug', $permissionSlug)->firstOrFail();
            $role->permissions()->attach($permission->id, ['scope' => 'own']);
        }
        $user->roles()->attach($role->id);

        return $user;
    }

    private function createTask(User $creator, User $assignee, string $status = 'new'): Task
    {
        return Task::query()->forceCreate([
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
            'assigned_department_id' => $assignee->department_id,
            'title' => 'Task management test '.uniqid(),
            'status' => $status,
            'priority' => 'normal',
        ]);
    }
}
