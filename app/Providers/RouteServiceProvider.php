<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter for login attempts (5 attempts per minute)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limiter for registration (3 attempts per hour)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Rate limiter for export operations (10 per hour per user)
        RateLimiter::for('export', function (Request $request) {
            return $request->user()
                ? Limit::perHour(10)->by($request->user()->id)
                : Limit::perHour(5)->by($request->ip());
        });

        // Rate limiter for import operations (5 per hour per user)
        RateLimiter::for('import', function (Request $request) {
            return $request->user()
                ? Limit::perHour(5)->by($request->user()->id)
                : Limit::perHour(2)->by($request->ip());
        });

        // Rate limiter for password reset (3 per hour)
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Rate limiter for file uploads (20 per hour per user)
        RateLimiter::for('upload', function (Request $request) {
            return $request->user()
                ? Limit::perHour(20)->by($request->user()->id)
                : Limit::perHour(5)->by($request->ip());
        });

        // Rate limiter for public endpoints (60 requests per minute)
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Rate limiter for authenticated endpoints (30 requests per minute)
        RateLimiter::for('authenticated', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(30)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });

        // Rate limiter for admin endpoints (10 requests per minute)
        RateLimiter::for('admin', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(10)->by($request->user()->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        // Rate limiter for search operations (15 per minute)
        RateLimiter::for('search', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(15)->by($request->user()->id)
                : Limit::perMinute(5)->by($request->ip());
        });

        // Rate limiter for report generation (5 per hour)
        RateLimiter::for('reports', function (Request $request) {
            return $request->user()
                ? Limit::perHour(5)->by($request->user()->id)
                : Limit::perHour(2)->by($request->ip());
        });

        // Rate limiter for permission checks (strict to prevent abuse)
        RateLimiter::for('permission-check', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(100)->by($request->user()->id)
                : Limit::perMinute(20)->by($request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
