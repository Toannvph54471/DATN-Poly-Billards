<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('admin/dashboard', function () {
    return view('dashboard');
});
Route::prefix('admin')->group(function () {
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');


    // Table
});
