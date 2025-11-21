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
        Schema::create('risk_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_report_id')->constrained('risk_reports')->onDelete('cascade');
            $table->text('direct_cause')->nullable();
            $table->text('root_cause')->nullable();
            $table->json('contributor_factors')->nullable();
            $table->text('recommendation_short')->nullable();
            $table->text('recommendation_medium')->nullable();
            $table->text('recommendation_long')->nullable();
            $table->foreignId('analyzed_by')->nullable()->constrained('users');
            $table->timestamp('analyzed_at')->nullable();
            $table->enum('analysis_status', ['draft', 'in_progress', 'completed', 'reviewed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_analysis');
    }
};
