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
        Schema::create('risk_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index()->constrained('tenants')->onDelete('cascade');
            $table->string('risk_title');
            $table->text('chronology');
            $table->string('reporter_unit');
            $table->enum('risk_type', ['KTD', 'KNC', 'KTC', 'KPC', 'Sentinel'])->nullable();
            $table->string('risk_category');
            $table->date('occurred_at');
            $table->string('impact');
            $table->string('probability');
            $table->string('risk_level');
            $table->enum('status', ['open', 'in_review', 'resolved'])->default('open');
            $table->text('recommendation')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_reports');
    }
};
