<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi terkonsolidasi untuk modul manajemen kinerja.
     */
    public function up(): void
    {
        // Tabel Performance_Indicators (Indikator Kinerja)
        Schema::create('performance_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('measurement_unit');
            $table->enum('indicator_type', ['KPI', 'OKR', 'BSC', 'MBO', 'Other'])->default('KPI');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Performance_Templates (Template Penilaian Kinerja)
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

            // Indeks untuk optimasi query
            $table->index(['role_id', 'indicator_id']);
        });

        // Tabel Performance_Scores (Nilai Kinerja)
        Schema::create('performance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained('performance_indicators')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('performance_templates')->nullOnDelete();
            $table->float('target_value');
            $table->float('actual_value')->nullable();
            $table->float('achievement_percentage')->nullable();
            $table->float('score')->nullable();
            $table->text('notes')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_label')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('draft');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indeks untuk optimasi query
            $table->index(['user_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_scores');
        Schema::dropIfExists('performance_templates');
        Schema::dropIfExists('performance_indicators');
    }
};
