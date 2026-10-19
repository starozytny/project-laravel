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
