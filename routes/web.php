<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('espace-membre', 'user/dashboard')->name('user.dashboard');
});

Route::middleware(['auth', 'verified', 'role:'.UserRole::Admin->value])->group(function () {
    Route::inertia('admin', 'admin/dashboard')->name('admin.dashboard');
});

require __DIR__.'/settings.php';
