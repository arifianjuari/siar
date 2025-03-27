<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Module::create([
            'name' => 'Manajemen Produk',
            'code' => 'product-management',
            'description' => 'Modul untuk mengelola data produk',
            'icon' => 'bi bi-box',
            'order' => 2, // Sesuaikan dengan urutan yang diinginkan
            'is_active' => true,
        ]);
    }
}
