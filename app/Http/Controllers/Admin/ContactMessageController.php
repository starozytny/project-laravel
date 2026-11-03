<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactMessages = ContactMessage::query()
            ->orderByRaw('read_at is not null')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/contact-messages/index', [
            'contactMessages' => ContactMessageResource::collection($contactMessages),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead();

        return Inertia::render('admin/contact-messages/show', [
            'contactMessage' => (new ContactMessageResource($contactMessage))->resolve(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Message supprimé.']);

        return to_route('admin.contact-messages.index');
    }
}
