<?php

require_once __DIR__ . '/vendor/autoload.php';

// Inisiasi aplikasi Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

// Fungsi util untuk output berwarna
function out($text, $color = 'white')
{
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
    ];

    $reset = "\033[0m";
    echo $colors[$color] . $text . $reset . PHP_EOL;
}

out("DIAGNOSTIK AKSES MODUL DI DASHBOARD", "cyan");
out("==========================================", "cyan");
out("");

// 1. Periksa modul-modul yang tersedia
out("1. MODUL YANG TERSEDIA", "blue");
out("------------------------------------------", "blue");

$modules = Module::all();
if ($modules->isEmpty()) {
    out("Tidak ada modul yang tersedia di database!", "red");
} else {
    out("Total modul: " . $modules->count(), "green");

    foreach ($modules as $module) {
        out("  - ID: {$module->id}, Nama: {$module->name}, Kode: {$module->code}, Slug: {$module->slug}", "white");
    }
}

out("");

// 2. Periksa tenant yang aktif
out("2. TENANT YANG AKTIF", "blue");
out("------------------------------------------", "blue");

$tenants = Tenant::where('is_active', true)->get();
if ($tenants->isEmpty()) {
    out("Tidak ada tenant aktif!", "red");
} else {
    out("Total tenant aktif: " . $tenants->count(), "green");

    foreach ($tenants as $tenant) {
        out("  - ID: {$tenant->id}, Nama: {$tenant->name}", "white");

        // Periksa modul yang aktif untuk tenant ini
        $activeModules = $tenant->modules()->wherePivot('is_active', true)->get();

        if ($activeModules->isEmpty()) {
            out("    * Tidak ada modul aktif untuk tenant ini", "yellow");
        } else {
            out("    * Modul aktif: " . $activeModules->count(), "green");
            foreach ($activeModules as $module) {
                out("      + {$module->name} (ID: {$module->id})", "white");
            }
        }
    }
}

out("");

// 3. Periksa fungsi helper
out("3. PEMERIKSAAN FUNGSI HELPER", "blue");
out("------------------------------------------", "blue");

// Cek implementasi hasModulePermission di app/Helpers/helpers.php
if (function_exists('hasModulePermission')) {
    out("Fungsi hasModulePermission tersedia", "green");

    // Cek implementasi di ModulePermissionHelper
    if (class_exists('\\App\\Helpers\\ModulePermissionHelper')) {
        out("Kelas ModulePermissionHelper tersedia", "green");

        // Periksa method dalam kelas ModulePermissionHelper
        $methods = get_class_methods('\\App\\Helpers\\ModulePermissionHelper');
        out("Method dalam ModulePermissionHelper: " . implode(", ", $methods), "white");
    } else {
        out("Kelas ModulePermissionHelper tidak tersedia!", "red");
    }
} else {
    out("Fungsi hasModulePermission tidak tersedia!", "red");
}

out("");

// 4. Periksa user dan hak akses
out("4. USER DAN HAK AKSES", "blue");
out("------------------------------------------", "blue");

$users = User::whereHas('role')->whereHas('tenant')->get();
if ($users->isEmpty()) {
    out("Tidak ada user aktif dengan role dan tenant!", "red");
} else {
    out("Total user dengan role dan tenant: " . $users->count(), "green");

    foreach ($users as $user) {
        out("  - User: {$user->name} (ID: {$user->id})", "white");
        out("    * Email: {$user->email}", "white");
        out("    * Tenant: {$user->tenant->name} (ID: {$user->tenant_id})", "white");
        out("    * Role: {$user->role->name} (ID: {$user->role_id})", "white");

        // Cek modul aktif untuk tenant user
        $tenant = $user->tenant;
        $activeModules = $tenant->modules()->wherePivot('is_active', true)->get();

        if ($activeModules->isEmpty()) {
            out("    * Tidak ada modul aktif untuk tenant user ini", "yellow");
        } else {
            out("    * Modul aktif untuk tenant: " . $activeModules->count(), "green");

            // Periksa izin untuk setiap modul
            foreach ($activeModules as $module) {
                // Cek izin
                $rolePermission = $user->role->modulePermissions()
                    ->where('module_id', $module->id)
                    ->first();

                if ($rolePermission) {
                    $canView = $rolePermission->pivot->can_view ? "Ya" : "Tidak";
                    $canCreate = $rolePermission->pivot->can_create ? "Ya" : "Tidak";
                    $canEdit = $rolePermission->pivot->can_edit ? "Ya" : "Tidak";
                    $canDelete = $rolePermission->pivot->can_delete ? "Ya" : "Tidak";

                    out("      + Modul: {$module->name} (ID: {$module->id})", "white");
                    out(
                        "        ~ can_view: {$canView}, can_create: {$canCreate}, can_edit: {$canEdit}, can_delete: {$canDelete}",
                        $canView == "Ya" ? "green" : "red"
                    );
                } else {
                    out("      + Modul: {$module->name} (ID: {$module->id}) - TIDAK ADA IZIN!", "red");
                }
            }
        }
    }
}

out("");

// 5. Cek Template Dashboard
out("5. TEMPLATE DASHBOARD", "blue");
out("------------------------------------------", "blue");

$dashboardPath = base_path('resources/views/dashboard.blade.php');
if (file_exists($dashboardPath)) {
    out("File template dashboard ditemukan", "green");

    // Periksa konten file untuk mencari override fungsi hasModulePermission
    $content = file_get_contents($dashboardPath);
    if (strpos($content, 'function hasModulePermission') !== false) {
        out("PERHATIAN: File dashboard.blade.php berisi definisi fungsi hasModulePermission sendiri!", "red");
        out("Ini dapat menyebabkan masalah dengan pengaksesan modul di dashboard", "yellow");
    } else {
        out("File dashboard.blade.php tidak mengandung definisi fungsi hasModulePermission", "green");
    }

    // Periksa bagian yang menampilkan modul di dashboard
    if (strpos($content, 'app/modules') !== false || strpos($content, 'modules/') !== false) {
        out("Dashboard memiliki bagian yang menampilkan modul", "green");
    } else {
        out("PERHATIAN: Dashboard mungkin tidak memiliki bagian untuk menampilkan modul!", "yellow");
    }
} else {
    out("File template dashboard tidak ditemukan!", "red");
}

out("");

// 6. Cek Middleware
out("6. MIDDLEWARE CHECK MODULE ACCESS", "blue");
out("------------------------------------------", "blue");

$middlewarePath = app_path('Http/Middleware/CheckModuleAccess.php');
if (file_exists($middlewarePath)) {
    out("File middleware CheckModuleAccess ditemukan", "green");

    // Periksa konten middleware
    $content = file_get_contents($middlewarePath);
    if (strpos($content, 'hasModulePermission') !== false) {
        out("Middleware menggunakan fungsi hasModulePermission", "green");

        // Periksa parameter fungsi
        if (preg_match('/hasModulePermission\s*\(\s*([^,]+),\s*([^,]+),\s*([^)]+)\s*\)/', $content, $matches)) {
            out("Panggilan fungsi hasModulePermission ditemukan", "white");
            out("  - Parameter 1: {$matches[1]}", "white");
            out("  - Parameter 2: {$matches[2]}", "white");
            out("  - Parameter 3: {$matches[3]}", "white");

            // Cek apakah parameter 3 (permission) berisi 'can_view' atau 'view'
            if (trim($matches[3]) == "'view'" || trim($matches[3]) == '"view"') {
                out("PERHATIAN: Parameter permission menggunakan 'view' bukan 'can_view'!", "red");
                out("Ini dapat menyebabkan kegagalan pengecekan izin", "yellow");
            }
        }
    } else {
        out("PERHATIAN: Middleware tidak menggunakan fungsi hasModulePermission!", "red");
    }
} else {
    out("File middleware CheckModuleAccess tidak ditemukan!", "red");
}

out("");

// 7. Rekomendasi Perbaikan
out("7. REKOMENDASI PERBAIKAN", "blue");
out("------------------------------------------", "blue");
out("1. Periksa dashboard.blade.php dan hapus override fungsi hasModulePermission jika ada", "green");
out("2. Pastikan panggilan hasModulePermission di middleware CheckModuleAccess menggunakan 'can_view' bukan 'view'", "green");
out("3. Periksa apakah template dashboard memiliki bagian untuk menampilkan modul yang tersedia", "green");
out("4. Pastikan pengguna memiliki tenant dan role yang aktif", "green");
out("5. Pastikan modul telah diaktifkan untuk tenant pengguna", "green");
out("6. Pastikan role pengguna memiliki izin 'can_view' untuk modul yang diinginkan", "green");
out("7. Coba logout dan login kembali untuk memperbarui sesi", "green");

// 8. Tambahkan widget modul ke dashboard jika belum ada
out("");
out("8. TAMBAHKAN WIDGET MODUL KE DASHBOARD", "blue");
out("------------------------------------------", "blue");

$dashboardWidgetsPath = resource_path('views/layouts/partials/dashboard_widgets.blade.php');
if (!file_exists($dashboardWidgetsPath) || (file_exists($dashboardWidgetsPath) && !strpos(file_get_contents($dashboardWidgetsPath), 'Modul Tersedia'))) {
    out("Direkomendasikan untuk menambahkan widget modul ke dashboard_widgets.blade.php", "yellow");
    out("Buat bagian yang menampilkan modul yang tersedia untuk pengguna", "white");
} else {
    out("Widget modul sudah ada di dashboard", "green");
}

out("");
out("DIAGNOSTIK SELESAI", "cyan");
out("==========================================", "cyan");
out("Jalankan script ini untuk memperbaiki masalah yang ditemukan:", "yellow");
out("php fix_dashboard_modules.php", "white");
