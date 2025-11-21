<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
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
        $this->registerModuleRoutes();
        $this->registerModuleViews();
    }

    /**
     * Register routes for all modules.
     */
    protected function registerModuleRoutes(): void
    {
        $routesPath = base_path('routes/modules');

        if (File::isDirectory($routesPath)) {
            $routeFiles = File::files($routesPath);

            foreach ($routeFiles as $routeFile) {
                Route::middleware('web')
                    ->group($routeFile->getPathname());
            }
        }
    }

    /**
     * Register views for all modules.
     */
    protected function registerModuleViews(): void
    {
        $viewsPath = resource_path('views/modules');

        if (File::isDirectory($viewsPath)) {
            $moduleFolders = File::directories($viewsPath);

            foreach ($moduleFolders as $moduleFolder) {
                $moduleName = basename($moduleFolder);
                $this->loadViewsFrom($moduleFolder, "modules.{$moduleName}");
            }
        }
    }
}
