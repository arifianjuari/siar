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
                'slug' => 'activity-management',
                'is_core' => false,
                'is_purchasable' => true,
                'is_visible' => true,
                'description' => 'Modul untuk mengelola dan memantau kegiatan organisasi',
                'version' => '1.0.0',
                'icon' => 'calendar',
                'permissions' => [
                    ['name' => 'view', 'description' => 'Melihat kegiatan'],
                    ['name' => 'create', 'description' => 'Membuat kegiatan baru'],
                    ['name' => 'edit', 'description' => 'Mengedit kegiatan'],
                    ['name' => 'delete', 'description' => 'Menghapus kegiatan'],
                    ['name' => 'assign', 'description' => 'Menugaskan pengguna ke kegiatan'],
                    ['name' => 'change_status', 'description' => 'Mengubah status kegiatan'],
                ]
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
