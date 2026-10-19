<?php

use App\Http\Responses\PasskeyLoginResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

function passkeyLoginRequest(User $user, bool $wantsJson): Request
{
    $request = Request::create('/passkeys/login', 'POST', server: $wantsJson ? ['HTTP_ACCEPT' => 'application/json'] : []);
    $request->setUserResolver(fn () => $user);

    return $request;
}

test('returns the admin dashboard url to admins logging in with a passkey', function () {
    $admin = User::factory()->admin()->create();

    $response = (new PasskeyLoginResponse)->toResponse(passkeyLoginRequest($admin, wantsJson: true));

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getData(true))->toBe(['redirect' => route('admin.dashboard')]);
});

test('returns the member area url to users logging in with a passkey', function () {
    $user = User::factory()->create();

    $response = (new PasskeyLoginResponse)->toResponse(passkeyLoginRequest($user, wantsJson: true));

    expect($response->getData(true))->toBe(['redirect' => route('user.dashboard')]);
});

test('sends admins back to the page they asked for before logging in with a passkey', function () {
    $admin = User::factory()->admin()->create();
    session()->put('url.intended', route('user.dashboard'));

    $response = (new PasskeyLoginResponse)->toResponse(passkeyLoginRequest($admin, wantsJson: true));

    expect($response->getData(true))->toBe(['redirect' => route('user.dashboard')]);
});

test('redirects admins to the admin dashboard when the request does not expect json', function () {
    $admin = User::factory()->admin()->create();

    $response = (new PasskeyLoginResponse)->toResponse(passkeyLoginRequest($admin, wantsJson: false));

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getTargetUrl())->toBe(route('admin.dashboard'));
});
