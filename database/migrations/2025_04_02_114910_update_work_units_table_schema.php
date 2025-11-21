<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_units', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->enum('unit_type', ['medical', 'non-medical', 'supporting'])->after('description')->nullable();
            $table->foreignId('head_of_unit_id')->nullable()->after('unit_type')->constrained('users')->nullOnDelete();

            // Rename kolom
            $table->renameColumn('name', 'unit_name');
            $table->renameColumn('code', 'unit_code');

            // Pastikan kolom-kolom yang sudah ada tetap ada
            // Catatan: is_active, parent_id, dan order tetap dipertahankan karena 
            // mungkin digunakan di aplikasi yang sudah ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_units', function (Blueprint $table) {
            // Hapus kolom baru
            $table->dropForeign(['head_of_unit_id']);
            $table->dropColumn(['unit_type', 'head_of_unit_id']);

            // Kembalikan nama kolom
            $table->renameColumn('unit_name', 'name');
            $table->renameColumn('unit_code', 'code');
        });
    }
};
