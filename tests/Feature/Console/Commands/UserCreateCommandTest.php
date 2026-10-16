<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    app()->setLocale('fr');
});

test('creates a user with a hashed password', function () {
    $this->artisan('user:create', ['--name' => 'Jean Dupont', '--email' => 'jean@example.com'])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->expectsQuestion('Confirmer le mot de passe', 'secret-password')
        ->expectsOutputToContain("L'utilisateur Jean Dupont a été créé avec succès.")
        ->assertSuccessful();

    $user = User::query()->where('email', 'jean@example.com')->firstOrFail();

    expect($user->name)->toBe('Jean Dupont')
        ->and($user->password)->not->toBe('secret-password')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});

test('rejects an invalid email address without creating a user', function () {
    $this->artisan('user:create', ['--name' => 'Jean Dupont', '--email' => 'test'])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->expectsQuestion('Confirmer le mot de passe', 'secret-password')
        ->expectsOutputToContain('Le champ email doit être une adresse e-mail valide.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});

test('rejects an email address that is already used without creating a second user', function () {
    User::factory()->create(['email' => 'jean@example.com']);

    $this->artisan('user:create', ['--name' => 'Autre Jean', '--email' => 'jean@example.com'])
        ->expectsOutputToContain('Cette adresse e-mail est déjà utilisée.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 1);
});

test('rejects a password confirmation that does not match without creating a user', function () {
    $this->artisan('user:create', ['--name' => 'Jean Dupont', '--email' => 'jean@example.com'])
        ->expectsQuestion('Mot de passe', 'secret-password')
        ->expectsQuestion('Confirmer le mot de passe', 'another-password')
        ->expectsOutputToContain('Le champ de confirmation password ne correspond pas.')
        ->assertFailed();

    $this->assertDatabaseCount('users', 0);
});
