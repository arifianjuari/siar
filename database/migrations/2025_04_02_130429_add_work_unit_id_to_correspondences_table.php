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
        Schema::table('correspondences', function (Blueprint $table) {
            // Tambahkan kolom work_unit_id setelah tenant_id
            $table->foreignId('work_unit_id')->nullable()->after('tenant_id')
                ->constrained('work_units')->nullOnDelete();

            // Tambahkan indeks untuk mempercepat query
            $table->index('work_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('correspondences', function (Blueprint $table) {
            // Hapus foreign key constraint dan kolom
            $table->dropForeign(['work_unit_id']);
            $table->dropIndex(['work_unit_id']);
            $table->dropColumn('work_unit_id');
        });
    }
};
