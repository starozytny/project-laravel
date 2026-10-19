<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('espace-membre', 'user/dashboard')->name('user.dashboard');
});

Route::inertia('mentions-legales', 'legal/notice')->name('legal.notice');
Route::inertia('politique-de-confidentialite', 'legal/privacy')->name('legal.privacy');

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
