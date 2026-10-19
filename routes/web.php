<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('espace-membre', 'user/dashboard')->name('user.dashboard');
});

Route::middleware(['auth', 'verified', 'can:access-admin'])->group(function () {
    Route::inertia('admin', 'admin/dashboard')->name('admin.dashboard');
});

require __DIR__.'/settings.php';
