<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('redirects guests to the login page', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('forbids users with the default user role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('forbids developers', function () {
    $developer = User::factory()->developer()->create();

    $this->actingAs($developer)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('renders the admin dashboard for admins', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/dashboard'));
});
