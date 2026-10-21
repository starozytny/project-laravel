<?php

namespace App\Http\Controllers\Public;

use App\Actions\ContactMessage\CreateContactMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessage\ContactMessageStoreRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    /**
     * Show the form to create a contact message
     */
    public function create(): Response
    {
        return Inertia::render('public/contact/create');
    }

    /**
     * Store a contact message
     */
    public function store(ContactMessageStoreRequest $request, CreateContactMessageAction $createContactMessageAction): RedirectResponse
    {
        $createContactMessageAction->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Votre message a été envoyé.']);

        return to_route('public.contact.create');
    }
}
