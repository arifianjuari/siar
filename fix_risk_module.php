<?php

// Script perbaikan untuk modul risk-management
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Module;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "==== Script Perbaikan Modul Risk Management ====\n\n";

// 1. Periksa modul Risk Management
$riskModule = Module::where('code', 'risk-management')
    ->orWhere('slug', 'risk-management')
    ->first();

if (!$riskModule) {
    echo "Modul Risk Management tidak ditemukan dalam database!\n";

    // Buat modul baru
    $riskModule = new Module();
    $riskModule->name = 'Manajemen Risiko';
    $riskModule->description = 'Modul untuk mengelola laporan risiko dan insiden';
    $riskModule->code = 'risk-management';
    $riskModule->slug = 'risk-management';
    $riskModule->icon = 'fa-exclamation-triangle';
    $riskModule->is_active = true;
    $riskModule->version = '1.0.0';
    $riskModule->order = 5;
    $riskModule->save();

    echo "Modul Risk Management berhasil dibuat dengan ID: {$riskModule->id}\n";
} else {
    echo "Modul Risk Management ditemukan:\n";
    echo "- ID: {$riskModule->id}\n";
    echo "- Name: {$riskModule->name}\n";
    echo "- Code: {$riskModule->code}\n";
    echo "- Slug: {$riskModule->slug}\n";

    // Pastikan kode dan slug sesuai
    if ($riskModule->code !== 'risk-management' || $riskModule->slug !== 'risk-management') {
        $riskModule->code = 'risk-management';
        $riskModule->slug = 'risk-management';
        $riskModule->save();
        echo "Kode dan slug diperbarui ke 'risk-management'\n";
    }
}

// 2. Periksa akses modul untuk semua tenant
echo "\n==== Tenant dan Modul Risk Management ====\n";
$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "\nTenant: {$tenant->name} (ID: {$tenant->id})\n";

    // Cek apakah tenant memiliki akses ke modul menggunakan tabel tenant_modules
    $moduleAccess = DB::table('tenant_modules')
        ->where('tenant_id', $tenant->id)
        ->where('module_id', $riskModule->id)
        ->first();

    if (!$moduleAccess) {
        echo "- Tenant tidak memiliki akses ke modul Risk Management\n";

        // Tambahkan akses
        DB::table('tenant_modules')->insert([
            'tenant_id' => $tenant->id,
            'module_id' => $riskModule->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "- Akses tenant ke modul Risk Management berhasil ditambahkan\n";
    } else {
        echo "- Tenant memiliki akses ke modul Risk Management (is_active: " . ($moduleAccess->is_active ? "Ya" : "Tidak") . ")\n";

        // Aktifkan jika belum aktif
        if (!$moduleAccess->is_active) {
            DB::table('tenant_modules')
                ->where('tenant_id', $tenant->id)
                ->where('module_id', $riskModule->id)
                ->update([
                    'is_active' => true,
                    'updated_at' => now()
                ]);

            echo "- Akses tenant ke modul Risk Management diaktifkan\n";
        }
    }

    // 3. Periksa akses untuk semua role dalam tenant
    $roles = Role::where('tenant_id', $tenant->id)->get();

    echo "- Roles dalam tenant:\n";
    foreach ($roles as $role) {
        echo "  - Role: {$role->name} (ID: {$role->id})\n";

        // Cek permission
        $permission = DB::table('role_module_permissions')
            ->where('role_id', $role->id)
            ->where('module_id', $riskModule->id)
            ->first();

        if (!$permission) {
            echo "    - Role tidak memiliki permission untuk modul Risk Management\n";

            // Tambahkan permission
            DB::table('role_module_permissions')->insert([
                'role_id' => $role->id,
                'module_id' => $riskModule->id,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
                'can_export' => true,
                'can_import' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            echo "    - Permission untuk role berhasil ditambahkan\n";
        } else {
            echo "    - Permission saat ini: view(" . ($permission->can_view ? "Ya" : "Tidak") . "), create(" . ($permission->can_create ? "Ya" : "Tidak") . "), edit(" . ($permission->can_edit ? "Ya" : "Tidak") . "), delete(" . ($permission->can_delete ? "Ya" : "Tidak") . ")\n";

            // Update permission jika perlu
            if (!$permission->can_view || !$permission->can_create || !$permission->can_edit || !$permission->can_delete) {
                DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module_id', $riskModule->id)
                    ->update([
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_export' => true,
                        'can_import' => true,
                        'updated_at' => now()
                    ]);

                echo "    - Permission berhasil diperbarui ke akses penuh\n";
            }
        }
    }
}

// 4. Periksa rute web.php
echo "\n==== Memeriksa Rute ====\n";
$routesFile = __DIR__ . '/routes/web.php';
$routesContent = file_get_contents($routesFile);

$riskRoutePattern = "/risk-management.*middleware.*module:risk-management/i";
if (preg_match($riskRoutePattern, $routesContent)) {
    echo "Rute untuk modul Risk Management ditemukan di web.php\n";
} else {
    echo "Peringatan: Rute untuk modul Risk Management tidak terdeteksi di web.php\n";
}

// 5. Periksa Tenant model
echo "\n==== Memeriksa Model Tenant ====\n";
try {
    $reflector = new ReflectionClass(Tenant::class);
    $method = $reflector->getMethod('modules');
    $code = file_get_contents($reflector->getFileName());

    if (strpos($code, 'tenant_modules') !== false && strpos($code, 'module_tenant') === false) {
        echo "Model Tenant menggunakan tabel tenant_modules (benar)\n";
    } elseif (strpos($code, 'module_tenant') !== false) {
        echo "Peringatan: Model Tenant menggunakan tabel module_tenant (salah)\n";
    } else {
        echo "Tidak dapat menentukan nama tabel yang digunakan\n";
    }
} catch (Exception $e) {
    echo "Error saat memeriksa model Tenant: " . $e->getMessage() . "\n";
}

// 6. Periksa middleware
echo "\n==== Memeriksa Middleware ====\n";
$checkModuleAccessFile = __DIR__ . '/app/Http/Middleware/CheckModuleAccess.php';
$middlewareContent = file_get_contents($checkModuleAccessFile);

$bypassPattern = "/if\s*\(\s*\\\$moduleSlug\s*===\s*['\"]risk-management['\"]\s*\)/i";
if (preg_match($bypassPattern, $middlewareContent)) {
    echo "Peringatan: Ditemukan kode bypass untuk modul Risk Management di CheckModuleAccess.php\n";
} else {
    echo "Middleware CheckModuleAccess tidak memiliki bypass khusus untuk modul Risk Management\n";
}

// 7. Periksa Helper hasModulePermission
echo "\n==== Memeriksa Fungsi Helper ====\n";
$helpersFile = __DIR__ . '/app/Helpers/helpers.php';
$helpersContent = file_get_contents($helpersFile);

$helperBypassPattern = "/if\s*\(\s*\\\$moduleCode\s*===\s*['\"]risk-management['\"]\s*\)/i";
if (preg_match($helperBypassPattern, $helpersContent)) {
    echo "Peringatan: Ditemukan kode bypass untuk modul Risk Management di helper function\n";
} else {
    echo "Fungsi helper tidak memiliki bypass khusus untuk modul Risk Management\n";
}

// 8. Menampilkan informasi pengguna & tenant
echo "\n==== Informasi User & Tenant Saat Ini ====\n";
$users = User::with('role', 'tenant')->get();

echo "Total users: " . count($users) . "\n";
foreach ($users as $user) {
    echo "- User: {$user->name} (ID: {$user->id})\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: " . ($user->role ? $user->role->name : "Tidak ada") . "\n";
    echo "  Tenant: " . ($user->tenant ? $user->tenant->name : "Tidak ada") . "\n";

    if ($user->role && $user->tenant) {
        $rolePermission = DB::table('role_module_permissions')
            ->where('role_id', $user->role->id)
            ->where('module_id', $riskModule->id)
            ->first();

        if ($rolePermission) {
            echo "  Permission untuk Risk Management: view(" . ($rolePermission->can_view ? "Ya" : "Tidak") . "), create(" . ($rolePermission->can_create ? "Ya" : "Tidak") . ")\n";
        } else {
            echo "  Tidak memiliki permission untuk Risk Management\n";
        }
    }
    echo "\n";
}

// 9. Periksa implementasi model relasi
echo "\n==== Memeriksa Model Module dan Tenant ====\n";
$tenantModel = file_get_contents(__DIR__ . '/app/Models/Tenant.php');
$moduleModel = file_get_contents(__DIR__ . '/app/Models/Module.php');

// Nama tabel relasi pada model Tenant
preg_match('/public\s+function\s+modules.*?return\s+\$this->belongsToMany\(\s*.*?::class\s*,\s*[\'"]([^\'"]+)[\'"]/', $tenantModel, $tenantMatches);
if (!empty($tenantMatches[1])) {
    echo "Tenant model menggunakan tabel relasi: " . $tenantMatches[1] . "\n";
    if ($tenantMatches[1] !== 'tenant_modules') {
        echo "Peringatan: Nama tabel relasi yang digunakan di model Tenant tidak sesuai dengan tabel di database!\n";
    }
} else {
    echo "Tidak dapat mendeteksi nama tabel relasi pada model Tenant\n";
}

// Nama tabel relasi pada model Module
preg_match('/public\s+function\s+tenants.*?return\s+\$this->belongsToMany\(\s*.*?::class\s*,\s*[\'"]([^\'"]+)[\'"]/', $moduleModel, $moduleMatches);
if (!empty($moduleMatches[1])) {
    echo "Module model menggunakan tabel relasi: " . $moduleMatches[1] . "\n";
    if ($moduleMatches[1] !== 'tenant_modules') {
        echo "Peringatan: Nama tabel relasi yang digunakan di model Module tidak sesuai dengan tabel di database!\n";
    }
} else {
    echo "Tidak dapat mendeteksi nama tabel relasi pada model Module\n";
}

echo "\n==== Solusi untuk User ====\n";
echo "1. Pastikan Anda sudah logout dan login kembali agar session terupdate\n";
echo "2. Pastikan Anda mengakses URL yang benar: http://siar.test/modules/risk-management/risk-reports\n";
echo "3. Pastikan role Anda memiliki permission 'can_view' untuk modul ini\n";
echo "4. Jika masih bermasalah, lihat log Laravel di storage/logs/laravel.log\n";

echo "\n==== Selesai ====\n";
