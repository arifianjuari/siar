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
        // Drop tabel terlebih dahulu jika sudah ada
        Schema::dropIfExists('performance_scores');

        Schema::create('performance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('period'); // Format YYYY-MM
            $table->foreignId('indicator_id')->nullable()->constrained('performance_indicators')->nullOnDelete();
            $table->float('target_value');
            $table->float('actual_value');
            $table->float('weight');
            $table->float('score');
            $table->enum('grade', ['A+', 'A', 'B', 'C', 'D']);
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Tambahkan indeks untuk optimasi query
            $table->index(['user_id', 'period']);
            $table->index(['period', 'tenant_id']);
            $table->index(['indicator_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_scores');
    }
};
