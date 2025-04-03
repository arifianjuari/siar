<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerformanceIndicator;
use App\Models\Tenant;
use App\Models\User;

class PerformanceIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tenant RS Bhayangkara
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Batu')->first();

        // Ambil user role 'Staf'
        $user = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Staf');
            })
            ->first();

        if (!$user) {
            $this->command->warn('User Staf tidak ditemukan.');
            return;
        }

        $indicators = [
            [
                'name' => 'Waktu Tanggap IGD < 5 Menit',
                'unit' => '%',
                'target' => 90,
            ],
            [
                'name' => 'Ketepatan Pelaporan Komplain',
                'unit' => '%',
                'target' => 100,
            ],
            [
                'name' => 'Infeksi Luka Operasi',
                'unit' => 'per 100 tindakan',
                'target' => 2,
            ],
        ];

        foreach ($indicators as $indicator) {
            PerformanceIndicator::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $indicator['name'],
                ],
                [
                    'unit' => $indicator['unit'],
                    'target' => $indicator['target'],
                    'created_by' => $user->id,
                    'tenant_id' => $tenant->id,
                ]
            );
        }
    }
}
