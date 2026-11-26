<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskVisibilityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct(private readonly TaskVisibilityService $visibility)
    {
        //
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Task::class);
        $validated = $request->validate([
            'filter' => ['sometimes', 'string', Rule::in(['my', 'created', 'participating', 'review', 'all'])],
        ]);
        $user = $request->user();
        $activeFilter = $validated['filter'] ?? 'all';
        $tasks = $this->visibility->queryForList($user, $activeFilter)->with(['creator', 'assignee'])->latest()->paginate(25)->withQueryString();

        return view('orvanta.tasks.index', compact('tasks', 'activeFilter'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Task::class);
        $employees = $this->availableAssignees($request->user());

        return view('orvanta.tasks.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Task::class);
        $validated = $request->validate($this->taskRules());
        $user = $request->user();
        $assignee = $this->resolveAssignee($user, (int) $validated['assigned_to']);
        $task = new Task;
        $task->fill($this->taskAttributes($validated));
        $task->forceFill([
            'created_by' => $user->id,
            'assigned_to' => $assignee->id,
            'assigned_department_id' => $assignee->department_id,
            'status' => 'new',
        ]);
        $task->save();

        return redirect()->route('tasks.index')->with('success', __('Задача успешно создана.'));
    }

    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);
        $employees = $this->availableAssignees(request()->user(), $task);

        return view('orvanta.tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);
        $rules = $this->taskRules();
        unset($rules['assigned_to']);
        $rules['assigned_to'] = ['sometimes', 'required', 'integer', Rule::exists('users', 'id')->where('status', 'active')];
        $validated = $request->validate($rules);
        $task->fill($this->taskAttributes($validated));
        if (array_key_exists('assigned_to', $validated)) {
            $assigneeId = (int) $validated['assigned_to'];
            if ($assigneeId !== $task->assigned_to) {
                $assignee = $this->resolveAssignee($request->user(), $assigneeId);
                $task->forceFill([
                    'assigned_to' => $assignee->id,
                    'assigned_department_id' => $assignee->department_id,
                ]);
            }
        }
        $task->save();

        return redirect()->route('tasks.index')->with('success', __('Задача успешно обновлена.'));
    }

    private function taskRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'priority' => ['required', 'string', Rule::in([
                'low', 'normal', 'high', 'urgent',
            ])],
            'starts_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'assigned_to' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('status', 'active'),
            ],
        ];
    }

    private function taskAttributes(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'starts_at' => $validated['starts_at'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
        ];
    }

    private function resolveAssignee(User $user, int $assigneeId): User
    {
        if ($assigneeId !== $user->id && ! $user->hasPermissionTo('tasks.assign')) {
            abort(403);
        }

        return User::query()->where('status', 'active')->findOrFail($assigneeId);
    }

    private function availableAssignees(User $user)
    {
        $query = User::query()->where('status', 'active')->orderBy('name');
        if (! $user->hasPermissionTo('tasks.assign')) {
            $query->whereKey($user->id);
        }

        return $query->get(['id', 'name', 'department_id']);
    }
}
