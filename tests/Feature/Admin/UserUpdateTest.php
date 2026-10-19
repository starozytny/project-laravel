<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
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
function validUpdateInput(array $overrides = []): array
{
    return [
        'name' => 'Nouveau Nom',
        'email' => 'nouveau@example.com',
        ...$overrides,
    ];
}

test('redirects guests to the login page', function () {
    $target = User::factory()->create();

    $this->get(route('admin.users.edit', $target))->assertRedirect(route('login'));
    $this->put(route('admin.users.update', $target), validUpdateInput())->assertRedirect(route('login'));

    expect($target->fresh()->name)->toBe($target->name);
});

test('forbids users with the default user role', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user)->get(route('admin.users.edit', $target))->assertForbidden();
    $this->actingAs($user)->put(route('admin.users.update', $target), validUpdateInput())->assertForbidden();

    expect($target->fresh()->name)->toBe($target->name);
});

test('returns not found for an unknown user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.users.edit', 999999))->assertNotFound();
    $this->actingAs($admin)->put(route('admin.users.update', 999999), validUpdateInput())->assertNotFound();
});

test('renders the edit form with the user and the role options', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->developer()->create(['name' => 'Cible', 'email' => 'cible@example.com']);

    $this->actingAs($admin)
        ->get(route('admin.users.edit', $target))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/edit')
            ->where('user.id', $target->id)
            ->where('user.name', 'Cible')
            ->where('user.email', 'cible@example.com')
            ->where('user.roles', [['value' => 'developer', 'label' => 'Développeur']])
            ->where('isSelf', false)
            ->has('roleOptions', 3)
            ->missing('user.password')
        );
});

test('flags the edit form as editing oneself', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.edit', $admin))
        ->assertInertia(fn (Assert $page) => $page->where('isSelf', true));
});

test('updates the name and the email of a user', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $target), validUpdateInput())
        ->assertRedirect(route('admin.users.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Utilisateur modifié.']);

    $target->refresh();

    expect($target->name)->toBe('Nouveau Nom')
        ->and($target->email)->toBe('nouveau@example.com');
});

test('replaces the roles of a user', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $target), validUpdateInput(['roles' => ['admin', 'developer']]))
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh()->getRoleNames()->sort()->values()->all())->toBe(['admin', 'developer']);
});

test('keeps the roles of a user when none are given', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->developer()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $target), validUpdateInput())
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh()->getRoleNames()->all())->toBe(['developer']);
});

test('accepts the current email address of the edited user', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create(['email' => 'cible@example.com']);

    $this->actingAs($admin)
        ->put(route('admin.users.update', $target), validUpdateInput(['email' => 'cible@example.com']))
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh()->name)->toBe('Nouveau Nom');
});

test('ignores the roles when an administrator edits oneself', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $admin), validUpdateInput(['roles' => ['user']]))
        ->assertRedirect(route('admin.users.index'));

    $admin->refresh();

    expect($admin->name)->toBe('Nouveau Nom')
        ->and($admin->getRoleNames()->all())->toBe(['admin']);
});

test('rejects invalid input without changing the user', function (array $overrides, string $errorKey) {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create(['name' => 'Cible', 'email' => 'cible@example.com']);
    User::factory()->create(['email' => 'pris@example.com']);

    $this->actingAs($admin)
        ->from(route('admin.users.edit', $target))
        ->put(route('admin.users.update', $target), validUpdateInput($overrides))
        ->assertRedirect(route('admin.users.edit', $target))
        ->assertSessionHasErrors($errorKey);

    $target->refresh();

    expect($target->name)->toBe('Cible')
        ->and($target->email)->toBe('cible@example.com')
        ->and($target->getRoleNames()->all())->toBe(['user']);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'invalid email' => [['email' => 'test'], 'email'],
    'email used by another user' => [['email' => 'pris@example.com'], 'email'],
    'unknown role' => [['roles' => ['superman']], 'roles.0'],
    'empty roles' => [['roles' => []], 'roles'],
]);
