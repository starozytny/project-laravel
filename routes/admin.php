<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:access-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::inertia('/', 'admin/dashboard')->name('dashboard');

        Route::prefix('utilisateurs')
            ->name('users.')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('creer', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('{user}/modifier', 'edit')->name('edit');
                Route::put('{user}', 'update')->name('update');
                Route::delete('{user}', 'destroy')->name('destroy');
            });

        Route::prefix('messages')
            ->name('contact-messages.')
            ->controller(ContactMessageController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('{contactMessage}', 'show')->name('show');
                Route::delete('{contactMessage}', 'destroy')->name('destroy');
            });
    });
