<x-mail::message>
# Nouveau message

**{{ $contactMessage->first_name }} {{ $contactMessage->last_name }}**

- **Société** : {{ $contactMessage->company ?? 'non renseignée' }}
- **Téléphone** : {{ $contactMessage->phone }}
- **E-mail** : {{ $contactMessage->email ?? 'non renseigné' }}

<x-mail::panel>
{!! nl2br(e($contactMessage->message)) !!}
</x-mail::panel>

Cordialement,<br>
{{ config('app.name') }}
</x-mail::message>
