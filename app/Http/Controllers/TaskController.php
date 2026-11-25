<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskVisibilityService;
use Illuminate\Contracts\View\View;
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
            'filter' => ['sometimes', 'string', Rule::in([
                'my', 'created', 'participating', 'review', 'all',
            ])],
        ]);
        $user = $request->user();
        $activeFilter = $validated['filter'] ?? 'all';
        $tasks = $this->visibility->queryForList($user, $activeFilter)->with(['creator', 'assignee'])->latest()->paginate(25)->withQueryString();

        return view('orvanta.tasks.index', compact('tasks', 'activeFilter'));
    }
}
