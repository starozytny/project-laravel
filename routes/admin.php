<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:access-admin'])->group(function () {
    Route::inertia('admin', 'admin/dashboard')->name('admin.dashboard');

    Route::get('admin/utilisateurs', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('admin/utilisateurs/creer', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('admin/utilisateurs', [UserController::class, 'store'])->name('admin.users.store');
});
