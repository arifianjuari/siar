<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class TelescopeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Hanya register jika Laravel Telescope tersedia (dev dependency)
        if (!class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)) {
            return;
        }

        // Register custom Telescope configuration
        $this->registerTelescope();
    }

    /**
     * Register Telescope configuration
     */
    protected function registerTelescope(): void
    {
        if (!class_exists(\Laravel\Telescope\Telescope::class)) {
            return;
        }

        $telescope = \Laravel\Telescope\Telescope::class;
        
        // Telescope hanya berjalan di local, staging, dan ketika dipaksa berjalan melalui variabel lingkungan
        $telescope::night();

        $this->hideSensitiveRequestDetails();

        $telescope::filter(function ($entry) {
            if ($this->app->environment('local', 'staging')) {
                return true;
            }

            // Force enable telescope in any environment via .env
            if (config('telescope.enabled') === true) {
                return true;
            }

            return $entry->isReportableException() ||
                $entry->isFailedRequest() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });

        // Register gate untuk akses Telescope
        $this->registerGate();
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if (!class_exists(\Laravel\Telescope\Telescope::class)) {
            return;
        }

        if ($this->app->environment('local', 'staging')) {
            return;
        }

        $telescope = \Laravel\Telescope\Telescope::class;
        $telescope::hideRequestParameters(['_token', 'password', 'password_confirmation']);

        $telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function registerGate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            // Pastikan user memiliki role dan role-nya adalah superadmin
            return $user &&
                $user->role &&
                $user->role->slug === 'superadmin';
        });
    }
}
