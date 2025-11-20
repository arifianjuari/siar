<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PermissionService as singleton
        $this->app->singleton(\App\Services\PermissionService::class, function ($app) {
            return new \App\Services\PermissionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Fix view cache path if config is invalid
        $this->fixViewCachePathConfig();
        
        $this->registerGlobalHelpers();
        
        // Skip view-related operations during package discovery to avoid cache path errors
        // Package discovery happens during composer install before storage directories exist
        if ($this->shouldRegisterViewServices()) {
            $this->ensureViewCacheDirectoryExists();
            $this->registerBladeDirectives();
            $this->configureDebugMode();
            $this->registerViewComposers();
            
            // Set default pagination view to Bootstrap 5
            \Illuminate\Pagination\Paginator::useBootstrap();
        }

        // Register morphMap for ActivityAssignee
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'user' => \App\Models\User::class,
            'work_unit' => \App\Models\WorkUnit::class,
        ]);

        // Register enum type for Doctrine DBAL
        if (class_exists('Doctrine\DBAL\Types\Type')) {
            if (!\Doctrine\DBAL\Types\Type::hasType('enum')) {
                \Doctrine\DBAL\Types\Type::addType('enum', 'Doctrine\DBAL\Types\StringType');
            }
            \Doctrine\DBAL\Types\Type::getType('enum')->canRequireSQLConversion(true);
        }
    }

    /**
     * Fix view cache path config if it's invalid
     * This prevents "invalid cache path" errors in production
     */
    protected function fixViewCachePathConfig(): void
    {
        $currentPath = config('view.compiled');
        
        // If config path is empty or invalid, set it to the correct default
        if (empty($currentPath) || !is_string($currentPath) || trim($currentPath) === '') {
            $correctPath = storage_path('framework/views');
            config(['view.compiled' => $correctPath]);
            
            // Also update the view config in the container
            $this->app['config']->set('view.compiled', $correctPath);
        }
    }

    /**
     * Determine if view services should be registered
     * Skip during package discovery to prevent cache path errors
     */
    protected function shouldRegisterViewServices(): bool
    {
        // Check if we're running package:discover command
        if ($this->app->runningInConsole()) {
            $argv = $_SERVER['argv'] ?? [];
            // Skip if running package:discover
            if (in_array('package:discover', $argv)) {
                return false;
            }
        }
        
        // Check if view compiled path is valid
        $viewCachePath = config('view.compiled');
        
        // If config returns empty/null/invalid, skip
        if (empty($viewCachePath) || !is_string($viewCachePath)) {
            return false;
        }
        
        // Check if the path exists and is writable
        if (!is_dir($viewCachePath) || !is_writable($viewCachePath)) {
            return false;
        }
        
        return true;
    }

    /**
     * Ensure view cache directory exists
     */
    protected function ensureViewCacheDirectoryExists(): void
    {
        try {
            // Get view cache path, fallback to default if config not available
            $viewCachePath = config('view.compiled');
            
            // If config returns empty/null (during package discovery), use default path
            if (empty($viewCachePath)) {
                $viewCachePath = storage_path('framework/views');
            }
            
            // Validate path is not empty and is a valid string
            if (!is_string($viewCachePath) || trim($viewCachePath) === '') {
                return; // Skip if path is invalid
            }
            
            // Create directory if it doesn't exist
            if (!is_dir($viewCachePath)) {
                @mkdir($viewCachePath, 0755, true);
            }
            
            // Ensure it's writable
            if (is_dir($viewCachePath) && !is_writable($viewCachePath)) {
                @chmod($viewCachePath, 0755);
            }
        } catch (\Exception $e) {
            // Silently fail during package discovery or other edge cases
            // The directory will be created by the build script anyway
        }
    }

    /**
     * Register global helper functions
     */
    protected function registerGlobalHelpers(): void
    {
        if (!function_exists('getCurrentTenant')) {
            function getCurrentTenant()
            {
                try {
                    // Prioritaskan tenant dari user yang login
                    if (auth()->check() && auth()->user()->tenant) {
                        return auth()->user()->tenant;
                    }

                    // Jika tidak, coba ambil dari session
                    $tenant_id = session('tenant_id');
                    if ($tenant_id) {
                        return \App\Models\Tenant::find($tenant_id);
                    }
                } catch (\Exception $e) {
                    // Log error jika diperlukan
                    \Illuminate\Support\Facades\Log::error('Error mendapatkan tenant: ' . $e->getMessage());
                }
                return null;
            }
        }

        if (!function_exists('hasModulePermission')) {
            function hasModulePermission($moduleCode, $user = null, $permission = 'can_view')
            {
                try {
                    if (empty($moduleCode)) {
                        return false;
                    }

                    if (is_null($user)) {
                        if (!auth()->check()) {
                            return false;
                        }
                        $user = auth()->user();
                    }

                    if (class_exists('\App\Helpers\ModulePermissionHelper')) {
                        return \App\Helpers\ModulePermissionHelper::hasModulePermission($user, $moduleCode, $permission);
                    }

                    if (!$user->tenant) {
                        return false;
                    }

                    return true;
                } catch (\Exception $e) {
                    // Handling error
                    return false;
                }
            }
        }
    }

    /**
     * Register custom blade directives
     */
    protected function registerBladeDirectives(): void
    {
        \Illuminate\Support\Facades\Blade::directive('hasPermission', function ($expression) {
            return "<?php if(hasModulePermission({$expression})): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endhasPermission', function () {
            return "<?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('canView', function ($module) {
            return "<?php if(hasModulePermission({$module}, auth()->user(), 'can_view')): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('canCreate', function ($module) {
            return "<?php if(hasModulePermission({$module}, auth()->user(), 'can_create')): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('canEdit', function ($module) {
            return "<?php if(hasModulePermission({$module}, auth()->user(), 'can_edit')): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('canDelete', function ($module) {
            return "<?php if(hasModulePermission({$module}, auth()->user(), 'can_delete')): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('canExport', function ($module) {
            return "<?php if(hasModulePermission({$module}, auth()->user(), 'can_export')): ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endcanView', function () {
            return "<?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endcanCreate', function () {
            return "<?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endcanEdit', function () {
            return "<?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endcanDelete', function () {
            return "<?php endif; ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('endcanExport', function () {
            return "<?php endif; ?>";
        });
    }

    /**
     * Configure debug mode based on current route
     */
    protected function configureDebugMode(): void
    {
        // Disable debug info pada routes tertentu
        if (
            request()->is('tenant/profile') || request()->is('tenant/profile/*') ||
            request()->routeIs('tenant.profile') || request()->routeIs('tenant.profile.*')
        ) {
            // Set app.debug ke false untuk mematikan debug
            config(['app.debug' => false]);

            // Juga matikan berbagai komponen debug lainnya
            // Check if Debugbar is registered in the app container
            if (app()->bound('debugbar')) {
                app('debugbar')->disable();
            }

            // Daftarkan view share untuk digunakan di seluruh view
            view()->share('hideDebugInfo', true);

            // Set cookie untuk memberitahu browser bahwa debug mode dimatikan
            if (!request()->cookie('no_debug')) {
                cookie()->queue('no_debug', '1', 60); // 60 menit
            }
        }
    }

    /**
     * Register view composers
     */
    protected function registerViewComposers(): void
    {
        // Sidebar composer untuk optimasi performa
        \Illuminate\Support\Facades\View::composer(
            'layouts.partials.sidebar',
            \App\Http\ViewComposers\SidebarComposer::class
        );
    }
}
