<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class WorkUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tenant RS Bhayangkara dengan nama yang sesuai di database
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Hasta Brata Batu')->first();

        if (!$tenant) {
            $this->command->error('Tenant RS Bhayangkara tidak ditemukan!');
            return;
        }

        $this->command->info("Membuat unit kerja untuk tenant: {$tenant->name}");

        $units = [
            [
                'name' => 'Wakil Direktur Pelayanan Medis',
                'type' => 'medical',
                'code' => 'WADIRUM',
            ],
            [
                'name' => 'Wakil Direktur Umum dan Keuangan',
                'type' => 'non-medical',
                'code' => 'WADIRYANMED',
            ],
            [
                'name' => 'Wakil Direktur Keperawatan',
                'type' => 'medical',
                'code' => 'WADIRWAT',
            ],
            // Sub Bagian
            [
                'name' => 'Sub Bagian Kepegawaian',
                'type' => 'non-medical',
                'code' => 'SUBAGKEPEG',
            ],
            [
                'name' => 'Sub Bagian Keuangan',
                'type' => 'non-medical',
                'code' => 'SUBAGKEU',
            ],
            [
                'name' => 'Sub Bagian Umum dan Rumah Tangga',
                'type' => 'non-medical',
                'code' => 'SUBAGUMRT',
            ],
            // Unit Penunjang
            [
                'name' => 'Unit Farmasi',
                'type' => 'supporting',
                'code' => 'UNITFARM',
            ],
            [
                'name' => 'Unit Gizi',
                'type' => 'supporting',
                'code' => 'UNITGIZI',
            ],
            [
                'name' => 'Unit Laboratorium',
                'type' => 'medical',
                'code' => 'UNITLAB',
            ],
            [
                'name' => 'Unit Radiologi',
                'type' => 'medical',
                'code' => 'UNITRAD',
            ],
            [
                'name' => 'Unit Rekam Medis',
                'type' => 'supporting',
                'code' => 'UNITREKMED',
            ],
            [
                'name' => 'Unit Laundry',
                'type' => 'supporting',
                'code' => 'UNITLAUND',
            ],
            [
                'name' => 'Unit CSSD',
                'type' => 'supporting',
                'code' => 'UNITCSSD',
            ],
            [
                'name' => 'Unit IT',
                'type' => 'supporting',
                'code' => 'UNITIT',
            ],
            // Unit Pelayanan
            [
                'name' => 'Unit IGD',
                'type' => 'medical',
                'code' => 'UNITIGD',
            ],
            [
                'name' => 'Unit Rawat Jalan',
                'type' => 'medical',
                'code' => 'UNITRJ',
            ],
            [
                'name' => 'Unit Rawat Inap',
                'type' => 'medical',
                'code' => 'UNITRI',
            ],
            [
                'name' => 'Unit VK (Bersalin)',
                'type' => 'medical',
                'code' => 'UNITVK',
            ],
            [
                'name' => 'Unit Kamar Operasi',
                'type' => 'medical',
                'code' => 'UNITOK',
            ],
            // Unit Penunjang Klinis Lain
            [
                'name' => 'Unit K3RS',
                'type' => 'supporting',
                'code' => 'UNITK3RS',
            ],
            [
                'name' => 'Unit PPI',
                'type' => 'supporting',
                'code' => 'UNITPPI',
            ],
            [
                'name' => 'Unit Mutu',
                'type' => 'supporting',
                'code' => 'UNITMUTU',
            ],
            [
                'name' => 'Unit Diklat',
                'type' => 'supporting',
                'code' => 'UNITDIKLAT',
            ],
        ];

        $createdCount = 0;
        foreach ($units as $index => $unit) {
            // Gunakan insert daripada model untuk lebih fleksibel dengan struktur tabel
            DB::table('work_units')->updateOrInsert(
                [
                    'tenant_id' => $tenant->id,
                    'unit_name' => $unit['name'], // Menggunakan unit_name sesuai kolom di database
                ],
                [
                    'description' => 'Unit kerja ' . $unit['name'],
                    'unit_code' => $unit['code'],
                    'unit_type' => $unit['type'], // Sesuai enum: medical, non-medical, supporting
                    'is_active' => true,
                    'order' => $index + 1,
                ]
            );
            $createdCount++;
        }

        $this->command->info("Berhasil membuat/memperbarui {$createdCount} unit kerja untuk tenant {$tenant->name}");
    }
}
