<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Module;

class TenantModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dapatkan semua tenant
        $tenants = Tenant::all();

        // Tambahkan modul untuk tenant
        foreach ($tenants as $tenant) {
            $modules = Module::whereIn('slug', [
                'user-management',
                'document-management',
                'risk-management',
                'correspondence',
                'individual-performance',
                'work-units',
                'spo-management',
                'activity-management'  // Tambahkan modul pengelolaan kegiatan
            ])->get();

            foreach ($modules as $module) {
                // Attach module ke tenant jika belum ada
                if (!$tenant->modules()->where('module_id', $module->id)->exists()) {
                    $tenant->modules()->attach($module->id, [
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
