<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Tenant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dapatkan modul manajemen risiko
        $riskModule = Module::where('code', 'risk-management')->first();

        if (!$riskModule) {
            // Jika tidak ditemukan, lewati
            return;
        }

        // Dapatkan semua tenant
        $tenants = Tenant::all();

        // Tambahkan modul ke setiap tenant
        foreach ($tenants as $tenant) {
            // Periksa apakah tenant sudah memiliki modul ini
            $exists = DB::table('tenant_modules')
                ->where('tenant_id', $tenant->id)
                ->where('module_id', $riskModule->id)
                ->exists();

            if (!$exists) {
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $tenant->id,
                    'module_id' => $riskModule->id,
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
        // Dapatkan modul manajemen risiko
        $riskModule = Module::where('code', 'risk-management')->first();

        if ($riskModule) {
            // Hapus relasi modul dengan semua tenant
            DB::table('tenant_modules')->where('module_id', $riskModule->id)->delete();
        }
    }
};
