<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::query()->select(['id', 'name', 'email', 'role', 'status', 'phone', 'position', 'department', 'created_at'])->orderBy('name')->paginate(15);
        return view('orvanta.employees.index', ['employees' => $employees]);
    }
}
