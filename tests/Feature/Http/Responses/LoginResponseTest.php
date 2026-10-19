<?php

use App\Models\User;

test('redirects admins to the admin dashboard after login', function () {
    $admin = User::factory()->admin()->create();

    $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

test('redirects developers to the admin dashboard after login', function () {
    $developer = User::factory()->developer()->create();

    $this->post(route('login.store'), [
        'email' => $developer->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));
});

test('sends admins back to the page they asked for before login', function () {
    $admin = User::factory()->admin()->create();

    $this->get(route('user.dashboard'))->assertRedirect(route('login'));

    $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('user.dashboard'));
});

test('keeps the json response of the login', function () {
    $admin = User::factory()->admin()->create();

    $this->postJson(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertOk()->assertExactJson(['two_factor' => false]);
});
