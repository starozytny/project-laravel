<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('renders the legal pages for guests', function (string $route, string $component) {
    $this->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    'legal notice' => ['legal.notice', 'legal/notice'],
    'privacy policy' => ['legal.privacy', 'legal/privacy'],
]);

test('renders the legal pages for authenticated users', function (string $route, string $component) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    'legal notice' => ['legal.notice', 'legal/notice'],
    'privacy policy' => ['legal.privacy', 'legal/privacy'],
]);
