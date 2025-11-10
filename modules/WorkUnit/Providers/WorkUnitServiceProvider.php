<?php

namespace Modules\WorkUnit\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class WorkUnitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
    }

    /**
     * Register module routes.
     */
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                require __DIR__ . '/../Http/routes.php';
            });
    }

    /**
     * Register module views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Views', 'work-unit');
    }
}
