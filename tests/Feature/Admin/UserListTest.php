<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('redirects guests to the login page', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});

test('forbids users with the default user role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('renders the users list for roles with admin access', function (UserRole $role) {
    $user = User::factory()->withRole($role)->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/users/index'));
})->with([
    'admin' => UserRole::Admin,
    'developer' => UserRole::Developer,
]);

test('lists users ordered by name with their role labels', function () {
    $admin = User::factory()->admin()->create(['name' => 'Zoe', 'email' => 'zoe@example.com']);
    User::factory()->create(['name' => 'Charlie', 'email' => 'charlie@example.com']);
    User::factory()->developer()->create(['name' => 'Alice', 'email' => 'alice@example.com']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 3)
            ->where('users.data.0.name', 'Alice')
            ->where('users.data.0.email', 'alice@example.com')
            ->where('users.data.0.roles', [['value' => 'developer', 'label' => 'Développeur']])
            ->where('users.data.1.name', 'Charlie')
            ->where('users.data.1.roles', [['value' => 'user', 'label' => 'Utilisateur']])
            ->where('users.data.2.name', 'Zoe')
            ->where('users.data.2.roles', [['value' => 'admin', 'label' => 'Administrateur']])
        );
});

test('lists every role of a user holding several roles', function () {
    $admin = User::factory()->admin()->create(['name' => 'Zoe']);
    User::factory()->create(['name' => 'Alice'])->assignRole(UserRole::Admin->value);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('users.data.0.name', 'Alice')
            ->has('users.data.0.roles', 2)
        );
});

test('does not expose sensitive attributes', function () {
    $admin = User::factory()->admin()->withTwoFactor()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->missing('users.data.0.password')
            ->missing('users.data.0.remember_token')
            ->missing('users.data.0.two_factor_secret')
            ->missing('users.data.0.two_factor_recovery_codes')
        );
});

test('paginates users by pages of 20', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(24)->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 20)
            ->where('users.meta.total', 25)
        );

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['page' => 2]))
        ->assertInertia(fn (Assert $page) => $page->has('users.data', 5));
});

test('loads roles with a constant number of queries', function () {
    $admin = User::factory()->admin()->create();

    $countQueries = function () use ($admin): int {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();

        return count(DB::getQueryLog());
    };

    // The first request also warms the permission cache of the acting user.
    $countQueries();
    $queriesWithOneUser = $countQueries();

    User::factory()->count(10)->create();

    expect($countQueries())->toBe($queriesWithOneUser);
});

test('filters users by name or email regardless of case', function (string $search) {
    $admin = User::factory()->admin()->create(['name' => 'Zoe', 'email' => 'zoe@example.com']);
    User::factory()->create(['name' => 'Jean Dupont', 'email' => 'jd@example.com']);
    User::factory()->create(['name' => 'Alice', 'email' => 'jean.alice@example.com']);
    User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => $search]))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 2)
            ->where('users.data.0.name', 'Alice')
            ->where('users.data.1.name', 'Jean Dupont')
            ->where('filters.search', trim($search))
        );
})->with([
    'lowercase' => 'jean',
    'uppercase' => 'JEAN',
    'surrounded by spaces' => '  jean  ',
]);

test('returns an empty list when nothing matches the search', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => 'introuvable']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 0)
            ->where('users.meta.total', 0)
            ->where('filters.search', 'introuvable')
        );
});

test('returns an empty search filter and every user when no search is given', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(2)->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 3)
            ->where('filters.search', '')
        );
});

test('keeps the search in the pagination links', function () {
    $admin = User::factory()->admin()->create(['name' => 'Zoe']);
    User::factory()->count(25)->create(['name' => 'Match']);

    $this->actingAs($admin)
        ->get(route('admin.users.index', ['search' => 'match']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 20)
            ->where('users.meta.total', 25)
            ->where('users.links.next', route('admin.users.index', ['search' => 'match', 'page' => 2]))
        );
});
