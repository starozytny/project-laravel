<?php

use App\Models\User;

test('redirects authenticated admins away from the login page to the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('login'))
        ->assertRedirect(route('admin.dashboard'));
});

test('redirects authenticated users away from the login page to the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});
