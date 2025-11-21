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
        // Karena kita sudah mengubah enum di migrasi sebelumnya, 
        // kita hanya perlu memastikan nilai default correct
        // dan menangani kemungkinan data yang sudah ada
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'internal' WHERE confidentiality_level IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu melakukan apa-apa karena perubahan ini hanya memperbarui data
    }
};
