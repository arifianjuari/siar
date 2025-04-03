<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Module;
use App\Models\ModuleProduct;

class ModuleProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tenant RS Bhayangkara
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Batu')->first();

        // Data dummy modul dan produk-produknya
        $data = [
            'Manajemen Dokumen' => [
                ['name' => 'Daftar Dokumen', 'slug' => 'document-list'],
                ['name' => 'Evaluasi Berkala', 'slug' => 'document-review'],
                ['name' => 'Approval Dokumen', 'slug' => 'document-approval'],
            ],
            'Manajemen Korespondensi' => [
                ['name' => 'Surat Masuk', 'slug' => 'incoming-letter'],
                ['name' => 'Surat Keluar', 'slug' => 'outgoing-letter'],
                ['name' => 'Disposisi', 'slug' => 'letter-disposition'],
            ],
            'Manajemen Kinerja' => [
                ['name' => 'Indikator Mutu', 'slug' => 'kpi-indicator'],
                ['name' => 'Laporan Evaluasi', 'slug' => 'kpi-evaluation'],
            ],
            'Manajemen Risiko' => [
                ['name' => 'Laporan Risiko', 'slug' => 'risk-report'],
                ['name' => 'Mitigasi Risiko', 'slug' => 'risk-mitigation'],
            ]
        ];

        foreach ($data as $moduleName => $products) {
            $module = Module::where('name', $moduleName)->where('tenant_id', $tenant->id)->first();

            if (!$module) {
                echo "Module $moduleName tidak ditemukan untuk tenant RS Bhayangkara.\n";
                continue;
            }

            foreach ($products as $product) {
                ModuleProduct::updateOrCreate(
                    [
                        'module_id' => $module->id,
                        'slug' => $product['slug']
                    ],
                    [
                        'name' => $product['name'],
                        'module_id' => $module->id
                    ]
                );
            }
        }
    }
}
