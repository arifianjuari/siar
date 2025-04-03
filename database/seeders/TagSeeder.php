<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil tenant RS Bhayangkara dengan nama yang sesuai
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Hasta Brata Batu')->first();

        if (!$tenant) {
            $this->command->error('Tenant RS Bhayangkara tidak ditemukan!');
            return;
        }

        $this->command->info("Membuat tag untuk tenant: {$tenant->name}");

        $tags = [
            ['name' => 'EP.1.1', 'description' => 'Visi, misi dan tujuan organisasi'],
            ['name' => 'EP.1.2', 'description' => 'Perencanaan strategis rumah sakit'],
            ['name' => 'EP.2.1', 'description' => 'Kompetensi SDM sesuai kebutuhan pelayanan'],
            ['name' => 'EP.3.1', 'description' => 'Ketersediaan SOP pelayanan medis'],
            ['name' => 'EP.4.1', 'description' => 'Sistem pelaporan dan evaluasi mutu'],
            ['name' => 'EP.5.1', 'description' => 'Manajemen Risiko dalam pelayanan'],
            ['name' => 'EP.6.1', 'description' => 'Pengelolaan fasilitas dan peralatan medis'],
            ['name' => 'EP.7.1', 'description' => 'Upaya pencegahan dan pengendalian infeksi'],
        ];

        $createdCount = 0;
        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $tag['name'],
                ],
                [
                    'slug' => Str::slug($tag['name']),
                    'description' => $tag['description'],
                ]
            );
            $createdCount++;
        }

        $this->command->info("Berhasil membuat/memperbarui {$createdCount} tag untuk tenant {$tenant->name}");
    }
}
