<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan modul korespondensi ke tabel modules
        $module = Module::create([
            'name' => 'Korespondensi',
            'code' => 'correspondence',
            'slug' => 'correspondence',
            'description' => 'Modul untuk mengelola surat dan nota dinas internal rumah sakit',
            'icon' => 'fa-envelope',
            'order' => 15, // Sesuaikan dengan urutan yang diinginkan
            'is_active' => true,
        ]);

        // Tambahkan modul ke semua tenant yang ada
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Periksa apakah tenant sudah memiliki modul ini
            $exists = DB::table('tenant_modules')
                ->where('tenant_id', $tenant->id)
                ->where('module_id', $module->id)
                ->exists();

            if (!$exists) {
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $tenant->id,
                    'module_id' => $module->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dapatkan modul korespondensi
        $module = Module::where('slug', 'correspondence')->first();

        if ($module) {
            // Hapus relasi modul dengan semua tenant
            DB::table('tenant_modules')->where('module_id', $module->id)->delete();

            // Hapus modul dari tabel modules
            $module->delete();
        }
    }
};
