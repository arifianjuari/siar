<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel sudah ada (dibuat oleh migration 2025_05_01_000005_create_cp_evaluation_steps_table.php)
        if (Schema::hasTable('cp_evaluation_steps')) {
            return;
        }

        Schema::create('cp_evaluation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cp_evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('cp_step_id')->constrained()->onDelete('restrict');
            $table->boolean('is_done')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_evaluation_steps');
    }
};
