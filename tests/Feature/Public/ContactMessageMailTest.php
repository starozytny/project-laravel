<?php

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    config(['mail.contact_recipient' => 'admin@example.com']);
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function contactInput(array $overrides = []): array
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

test('sends the message to the configured recipient', function () {
    Mail::fake();

    $this->post(route('public.contact.store'), contactInput())
        ->assertRedirect(route('public.contact.create'));

    Mail::assertSentCount(1);
    Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
        return $mail->hasTo('admin@example.com')
            && $mail->hasSubject('Nouveau message de contact de Jean Dupont')
            && $mail->contactMessage->is(ContactMessage::query()->firstOrFail());
    });
});

test('shows the message of the visitor in the email', function () {
    $mailable = new ContactMessageReceived(
        ContactMessage::factory()->create(['message' => 'Bonjour, je souhaite en savoir plus.'])
    );

    $mailable->assertSeeInHtml('Bonjour, je souhaite en savoir plus.');
});

test('lets the recipient reply to the visitor when an email address is given', function () {
    Mail::fake();

    $this->post(route('public.contact.store'), contactInput());

    Mail::assertSent(ContactMessageReceived::class, fn (ContactMessageReceived $mail) => $mail->hasReplyTo('jean@example.com'));
});

test('has no reply address when the visitor gave no email address', function () {
    Mail::fake();

    $this->post(route('public.contact.store'), contactInput(['email' => null]));

    Mail::assertSent(ContactMessageReceived::class, fn (ContactMessageReceived $mail) => $mail->replyTo === []);
});

test('sends nothing when the input is invalid', function () {
    Mail::fake();

    $this->post(route('public.contact.store'), contactInput(['phone' => '']))
        ->assertSessionHasErrors('phone');

    Mail::assertNothingSent();
    $this->assertDatabaseCount('contact_messages', 0);
});

test('keeps the message and reports the error when the email cannot be sent', function () {
    Exceptions::fake();
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP is down'));

    $this->post(route('public.contact.store'), contactInput())
        ->assertRedirect(route('public.contact.create'))
        ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Votre message a été envoyé.']);

    $this->assertDatabaseCount('contact_messages', 1);
    Exceptions::assertReported(RuntimeException::class);
});
