<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DealController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware(['auth', 'active', 'admin'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [EmployeeController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [EmployeeController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [EmployeeController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('admin')->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->middleware('admin')->name('employees.update');
});


Route::get('/deals', [DealController::class, 'index'])->middleware(['auth', 'active'])->name('deals.index');
Route::get('/deals/create', [DealController::class, 'create'])->middleware(['auth', 'active'])->name('deals.create');
Route::get('/deals/{deal}/edit', [DealController::class, 'edit'])->middleware(['auth', 'active'])->name('deals.edit');
Route::put('/deals/{deal}', [DealController::class, 'update'])->middleware(['auth', 'active'])->name('deals.update');
Route::get('/deals/{deal}', [DealController::class, 'show'])->middleware(['auth', 'active'])->name('deals.show');
Route::post('/deals', [DealController::class, 'store'])->middleware(['auth', 'active'])->name('deals.store');


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware(['auth', 'active'])->name('logout');

Route::get('/', function () {
    return view('orvanta.dashboard');
})->middleware(['auth', 'active'])->name('dashboard');
