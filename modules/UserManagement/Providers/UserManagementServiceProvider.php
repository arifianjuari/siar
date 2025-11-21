<?php

namespace Modules\UserManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserManagementServiceProvider extends ServiceProvider
{
    /**
     * @var string Module name
     */
    protected $moduleName = 'UserManagement';

    /**
     * @var string Module name in lowercase
     */
    protected $moduleNameLower = 'user-management';

    /**
     * Register services.
     */
    public function register(): void
    {
        // Register module config
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerMigrations();
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Register module routes.
     */
    protected function registerRoutes(): void
    {
        if (!$this->app->routesAreCached()) {
            Route::middleware('web')
                ->group(module_path($this->moduleName, 'Http/routes.php'));
        }
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/Views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     */
    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/Lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register migrations.
     */
    protected function registerMigrations(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get publishable view paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach ($this->app['config']->get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
