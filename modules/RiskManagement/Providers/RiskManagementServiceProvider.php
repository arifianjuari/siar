<?php

namespace Modules\RiskManagement\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RiskManagementServiceProvider extends ServiceProvider
{
    /**
     * Module namespace
     * @var string
     */
    protected $moduleName = 'RiskManagement';
    
    /**
     * Module lowercase name
     * @var string
     */
    protected $moduleNameLower = 'risk-management';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerMigrations();
        $this->registerTranslations();
    }
    
    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        $routePath = __DIR__ . '/../Http/routes.php';
        if (file_exists($routePath)) {
            $this->loadRoutesFrom($routePath);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = __DIR__ . '/../Config/config.php';
        if (file_exists($configPath)) {
            $this->publishes([
                $configPath => config_path($this->moduleNameLower . '.php'),
            ], 'config');
            $this->mergeConfigFrom($configPath, $this->moduleNameLower);
        }
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        $sourcePath = __DIR__ . '/../Resources/Views';
        
        // Load views from module's Resources/Views directory
        $this->loadViewsFrom($sourcePath, $this->moduleNameLower);
        
        // Allow publishing views to resources/views/vendor for customization
        $this->publishes([
            $sourcePath => resource_path('views/vendor/' . $this->moduleNameLower)
        ], ['views', $this->moduleNameLower . '-module-views']);
    }

    /**
     * Register translations.
     */
    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);
        $sourceLangPath = __DIR__ . '/../Resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } elseif (is_dir($sourceLangPath)) {
            $this->loadTranslationsFrom($sourceLangPath, $this->moduleNameLower);
        }
    }

    /**
     * Register migrations.
     */
    protected function registerMigrations(): void
    {
        $migrationPath = __DIR__ . '/../Database/Migrations';
        if (is_dir($migrationPath)) {
            $this->loadMigrationsFrom($migrationPath);
            $this->publishes([
                $migrationPath => database_path('migrations')
            ], ['migrations', $this->moduleNameLower . '-module-migrations']);
        }
    }
}
