<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $rootDepartments = Department::whereNull('parent_id')->with(['descendants' => function ($query) {
            $query->withCount('users')->orderBy('name');
        }])->withCount('users')->orderBy('name')->get();

        return view('orvanta.departments.index', compact('rootDepartments'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('orvanta.departments._form', [
            'department' => new Department,
            'departments' => $departments,
        ]);
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return redirect()->route('departments.index')->with('success', 'Подразделение успешно создано.');
    }

    public function edit(Department $department): View
    {
        $excludedIds = array_merge([$department->id], $department->descendantIds());
        $departments = Department::whereNotIn('id', $excludedIds)->orderBy('name')->get();

        return view('orvanta.departments._form', compact('department', 'departments'));
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('departments.index')->with('success', 'Подразделение обновлено.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->children()->exists()) {
            return back()->with('error', 'Нельзя удалить подразделение, у которого есть дочерние отделы.');
        }

        if ($department->users()->exists()) {
            return back()->with('error', 'Нельзя удалить подразделение, в котором числятся сотрудники.');
        }
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Подразделение удалено.');
    }
}
