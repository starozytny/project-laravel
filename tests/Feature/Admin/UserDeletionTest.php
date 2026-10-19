<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $this->seed(RoleSeeder::class);
});

test('redirects guests to the login page', function () {
    $target = User::factory()->create();

    $this->delete(route('admin.users.destroy', $target))->assertRedirect(route('login'));

    $this->assertModelExists($target);
});

test('forbids users with the default user role', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user)->delete(route('admin.users.destroy', $target))->assertForbidden();

    $this->assertModelExists($target);
});

test('returns not found for an unknown user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->delete(route('admin.users.destroy', 999999))->assertNotFound();
});

test('deletes a user and detaches their roles', function (string $state) {
    $actor = User::factory()->{$state}()->create();
    $target = User::factory()->admin()->create();

    $this->actingAs($actor)
        ->delete(route('admin.users.destroy', $target))
        ->assertRedirect(route('admin.users.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Utilisateur supprimé.']);

    $this->assertModelMissing($target);
    $this->assertDatabaseMissing('model_has_roles', [
        'model_id' => $target->id,
        'model_type' => User::class,
    ]);
    $this->assertModelExists($actor);
})->with(['admin', 'developer']);

test('forbids deleting oneself', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertForbidden();

    $this->assertModelExists($admin);
});
