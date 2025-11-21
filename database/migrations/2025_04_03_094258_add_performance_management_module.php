<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan modul baru untuk Performance Management
        DB::table('modules')->insert([
            'name' => 'KPI Individu',
            'code' => 'KPI',
            'description' => 'Modul untuk mengelola indikator kinerja individu, nilai, dan template',
            'slug' => 'performance-management',
            'icon' => 'chart-bar',
            'order' => 50, // Urutan di menu
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus modul Performance Management
        DB::table('modules')->where('code', 'KPI')->delete();
    }
};
