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

        // Set default pagination view to Bootstrap 5
        \Illuminate\Pagination\Paginator::useBootstrap();
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
}
