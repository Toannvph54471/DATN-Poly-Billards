<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// Route cho Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('users/index', function () {
        return view('admin.users.index');
    })->name('admin.users.index'); 

    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');
});

// Route cho Employee
Route::prefix('employee')->middleware(['auth', 'employee'])->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.dashboard');
    })->name('employee.dashboard');
});