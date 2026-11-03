<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
        $this->configureRateLimiting();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : Password::min(6),
        );
    }

    /**
     * Configure default gates applications
     */
    protected function configureGates(): void
    {
        Gate::define('access-admin', fn (User $user): bool => $user->hasRoleOrAbove(UserRole::Admin));
    }

    /**
     * Configure antispam form
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('contact', function (Request $request): array {
            $tooManyMessages = fn (Request $request, array $headers) => back()->withErrors([
                'form' => 'Vous avez envoyé trop de messages. Réessayez plus tard.',
            ]);

            return [
                Limit::perMinute(2)->by($request->ip())->response($tooManyMessages),
                Limit::perHour(10)->by($request->ip())->response($tooManyMessages),
            ];
        });
    }
}
