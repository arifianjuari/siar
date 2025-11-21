<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan modul Aktivitas ke tabel modules
        DB::table('modules')->insert([
            'name' => 'Pengelolaan Kegiatan',
            'code' => 'activity-management',
            'slug' => 'activity-management',
            'description' => 'Modul untuk mengelola seluruh kegiatan dan tindak lanjut dari berbagai modul lainnya',
            'icon' => 'fa-tasks',
            'order' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dapatkan ID modul aktivitas
        $moduleId = DB::table('modules')->where('code', 'activity-management')->value('id');

        // Tambahkan modul ke semua tenant aktif
        $activeTenants = DB::table('tenants')->where('is_active', true)->get();
        foreach ($activeTenants as $tenant) {
            DB::table('tenant_modules')->insert([
                'tenant_id' => $tenant->id,
                'module_id' => $moduleId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ambil ID modul
        $moduleId = DB::table('modules')->where('code', 'activity-management')->value('id');

        if ($moduleId) {
            // Hapus dari tenant_modules
            DB::table('tenant_modules')->where('module_id', $moduleId)->delete();

            // Hapus dari modules
            DB::table('modules')->where('id', $moduleId)->delete();
        }
    }
};
