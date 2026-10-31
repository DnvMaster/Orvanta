<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', function () {
        return view('orvanta.dashboard');
    })->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [EmployeeController::class, 'editProfile'])->name('edit');
        Route::put('/', [EmployeeController::class, 'updateProfile'])->name('update');
        Route::put('/password', [EmployeeController::class, 'updatePassword'])->name('password.update');
    });

    Route::prefix('employees')->name('employees.')->middleware('admin')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
    });

    Route::prefix('deals')->name('deals.')->group(function () {
        Route::get('/', [DealController::class, 'index'])->name('index');
        Route::get('/create', [DealController::class, 'create'])->name('create');
        Route::post('/', [DealController::class, 'store'])->name('store');
        Route::get('/{deal}/edit', [DealController::class, 'edit'])->name('edit');
        Route::put('/{deal}', [DealController::class, 'update'])->name('update');
        Route::get('/{deal}', [DealController::class, 'show'])->name('show');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});