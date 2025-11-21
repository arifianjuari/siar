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
        // Drop tabel terlebih dahulu dan buat ulang
        Schema::dropIfExists('performance_templates');

        Schema::create('performance_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('indicator_id')->nullable()->constrained('performance_indicators')->nullOnDelete();
            $table->float('weight');
            $table->float('default_target_value')->nullable();
            $table->integer('position')->nullable();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Tambahkan indeks untuk optimasi query
            $table->index(['role_id', 'indicator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_templates');
    }
};
