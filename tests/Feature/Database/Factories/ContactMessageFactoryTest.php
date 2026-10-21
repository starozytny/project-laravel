<?php

use App\Models\ContactMessage;

test('creates an unread message with every field by default', function () {
    $message = ContactMessage::factory()->create();

    expect($message->read_at)->toBeNull()
        ->and($message->isRead())->toBeFalse()
        ->and($message->first_name)->not->toBeEmpty()
        ->and($message->last_name)->not->toBeEmpty()
        ->and($message->company)->not->toBeEmpty()
        ->and($message->email)->not->toBeEmpty()
        ->and($message->phone)->not->toBeEmpty()
        ->and($message->message)->not->toBeEmpty();
});

test('creates a read message with the read state', function () {
    $message = ContactMessage::factory()->read()->create();

    expect($message->read_at)->not->toBeNull()
        ->and($message->isRead())->toBeTrue();
});

test('creates a message without an email address', function () {
    $message = ContactMessage::factory()->withoutEmail()->create();

    expect($message->email)->toBeNull();
});

test('creates a message without a company', function () {
    $message = ContactMessage::factory()->withoutCompany()->create();

    expect($message->company)->toBeNull();
});
