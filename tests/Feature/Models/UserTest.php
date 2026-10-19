<?php

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('sends admins to the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->homeUrl())->toBe(route('admin.dashboard'));
});

test('sends users with the default role to the dashboard', function () {
    $user = User::factory()->create();

    expect($user->homeUrl())->toBe(route('user.dashboard'));
});

test('sends developers to the admin dashboard', function () {
    $developer = User::factory()->developer()->create();

    expect($developer->homeUrl())->toBe(route('admin.dashboard'));
});

test('gives the admin dashboard priority when the user has several roles', function () {
    $user = User::factory()->developer()->create();
    $user->assignRole(Role::findOrCreate(UserRole::Admin->value));

    expect($user->homeUrl())->toBe(route('admin.dashboard'));
});

test('grants a role to the roles above it', function () {
    $developer = User::factory()->developer()->create();

    expect($developer->hasRoleOrAbove(UserRole::Admin))->toBeTrue()
        ->and($developer->hasRoleOrAbove(UserRole::User))->toBeTrue();
});

test('does not grant a role to the roles below it', function () {
    $user = User::factory()->create();

    expect($user->hasRoleOrAbove(UserRole::Admin))->toBeFalse();
});
