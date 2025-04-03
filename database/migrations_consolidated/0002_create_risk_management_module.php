<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi terkonsolidasi untuk modul manajemen risiko.
     */
    public function up(): void
    {
        // Tabel Risk_Reports (Laporan Risiko)
        Schema::create('risk_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index()->constrained('tenants')->onDelete('cascade');
            $table->string('riskreport_number')->unique();
            $table->string('risk_title');
            $table->text('chronology');
            $table->text('description')->nullable();
            $table->text('immediate_action')->nullable();
            $table->foreignId('work_unit_id')->nullable()->constrained('work_units')->nullOnDelete();
            $table->enum('risk_type', ['KTD', 'KNC', 'KTC', 'KPC', 'Sentinel'])->nullable();
            $table->string('risk_category');
            $table->date('occurred_at');
            $table->string('impact');
            $table->string('probability');
            $table->string('risk_level');
            $table->enum('status', ['open', 'in_review', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('recommendation')->nullable();
            $table->enum('confidentiality_level', ['Internal', 'Publik', 'Rahasia'])->default('Internal');

            // Kolom terkait persetujuan
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('approval_notes')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Tabel Risk_Analysis (Analisis Risiko)
        Schema::create('risk_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_report_id')->constrained()->onDelete('cascade');
            $table->text('root_cause_analysis');
            $table->text('proposed_solution');
            $table->date('target_completion_date')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_analysis');
        Schema::dropIfExists('risk_reports');
    }
};
