<?php

// Script untuk mendiagnosa hubungan role dan modul
// Cara penggunaan: php storage/temp_check_permissions.php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Module;
use App\Models\User;

echo "====== CEKK HUBUNGAN ROLE DAN MODUL ======\n\n";

// 1. Periksa semua role
echo "=== DAFTAR ROLE ===\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "Role: {$role->name} (ID: {$role->id})\n";
    echo "  Tenant: " . ($role->tenant ? $role->tenant->name . " (ID: {$role->tenant_id})" : "Tidak ada tenant") . "\n";

    // Periksa modul permission untuk role ini
    echo "  Modul Permissions:\n";

    // Ambil langsung dari database untuk memastikan data mentah
    $rolePermissions = DB::table('role_module_permissions')
        ->where('role_id', $role->id)
        ->join('modules', 'modules.id', '=', 'role_module_permissions.module_id')
        ->select('role_module_permissions.*', 'modules.name as module_name', 'modules.slug as module_slug', 'modules.code as module_code')
        ->get();

    if ($rolePermissions->isEmpty()) {
        echo "    - Tidak ada permission yang ditetapkan\n";
    } else {
        foreach ($rolePermissions as $perm) {
            echo "    - Modul: {$perm->module_name} (ID: {$perm->module_id}, Slug: {$perm->module_slug})\n";
            echo "      * can_view: " . ($perm->can_view ? "Ya" : "Tidak") . "\n";
            echo "      * can_create: " . ($perm->can_create ? "Ya" : "Tidak") . "\n";
            echo "      * can_edit: " . ($perm->can_edit ? "Ya" : "Tidak") . "\n";
            echo "      * can_delete: " . ($perm->can_delete ? "Ya" : "Tidak") . "\n";
        }
    }

    echo "\n";
}

// 2. Periksa apakah modul dengan slug 'user-management' ada
echo "=== PERIKSA MODUL USER MANAGEMENT ===\n";
$module = Module::where('slug', 'user-management')->first();
if (!$module) {
    echo "Modul dengan slug 'user-management' tidak ditemukan!\n";
} else {
    echo "Modul ditemukan: {$module->name} (ID: {$module->id})\n";
    echo "  Slug: {$module->slug}\n";
    echo "  Code: {$module->code}\n";

    // Periksa role mana yang memiliki akses ke modul ini
    echo "  Role dengan akses ke modul ini:\n";
    $rolePermissions = DB::table('role_module_permissions')
        ->where('module_id', $module->id)
        ->join('roles', 'roles.id', '=', 'role_module_permissions.role_id')
        ->select('role_module_permissions.*', 'roles.name as role_name')
        ->get();

    if ($rolePermissions->isEmpty()) {
        echo "    - Tidak ada role yang memiliki akses ke modul ini\n";
    } else {
        foreach ($rolePermissions as $perm) {
            echo "    - Role: {$perm->role_name} (ID: {$perm->role_id})\n";
            echo "      * can_view: " . ($perm->can_view ? "Ya" : "Tidak") . "\n";
            echo "      * can_create: " . ($perm->can_create ? "Ya" : "Tidak") . "\n";
            echo "      * can_edit: " . ($perm->can_edit ? "Ya" : "Tidak") . "\n";
            echo "      * can_delete: " . ($perm->can_delete ? "Ya" : "Tidak") . "\n";
        }
    }
}

echo "\n";

// 3. Periksa pengguna dengan role yang dipilih (contoh role_id = 16)
echo "=== PERIKSA USER DENGAN ROLE TENANT ADMIN (ID: 16) ===\n";
$users = User::where('role_id', 16)->get();
if ($users->isEmpty()) {
    echo "Tidak ada user dengan role_id = 16\n";
} else {
    foreach ($users as $user) {
        echo "User: {$user->name} (ID: {$user->id})\n";
        echo "  Email: {$user->email}\n";
        echo "  Role: " . ($user->role ? $user->role->name : "Tidak ada role") . "\n";
        echo "  Tenant: " . ($user->tenant ? $user->tenant->name : "Tidak ada tenant") . "\n";

        // Periksa apakah user memiliki akses ke modul user-management
        if ($module) {
            $hasView = $user->hasPermission($module, 'can_view');
            $hasCreate = $user->hasPermission($module, 'can_create');
            $hasEdit = $user->hasPermission($module, 'can_edit');
            $hasDelete = $user->hasPermission($module, 'can_delete');

            echo "  Permission untuk modul {$module->name}:\n";
            echo "    * can_view: " . ($hasView ? "Ya" : "Tidak") . "\n";
            echo "    * can_create: " . ($hasCreate ? "Ya" : "Tidak") . "\n";
            echo "    * can_edit: " . ($hasEdit ? "Ya" : "Tidak") . "\n";
            echo "    * can_delete: " . ($hasDelete ? "Ya" : "Tidak") . "\n";
        }
    }
}

echo "\n";

// 4. Trace masalah pada hasPermission
echo "=== TRACE hasPermission METHOD ===\n";
$roleId = 16; // Ganti dengan role_id yang ingin diperiksa
$role = Role::find($roleId);

if (!$role) {
    echo "Role dengan ID {$roleId} tidak ditemukan\n";
} else {
    echo "Role: {$role->name} (ID: {$role->id})\n";

    // Periksa implementasi hasPermission
    if ($module) {
        $pivotRecord = DB::table('role_module_permissions')
            ->where('role_id', $roleId)
            ->where('module_id', $module->id)
            ->first();

        echo "  Mencari di tabel role_module_permissions:\n";
        echo "  Query: SELECT * FROM role_module_permissions WHERE role_id = {$roleId} AND module_id = {$module->id}\n";

        if (!$pivotRecord) {
            echo "  Record tidak ditemukan!\n";
        } else {
            echo "  Record ditemukan:\n";
            echo "    * ID: {$pivotRecord->id}\n";
            echo "    * can_view: " . ($pivotRecord->can_view ? "1" : "0") . "\n";
            echo "    * can_create: " . ($pivotRecord->can_create ? "1" : "0") . "\n";
            echo "    * can_edit: " . ($pivotRecord->can_edit ? "1" : "0") . "\n";
            echo "    * can_delete: " . ($pivotRecord->can_delete ? "1" : "0") . "\n";
        }

        // Periksa implementasi hasPermission di Role model
        echo "\n  Pemeriksaan implementasi hasPermission di Role model:\n";
        $rolePermission = $role->hasPermission($module->code, 'can_view');
        echo "  Role->hasPermission('{$module->code}', 'can_view') = " . ($rolePermission ? "true" : "false") . "\n";

        $rolePermission = $role->hasPermission($module->code, 'can_edit');
        echo "  Role->hasPermission('{$module->code}', 'can_edit') = " . ($rolePermission ? "true" : "false") . "\n";

        $rolePermission = $role->hasPermission($module->code, 'can_delete');
        echo "  Role->hasPermission('{$module->code}', 'can_delete') = " . ($rolePermission ? "true" : "false") . "\n";

        // Periksa implementasi modulePermissions relation
        $modulePermissions = $role->modulePermissions()
            ->where('modules.id', $module->id)
            ->first();

        echo "\n  Pemeriksaan implementasi modulePermissions relation:\n";
        echo "  Query: SELECT * FROM modules INNER JOIN role_module_permissions ON modules.id = role_module_permissions.module_id WHERE role_module_permissions.role_id = {$roleId} AND modules.id = {$module->id}\n";

        if (!$modulePermissions) {
            echo "  Relationship tidak mengembalikan data!\n";
        } else {
            echo "  Relationship mengembalikan data:\n";
            echo "    * Module: {$modulePermissions->name}\n";
            echo "    * can_view: " . ($modulePermissions->pivot->can_view ? "1" : "0") . "\n";
            echo "    * can_create: " . ($modulePermissions->pivot->can_create ? "1" : "0") . "\n";
            echo "    * can_edit: " . ($modulePermissions->pivot->can_edit ? "1" : "0") . "\n";
            echo "    * can_delete: " . ($modulePermissions->pivot->can_delete ? "1" : "0") . "\n";
        }
    }
}

echo "\n=== SELESAI ===\n";
