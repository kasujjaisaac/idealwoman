<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('manage-content', fn (User $user): bool => in_array($user->role, ['admin', 'editor'], true));
        Gate::define('manage-users', fn (User $user): bool => $user->role === 'admin');
        RateLimiter::for('admin-login', fn (Request $request): Limit => Limit::perMinute(5)->by('admin-login|'.$request->ip()));
        RateLimiter::for('admin-password-reset', fn (Request $request): Limit => Limit::perMinute(3)->by('admin-password-reset|'.$request->ip()));
        ResetPassword::createUrlUsing(fn (User $user, string $token): string => rtrim(config('app.url'), '/').route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()], false));
    }
}
