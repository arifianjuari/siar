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
        // Tambahkan modul manajemen risiko ke tabel modules
        DB::table('modules')->insert([
            'name' => 'Manajemen Risiko',
            'code' => 'risk-management',
            'description' => 'Modul untuk pelaporan dan pemantauan risiko di seluruh unit rumah sakit',
            'icon' => 'fa-triangle-exclamation',
            'order' => 10, // Sesuaikan dengan urutan yang diinginkan
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus modul manajemen risiko dari tabel modules
        DB::table('modules')->where('code', 'risk-management')->delete();
    }
};
