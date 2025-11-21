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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom supervisor_id setelah work_unit_id
            $table->foreignId('supervisor_id')->after('work_unit_id')->nullable()
                ->constrained('users')->nullOnDelete();

            // Tambahkan kolom employment_status setelah supervisor_id
            $table->enum('employment_status', ['aktif', 'resign', 'cuti', 'magang'])
                ->after('supervisor_id')->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['supervisor_id', 'employment_status']);
        });
    }
};
