<?php

namespace Modules\SPOManagement\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\SPOManagement\Models\SPO;
use Modules\SPOManagement\Policies\SPOPolicy;

class SPOManagementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Load module config
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'spo-management'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views', 'spo-management');

        // Register policies
        Gate::policy(SPO::class, SPOPolicy::class);
        Gate::policy(\App\Models\SPO::class, SPOPolicy::class); // Backward compatibility

        // Publish config (optional)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/config.php' => config_path('spo-management.php'),
            ], 'spo-management-config');

            // Publish views (optional)
            $this->publishes([
                __DIR__ . '/../Resources/Views' => resource_path('views/vendor/spo-management'),
            ], 'spo-management-views');
        }
    }
}
