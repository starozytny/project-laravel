<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('does not share the admin access ability with guests', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.abilities.accessAdmin', false));
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
