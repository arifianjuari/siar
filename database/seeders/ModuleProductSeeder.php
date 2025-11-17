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
        // Ambil tenant yang ada (prioritaskan RS Bhayangkara jika ada), fallback ke tenant pertama
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Hasta Brata Batu')->first() ?? Tenant::first();

        if (!$tenant) {
            echo "Tidak ada tenant. Lewati ModuleProductSeeder.\n";
            return;
        }

        // Jika model ModuleProduct tidak tersedia, lewati dengan aman
        if (!class_exists(ModuleProduct::class)) {
            echo "Model ModuleProduct tidak ditemukan. Lewati ModuleProductSeeder.\n";
            return;
        }

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
            // Tabel modules tidak memiliki kolom tenant_id, cari berdasarkan nama saja
            $module = Module::where('name', $moduleName)->first();

            if (!$module) {
                echo "Module $moduleName tidak ditemukan. Lewati.\n";
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

