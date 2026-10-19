<?php

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('gives the user role by default', function () {
    $user = User::factory()->create();

    expect($user->getRoleNames()->all())->toBe(['user']);
});

test('replaces the default role with the one of the state', function (string $state, string $role) {
    $user = User::factory()->{$state}()->create();

    expect($user->getRoleNames()->all())->toBe([$role]);
})->with([
    'admin' => ['admin', 'admin'],
    'developer' => ['developer', 'developer'],
]);

test('replaces the default role with the given role', function (UserRole $role) {
    $user = User::factory()->withRole($role)->create();

    expect($user->getRoleNames()->all())->toBe([$role->value]);
})->with(UserRole::cases());
