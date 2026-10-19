<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app()->setLocale('fr');
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->seed(RoleSeeder::class);
});

test('creates a user with a hashed password and the given roles', function () {
    $this->artisan('user:create', [
        '--name' => 'Jean Dupont',
        '--email' => 'jean@example.com',
        '--role' => ['admin', 'developer'],
    ])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->expectsOutputToContain("L'utilisateur Jean Dupont a été créé avec succès.")
        ->assertSuccessful();

    $user = User::query()->where('email', 'jean@example.com')->firstOrFail();

    expect($user->name)->toBe('Jean Dupont')
        ->and($user->password)->not->toBe('secret-password')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue()
        ->and($user->getRoleNames()->sort()->values()->all())->toBe(['admin', 'developer']);
});

test('asks for the roles when none are given', function () {
    $this->artisan('user:create', ['--name' => 'Jean Dupont', '--email' => 'jean@example.com'])
        ->expectsChoice('Rôles', ['admin'], [
            'developer' => 'Développeur',
            'admin' => 'Administrateur',
            'user' => 'Utilisateur',
        ])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->assertSuccessful();

    $user = User::query()->where('email', 'jean@example.com')->firstOrFail();

    expect($user->getRoleNames()->all())->toBe(['admin']);
});

test('rejects an invalid email address without creating a user', function () {
    $this->artisan('user:create', ['--name' => 'Jean Dupont', '--email' => 'test', '--role' => ['user']])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->expectsOutputToContain('Le champ email doit être une adresse e-mail valide.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});

test('rejects an email address that is already used without creating a second user', function () {
    User::factory()->create(['email' => 'jean@example.com']);

    $this->artisan('user:create', ['--name' => 'Autre Jean', '--email' => 'jean@example.com', '--role' => ['user']])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->expectsOutputToContain('La valeur du champ email est déjà utilisée.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 1);
});

test('rejects an unknown role without creating a user', function () {
    $this->artisan('user:create', ['--name' => 'Jean Dupont', '--email' => 'jean@example.com', '--role' => ['superman']])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});
