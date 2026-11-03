<?php

use App\Models\ContactMessage;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('redirects guests to the login page', function () {
    $contactMessage = ContactMessage::factory()->create();

    $this->get(route('admin.contact-messages.index'))->assertRedirect(route('login'));
    $this->get(route('admin.contact-messages.show', $contactMessage))->assertRedirect(route('login'));
    $this->delete(route('admin.contact-messages.destroy', $contactMessage))->assertRedirect(route('login'));

    expect($contactMessage->fresh()->isRead())->toBeFalse();
    $this->assertModelExists($contactMessage);
});

test('forbids users with the default user role', function () {
    $user = User::factory()->create();
    $contactMessage = ContactMessage::factory()->create();

    $this->actingAs($user)->get(route('admin.contact-messages.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.contact-messages.show', $contactMessage))->assertForbidden();
    $this->actingAs($user)->delete(route('admin.contact-messages.destroy', $contactMessage))->assertForbidden();

    expect($contactMessage->fresh()->isRead())->toBeFalse();
    $this->assertModelExists($contactMessage);
});

test('returns not found for an unknown message', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.contact-messages.show', 999999))->assertNotFound();
    $this->actingAs($admin)->delete(route('admin.contact-messages.destroy', 999999))->assertNotFound();
});

test('lists the messages for roles with admin access', function (string $state) {
    $user = User::factory()->{$state}()->create();
    ContactMessage::factory()->create([
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'company' => 'Acme',
        'email' => 'jean@example.com',
        'phone' => '06 12 34 56 78',
        'message' => 'Bonjour',
    ]);

    $this->actingAs($user)
        ->get(route('admin.contact-messages.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/contact-messages/index')
            ->has('contactMessages.data', 1)
            ->where('contactMessages.data.0.firstName', 'Jean')
            ->where('contactMessages.data.0.lastName', 'Dupont')
            ->where('contactMessages.data.0.company', 'Acme')
            ->where('contactMessages.data.0.email', 'jean@example.com')
            ->where('contactMessages.data.0.phone', '06 12 34 56 78')
            ->where('contactMessages.data.0.message', 'Bonjour')
            ->where('contactMessages.data.0.isRead', false)
        );
})->with(['admin', 'developer']);

test('lists the unread messages first, then the most recent ones', function () {
    $admin = User::factory()->admin()->create();
    ContactMessage::factory()->read()->create(['first_name' => 'Lu', 'created_at' => now()]);
    ContactMessage::factory()->create(['first_name' => 'NonLuAncien', 'created_at' => now()->subDays(3)]);
    ContactMessage::factory()->create(['first_name' => 'NonLuRecent', 'created_at' => now()->subDay()]);

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('contactMessages.data.0.firstName', 'NonLuRecent')
            ->where('contactMessages.data.1.firstName', 'NonLuAncien')
            ->where('contactMessages.data.2.firstName', 'Lu')
            ->where('contactMessages.data.2.isRead', true)
        );
});

test('paginates the messages by pages of 20', function () {
    $admin = User::factory()->admin()->create();
    ContactMessage::factory()->count(25)->create();

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('contactMessages.data', 20)
            ->where('contactMessages.meta.total', 25)
        );

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.index', ['page' => 2]))
        ->assertInertia(fn (Assert $page) => $page->has('contactMessages.data', 5));
});

test('lists a message without company and email address', function () {
    $admin = User::factory()->admin()->create();
    ContactMessage::factory()->withoutCompany()->withoutEmail()->create();

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('contactMessages.data.0.company', null)
            ->where('contactMessages.data.0.email', null)
        );
});

test('shows a message and marks it as read', function () {
    $admin = User::factory()->admin()->create();
    $contactMessage = ContactMessage::factory()->create(['first_name' => 'Jean', 'message' => "Ligne 1\nLigne 2"]);

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.show', $contactMessage))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/contact-messages/show')
            ->where('contactMessage.id', $contactMessage->id)
            ->where('contactMessage.firstName', 'Jean')
            ->where('contactMessage.message', "Ligne 1\nLigne 2")
            ->where('contactMessage.isRead', true)
        );

    expect($contactMessage->fresh()->isRead())->toBeTrue();
});

test('keeps the first reading date when a message is shown again', function () {
    $admin = User::factory()->admin()->create();
    $firstReading = now()->subDays(2)->startOfSecond();
    $contactMessage = ContactMessage::factory()->create(['read_at' => $firstReading]);

    $this->actingAs($admin)
        ->get(route('admin.contact-messages.show', $contactMessage))
        ->assertOk();

    expect($contactMessage->fresh()->read_at->equalTo($firstReading))->toBeTrue();
});

test('deletes a message', function (string $state) {
    $user = User::factory()->{$state}()->create();
    $contactMessage = ContactMessage::factory()->create();
    $otherMessage = ContactMessage::factory()->create();

    $this->actingAs($user)
        ->delete(route('admin.contact-messages.destroy', $contactMessage))
        ->assertRedirect(route('admin.contact-messages.index'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Message supprimé.']);

    $this->assertModelMissing($contactMessage);
    $this->assertModelExists($otherMessage);
})->with(['admin', 'developer']);
