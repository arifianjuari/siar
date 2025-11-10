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
        // Mengubah kolom id menjadi BIGINT UNSIGNED dengan AUTO_INCREMENT
        Schema::table('spos', function (Blueprint $table) {
            // Hapus primary key constraint terlebih dahulu
            $table->dropPrimary('id');
        });

        // Gunakan raw SQL untuk mengubah tipe kolom dan menambahkan AUTO_INCREMENT
        DB::statement('ALTER TABLE spos CHANGE id id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Gunakan raw SQL untuk mengubah kembali kolom id menjadi CHAR(36)
        DB::statement('ALTER TABLE spos CHANGE id id CHAR(36) NOT NULL');

        Schema::table('spos', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
