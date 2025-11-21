<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel sudah ada (dibuat oleh migration 2025_05_01_000006_create_cp_evaluation_additional_steps_table.php)
        if (Schema::hasTable('cp_evaluation_additional_steps')) {
            return;
        }

        Schema::create('cp_evaluation_additional_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cp_evaluation_id')->constrained()->onDelete('cascade');
            $table->string('additional_step_name');
            $table->decimal('additional_step_cost', 12, 2);
            $table->enum('justification_status', ['Justified', 'Tidak Justified']);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_evaluation_additional_steps');
    }
};
