<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_pathways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('diagnosis_code')->nullable();
            $table->string('diagnosis_name')->nullable();
            $table->string('procedure_code')->nullable();
            $table->string('procedure_name')->nullable();
            // Status enum dengan nilai yang sesuai dengan migration 2025_04_30
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            // Kolom structured_data untuk menyimpan JSON dengan CP steps, unit cost, dan evaluation results
            $table->json('structured_data')->nullable()->after('procedure_name');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_pathways');
    }
};
