<?php

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('does not share the admin access ability with guests', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.abilities.accessAdmin', false));
});

test('shares the admin access ability according to the role hierarchy', function (string $state, bool $canAccessAdmin) {
    $user = User::factory()->{$state}()->create();

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.abilities.accessAdmin', $canAccessAdmin));
})->with([
    'admin' => ['admin', true],
    'developer' => ['developer', true],
]);

test('does not share the admin access ability with the default role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('auth.abilities.accessAdmin', false));
});

test('shares the number of unread contact messages with administrators', function (string $state) {
    $user = User::factory()->{$state}()->create();
    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->read()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('unreadContactMessages', 2));
})->with(['admin', 'developer']);

test('shares no unread contact messages with guests', function () {
    ContactMessage::factory()->create();

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->where('unreadContactMessages', 0));
});

test('does not query the contact messages for users without admin access', function () {
    $user = User::factory()->create();
    ContactMessage::factory()->create();

    DB::enableQueryLog();

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('unreadContactMessages', 0));

    $contactMessageQueries = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains($query['query'], 'contact_messages'));

    expect($contactMessageQueries)->toBeEmpty();
});
