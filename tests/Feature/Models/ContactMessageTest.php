<?php

use App\Models\ContactMessage;

test('selects only the unread messages with the unread scope', function () {
    $unread = ContactMessage::factory()->create();
    ContactMessage::factory()->read()->create();

    expect(ContactMessage::unread()->pluck('id')->all())->toBe([$unread->id]);
});

test('tells whether a message has been read', function () {
    expect(ContactMessage::factory()->create()->isRead())->toBeFalse()
        ->and(ContactMessage::factory()->read()->create()->isRead())->toBeTrue();
});

test('marks an unread message as read', function () {
    $contactMessage = ContactMessage::factory()->create();

    $contactMessage->markAsRead();

    expect($contactMessage->fresh()->isRead())->toBeTrue();
});

test('keeps the first reading date when a message is marked as read again', function () {
    $firstReading = now()->subDays(2)->startOfSecond();
    $contactMessage = ContactMessage::factory()->create(['read_at' => $firstReading]);

    $contactMessage->markAsRead();

    expect($contactMessage->fresh()->read_at->equalTo($firstReading))->toBeTrue();
});
