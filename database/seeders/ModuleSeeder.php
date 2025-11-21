<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'User Management',
                'code' => 'user-management',
                'description' => 'Manajemen pengguna dan hak akses',
                'icon' => 'fas fa-users',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Dashboard',
                'code' => 'dashboard',
                'description' => 'Dashboard aplikasi',
                'icon' => 'fas fa-tachometer-alt',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Pengelolaan Kegiatan',
                'code' => 'activity-management',
                'description' => 'Modul untuk mengelola dan memantau kegiatan organisasi',
                'icon' => 'calendar',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($modules as $module) {
            // Hindari duplikasi berdasarkan code
            Module::updateOrCreate(
                ['code' => $module['code']],
                $module
            );
        }
    }
}

