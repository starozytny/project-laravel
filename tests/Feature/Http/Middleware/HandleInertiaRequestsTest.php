<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('shares the roles of the authenticated user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('user.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.roles', ['admin']));
});

test('shares an empty list of roles with guests', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.roles', []));
});

test('shares the admin access ability according to the role hierarchy', function (string $state, bool $canAccessAdmin) {
    $user = User::factory()->{$state}()->create();

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.abilities.accessAdmin', $canAccessAdmin));
})->with([
    'admin' => ['admin', true],
    'developer' => ['developer', true],
]);

test('does not share the admin access ability with the default role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.abilities.accessAdmin', false));
});
