<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->seed(RoleSeeder::class);
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function validUserInput(array $overrides = []): array
{
    return [
        'name' => 'Jean Dupont',
        'email' => 'jean@example.com',
        'password' => 'secret-password',
        ...$overrides,
    ];
}

test('redirects guests to the login page', function () {
    $this->get(route('admin.users.create'))->assertRedirect(route('login'));
    $this->post(route('admin.users.store'), validUserInput())->assertRedirect(route('login'));

    $this->assertDatabaseMissing('users', ['email' => 'jean@example.com']);
});

test('forbids users with the default user role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.create'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.users.store'), validUserInput())->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'jean@example.com']);
});

test('renders the creation form with the role options', function (string $state) {
    $user = User::factory()->{$state}()->create();

    $this->actingAs($user)
        ->get(route('admin.users.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/create')
            ->where('roleOptions', [
                ['value' => 'developer', 'label' => 'Développeur'],
                ['value' => 'admin', 'label' => 'Administrateur'],
                ['value' => 'user', 'label' => 'Utilisateur'],
            ])
        );
})->with(['admin', 'developer']);

test('creates a user with the default role and a hashed password', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.store'), validUserInput())
        ->assertRedirect(route('admin.users.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Utilisateur créé.']);

    $user = User::query()->where('email', 'jean@example.com')->firstOrFail();

    expect($user->name)->toBe('Jean Dupont')
        ->and($user->password)->not->toBe('secret-password')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue()
        ->and($user->getRoleNames()->all())->toBe(['user']);
});

test('creates a user with the given roles', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.store'), validUserInput(['roles' => ['admin', 'developer']]))
        ->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'jean@example.com')->firstOrFail();

    expect($user->getRoleNames()->sort()->values()->all())->toBe(['admin', 'developer']);
});

test('rejects invalid input without creating a user', function (array $overrides, string $errorKey) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), validUserInput($overrides))
        ->assertRedirect(route('admin.users.create'))
        ->assertSessionHasErrors($errorKey);

    $this->assertDatabaseCount('users', 1);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'invalid email' => [['email' => 'test'], 'email'],
    'weak password' => [['password' => 'short'], 'password'],
    'unknown role' => [['roles' => ['superman']], 'roles.0'],
    'empty roles' => [['roles' => []], 'roles'],
]);

test('rejects an email address already used', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['email' => 'jean@example.com']);

    $this->actingAs($admin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), validUserInput())
        ->assertSessionHasErrors('email');

    $this->assertDatabaseCount('users', 2);
});
