<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

test('renders the error page for a forbidden request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden()
        ->assertInertia(fn (Assert $page) => $page
            ->component('errors/error')
            ->where('status', 403)
        );
});

test('renders the error page for an unknown url', function () {
    $this->get('/cette-page-n-existe-pas')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('errors/error')
            ->where('status', 404)
        );
});

test('shares the authenticated user with the error page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/cette-page-n-existe-pas')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page
            ->component('errors/error')
            ->where('auth.user.id', $user->id)
        );
});

test('keeps the json response for a json request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson(route('admin.dashboard'))
        ->assertForbidden()
        ->assertJsonMissingPath('component');
});

test('renders the error page for a server error outside debug mode', function () {
    config(['app.debug' => false]);

    Route::get('/boom', fn () => abort(500));

    $this->get('/boom')
        ->assertStatus(500)
        ->assertInertia(fn (Assert $page) => $page
            ->component('errors/error')
            ->where('status', 500)
        );
});

test('keeps the default response for a server error in debug mode', function () {
    config(['app.debug' => true]);

    Route::get('/boom', fn () => abort(500));

    $this->get('/boom')
        ->assertStatus(500)
        ->assertDontSee('errors/error', false);
});
