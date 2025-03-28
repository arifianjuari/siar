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
        // Ubah nilai status yang ada ke nilai baru
        DB::table('risk_reports')
            ->where('status', 'open')
            ->update(['status' => 'Draft']);

        DB::table('risk_reports')
            ->where('status', 'in_review')
            ->update(['status' => 'Ditinjau']);

        DB::table('risk_reports')
            ->where('status', 'resolved')
            ->update(['status' => 'Selesai']);

        // Ubah tipe enum kolom status
        DB::statement("ALTER TABLE risk_reports MODIFY COLUMN status ENUM('Draft', 'Ditinjau', 'Selesai') DEFAULT 'Draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ubah nilai status kembali ke nilai lama
        DB::table('risk_reports')
            ->where('status', 'Draft')
            ->update(['status' => 'open']);

        DB::table('risk_reports')
            ->where('status', 'Ditinjau')
            ->update(['status' => 'in_review']);

        DB::table('risk_reports')
            ->where('status', 'Selesai')
            ->update(['status' => 'resolved']);

        // Kembalikan tipe enum kolom status
        DB::statement("ALTER TABLE risk_reports MODIFY COLUMN status ENUM('open', 'in_review', 'resolved') DEFAULT 'open'");
    }
};
