<?php

namespace Modules\ActivityManagement\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\ActivityManagement\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the module.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Define the "web" routes for the module.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $webRoutePath = __DIR__ . '/../Http/routes.php';
        if (file_exists($webRoutePath)) {
            Route::middleware('web')
                ->namespace($this->moduleNamespace)
                ->group($webRoutePath);
        }
    }

    /**
     * Define the "api" routes for the module.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $apiRoutePath = __DIR__ . '/../Http/api.php';
        if (file_exists($apiRoutePath)) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->moduleNamespace)
                ->group($apiRoutePath);
        }
    }
}
