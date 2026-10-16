<?php

use App\Actions\User\CreateUserAction;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

test('assigns the user role when no role is given', function () {
    $this->seed(RoleSeeder::class);

    $user = app(CreateUserAction::class)->handle([
        'name' => 'Jean Dupont',
        'email' => 'jean@example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);

    expect($user->getRoleNames()->all())->toBe(['user']);
});

test('does not create the user when the role assignment fails', function () {
    expect(fn () => app(CreateUserAction::class)->handle([
        'name' => 'Jean Dupont',
        'email' => 'jean@example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
        'roles' => ['admin'],
    ]))->toThrow(RoleDoesNotExist::class);

    $this->assertDatabaseCount('users', 0);
});
