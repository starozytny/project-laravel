<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('espace-membre', 'user/dashboard')->name('user.dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
