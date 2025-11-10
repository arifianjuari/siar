<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Call KendaliMutuBiayaSeeder
        $this->call(KendaliMutuBiayaSeeder::class);

        // Call KendaliMutuBiayaModuleSeeder
        $this->call(KendaliMutuBiayaModuleSeeder::class);
    }
}
