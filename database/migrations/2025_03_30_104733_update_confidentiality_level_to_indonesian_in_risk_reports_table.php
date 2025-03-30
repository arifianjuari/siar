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
        // Mengubah data yang sudah ada ke bahasa Indonesia
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'Publik' WHERE confidentiality_level = 'public'");
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'Internal' WHERE confidentiality_level = 'internal'");
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'Rahasia' WHERE confidentiality_level = 'confidential'");

        // Mengubah tipe kolom enum ke bahasa Indonesia
        DB::statement("ALTER TABLE risk_reports MODIFY COLUMN confidentiality_level ENUM('Publik', 'Internal', 'Rahasia') DEFAULT 'Internal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengubah data kembali ke bahasa Inggris
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'public' WHERE confidentiality_level = 'Publik'");
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'internal' WHERE confidentiality_level = 'Internal'");
        DB::statement("UPDATE risk_reports SET confidentiality_level = 'confidential' WHERE confidentiality_level = 'Rahasia'");

        // Mengubah tipe kolom enum kembali ke bahasa Inggris
        DB::statement("ALTER TABLE risk_reports MODIFY COLUMN confidentiality_level ENUM('public', 'internal', 'confidential') DEFAULT 'internal'");
    }
};
