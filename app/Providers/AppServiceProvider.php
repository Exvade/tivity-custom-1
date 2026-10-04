<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('wishes', function (Request $request) {
            $invitation = $request->route('invitation');
            $invitationKey = is_object($invitation) ? $invitation->getKey() : (string) $invitation;
            $key = $invitationKey.':'.$request->ip();

            return [
                Limit::perMinute(3)->by('minute:'.$key),
                Limit::perDay(20)->by('day:'.$key),
            ];
        });

        RateLimiter::for('admin-login', fn (Request $request) => [
            Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()),
        ]);
    }
}
