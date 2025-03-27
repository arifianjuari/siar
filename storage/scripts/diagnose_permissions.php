<?php

use App\Models\Role;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== DIAGNOSA KONFIGURASI PERMISSION ===\n\n";

echo "I. MEMERIKSA ROLES\n";
echo "===================\n";
$roles = Role::all();

foreach ($roles as $role) {
    echo "ROLE: {$role->name} (ID: {$role->id})\n";
    echo "- Tenant: " . ($role->tenant ? $role->tenant->name : 'TIDAK ADA TENANT') . " (ID: {$role->tenant_id})\n";
    echo "- Status: " . ($role->is_active ? 'AKTIF' : 'NONAKTIF') . "\n";
    echo "- Jumlah user: " . $role->users()->count() . "\n";

    echo "\n";
}

echo "\nII. MEMERIKSA MODULE PERMISSIONS\n";
echo "==============================\n";

$modules = Module::all();

echo "Total modul: " . $modules->count() . "\n\n";

foreach ($roles as $role) {
    echo "ROLE: {$role->name} (ID: {$role->id})\n";
    echo "----------------------------------------\n";

    foreach ($modules as $module) {
        echo "  MODULE: {$module->name} (Code: {$module->code})\n";

        $permission = DB::table('role_module_permissions')
            ->where('role_id', $role->id)
            ->where('module_id', $module->id)
            ->first();

        if (!$permission) {
            echo "    TIDAK ADA IZIN DIKONFIGURASI!\n";
            continue;
        }

        echo "    - can_view: " . ($permission->can_view ? "YES ✅" : "NO ❌") . "\n";
        echo "    - can_create: " . ($permission->can_create ? "YES ✅" : "NO ❌") . "\n";
        echo "    - can_edit: " . ($permission->can_edit ? "YES ✅" : "NO ❌") . "\n";
        echo "    - can_delete: " . ($permission->can_delete ? "YES ✅" : "NO ❌") . "\n";
        echo "    - can_export: " . ($permission->can_export ? "YES ✅" : "NO ❌") . "\n";
        if (property_exists($permission, 'can_import')) {
            echo "    - can_import: " . ($permission->can_import ? "YES ✅" : "NO ❌") . "\n";
        }

        echo "\n";
    }

    echo "\n";
}

echo "\nIII. MEMERIKSA ROUTE PROTECTION\n";
echo "=============================\n";

$routes = \Illuminate\Support\Facades\Route::getRoutes();
$moduleRoutes = [];

foreach ($routes as $route) {
    $name = $route->getName();
    if (strpos($name, 'modules.') === 0) {
        $parts = explode('.', $name);
        if (count($parts) >= 3) {
            $moduleSlug = str_replace('modules.', '', $parts[0] . '.' . $parts[1]);
            $action = $parts[2];

            $middlewares = $route->middleware();

            // Cek apakah route memiliki middleware permission
            $hasPermissionMiddleware = false;
            $permissionType = 'NONE';

            foreach ($middlewares as $middleware) {
                if (strpos($middleware, 'check.permission:') === 0) {
                    $hasPermissionMiddleware = true;
                    $permissionParts = explode(',', str_replace('check.permission:', '', $middleware));
                    if (count($permissionParts) > 1) {
                        $permissionType = $permissionParts[1];
                    }
                }
            }

            $moduleRoutes[] = [
                'name' => $name,
                'module' => $moduleSlug,
                'action' => $action,
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'has_permission' => $hasPermissionMiddleware,
                'permission_type' => $permissionType
            ];
        }
    }
}

// Tampilkan hasil
foreach ($moduleRoutes as $route) {
    $permissionStatus = $route['has_permission']
        ? "✅ ({$route['permission_type']})"
        : "❌ NO PERMISSION CHECK";

    echo "{$route['method']} {$route['uri']} => {$route['name']} {$permissionStatus}\n";
}

echo "\n=== DIAGNOSA SELESAI ===\n";
