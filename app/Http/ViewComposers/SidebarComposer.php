<?php

namespace App\Http\ViewComposers;

use App\Models\Tenant;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $activeModules = collect([]);
        $isTenantAdmin = false;
        $isSuperAdmin = false;
        
        try {
            if (!auth()->check()) {
                $view->with([
                    'activeModules' => $activeModules,
                    'isTenantAdmin' => $isTenantAdmin,
                    'isSuperAdmin' => $isSuperAdmin,
                ]);
                return;
            }
            
            $user = auth()->user();
            $tenant_id = session('tenant_id');
            
            // Check roles with proper tenant validation
            if ($user->role) {
                // Superadmin must have superadmin role AND be in System tenant
                $isSystemTenant = $user->tenant && ($user->tenant->id === 1 || $user->tenant->name === 'System');
                $isSuperAdmin = $user->role->slug === 'superadmin' && $isSystemTenant;
                $isTenantAdmin = $user->role->slug === 'tenant-admin';
            }
            
            // Get active modules with caching (only for non-superadmin)
            if (!$isSuperAdmin && $tenant_id) {
                $cacheKey = 'sidebar_modules_tenant_' . $tenant_id;
                
                $activeModules = Cache::remember($cacheKey, 3600, function () use ($tenant_id) {
                    $tenant = Tenant::find($tenant_id);
                    if (!$tenant) {
                        return collect([]);
                    }
                    
                    return $tenant->modules()
                        ->where('tenant_modules.is_active', true)
                        ->orderBy('name')
                        ->get();
                });
            }
            
        } catch (\Exception $e) {
            // Log error but don't break the page
            if (config('app.debug')) {
                Log::error('SidebarComposer error: ' . $e->getMessage());
            }
        }
        
        $view->with([
            'activeModules' => $activeModules,
            'isTenantAdmin' => $isTenantAdmin,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }
}
