<?php

namespace App\Actions\ContactMessage;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreateContactMessageAction
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function handle(array $input): ContactMessage
    {
        $contactMessage = ContactMessage::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'company' => $input['company'] ?? null,
            'email' => $input['email'] ?? null,
            'phone' => $input['phone'],
            'message' => $input['message'],
        ]);

        try {
            Mail::to(config('mail.contact_recipient'))
                ->send(new ContactMessageReceived($contactMessage));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $contactMessage;
    }
}
