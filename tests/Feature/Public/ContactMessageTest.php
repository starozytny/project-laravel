<?php

use App\Models\ContactMessage;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function validContactInput(array $overrides = []): array
{
    return [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'company' => 'Acme',
        'email' => 'jean@example.com',
        'phone' => '06 12 34 56 78',
        'message' => 'Bonjour, je souhaite en savoir plus.',
        ...$overrides,
    ];
}

test('renders the contact form for guests and authenticated users', function () {
    $this->get(route('public.contact.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('public/contact/create'));

    $this->actingAs(User::factory()->create())
        ->get(route('public.contact.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('public/contact/create'));
});

test('stores a contact message sent by a guest', function () {
    $this->post(route('public.contact.store'), validContactInput())
        ->assertRedirect(route('public.contact.create'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Votre message a été envoyé.']);

    $this->assertDatabaseCount('contact_messages', 1);

    $message = ContactMessage::query()->firstOrFail();

    expect($message->first_name)->toBe('Jean')
        ->and($message->last_name)->toBe('Dupont')
        ->and($message->company)->toBe('Acme')
        ->and($message->email)->toBe('jean@example.com')
        ->and($message->phone)->toBe('06 12 34 56 78')
        ->and($message->message)->toBe('Bonjour, je souhaite en savoir plus.')
        ->and($message->isRead())->toBeFalse();
});

test('accepts a message without company and email address', function () {
    $this->post(route('public.contact.store'), validContactInput(['company' => null, 'email' => null]))
        ->assertRedirect(route('public.contact.create'));

    $message = ContactMessage::query()->firstOrFail();

    expect($message->company)->toBeNull()
        ->and($message->email)->toBeNull();
});

test('cannot mark a message as read through the form', function () {
    $this->post(route('public.contact.store'), validContactInput(['read_at' => now()->toDateTimeString()]))
        ->assertRedirect(route('public.contact.create'));

    expect(ContactMessage::query()->firstOrFail()->read_at)->toBeNull();
});

test('rejects invalid input without storing a message', function (array $overrides, string $errorKey) {
    $this->from(route('public.contact.create'))
        ->post(route('public.contact.store'), validContactInput($overrides))
        ->assertRedirect(route('public.contact.create'))
        ->assertSessionHasErrors($errorKey);

    $this->assertDatabaseCount('contact_messages', 0);
})->with([
    'missing first name' => [['first_name' => ''], 'first_name'],
    'missing last name' => [['last_name' => ''], 'last_name'],
    'missing phone' => [['phone' => ''], 'phone'],
    'missing message' => [['message' => ''], 'message'],
    'too short message' => [['message' => 'Court'], 'message'],
    'invalid email' => [['email' => 'pas-un-email'], 'email'],
    'too long first name' => [['first_name' => str_repeat('a', 256)], 'first_name'],
    'too long phone' => [['phone' => str_repeat('1', 31)], 'phone'],
]);
