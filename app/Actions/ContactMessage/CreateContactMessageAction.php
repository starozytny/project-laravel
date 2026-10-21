<?php

namespace App\Actions\ContactMessage;

use App\Models\ContactMessage;

class CreateContactMessageAction
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function handle(array $input): ContactMessage
    {
        return ContactMessage::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'company' => $input['company'] ?? null,
            'email' => $input['email'] ?? null,
            'phone' => $input['phone'],
            'message' => $input['message'],
        ]);
    }
}
