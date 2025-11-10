<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cp_evaluation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cp_evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('cp_step_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');
            $table->date('completion_date')->nullable();
            $table->boolean('is_compliant')->default(true);
            $table->text('non_compliance_reason')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_evaluation_steps');
    }
};
