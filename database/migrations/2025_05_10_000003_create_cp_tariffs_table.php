<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel sudah ada (dibuat oleh migration 2025_05_01_000003_create_cp_tariffs_table.php)
        if (Schema::hasTable('cp_tariffs')) {
            return;
        }

        Schema::create('cp_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_pathway_id')->constrained()->onDelete('cascade');
            $table->string('code_ina_cbg');
            $table->string('description');
            $table->decimal('claim_value', 12, 2);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_tariffs');
    }
};
