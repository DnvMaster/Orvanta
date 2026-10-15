
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::get('/employees', [EmployeeController::class, 'index'])->middleware(['auth', 'active', 'admin'])->name('employees.index');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware(['auth', 'active'])->name('logout');

Route::get('/', function () {
    return view('orvanta.dashboard');
})->middleware(['auth', 'active'])->name('dashboard');
