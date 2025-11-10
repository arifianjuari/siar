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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        $this->registerGlobalHelpers();
        $this->registerBladeDirectives();
        $this->configureDebugMode();

        // Set default pagination view to Bootstrap 5
        \Illuminate\Pagination\Paginator::useBootstrap();

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
            if (class_exists('\Barryvdh\Debugbar\Facade')) {
                \Barryvdh\Debugbar\Facade::disable();
            }

            // Daftarkan view share untuk digunakan di seluruh view
            view()->share('hideDebugInfo', true);

            // Set cookie untuk memberitahu browser bahwa debug mode dimatikan
            if (!request()->cookie('no_debug')) {
                cookie()->queue('no_debug', '1', 60); // 60 menit
            }
        }
    }
}
