<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Tenant;

class KendaliMutuBiayaModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Mulai seeding modul Kendali Mutu Biaya...\n";

        // Buat modul Kendali Mutu Biaya atau ambil jika sudah ada
        $module = Module::firstOrCreate(
            ['code' => 'KMKB'],
            [
                'name' => 'Kendali Mutu Biaya',
                'slug' => 'kendali-mutu-biaya',
                'description' => 'Modul untuk mengelola clinical pathway dan evaluasi kendali mutu biaya',
                'icon' => 'fas fa-chart-line',
                'order' => 10,
                'is_active' => true,
            ]
        );

        echo $module->wasRecentlyCreated
            ? "Modul Kendali Mutu Biaya berhasil dibuat.\n"
            : "Modul Kendali Mutu Biaya sudah ada, menggunakan data yang ada.\n";

        // Dapatkan semua tenant
        $tenants = Tenant::all();

        // Tambahkan modul untuk semua tenant
        foreach ($tenants as $tenant) {
            // Attach module ke tenant jika belum ada
            if (!$tenant->modules()->where('module_id', $module->id)->exists()) {
                $tenant->modules()->attach($module->id, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        echo "Modul Kendali Mutu Biaya berhasil ditambahkan ke semua tenant.\n";

        echo "Modul Kendali Mutu Biaya berhasil ditambahkan ke sistem.\n";
    }
}
