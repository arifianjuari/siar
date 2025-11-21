<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cp_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_pathway_id')->constrained()->onDelete('cascade');
            $table->string('patient_name');
            $table->string('medical_record_number');
            $table->string('patient_id')->nullable();
            $table->date('admission_date');
            $table->date('discharge_date')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->enum('insurance_type', ['bpjs', 'umum', 'asuransi'])->default('umum');
            $table->string('insurance_number')->nullable();
            $table->text('diagnosis')->nullable();
            $table->string('doctor_name')->nullable();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_evaluations');
    }
};
