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
            $table->date('evaluation_date');
            $table->foreignId('evaluator_user_id')->constrained('users')->onDelete('restrict');
            $table->decimal('compliance_percentage', 5, 2);
            $table->decimal('total_additional_cost', 12, 2)->default(0);
            $table->enum('evaluation_status', ['Hijau', 'Kuning', 'Merah']);
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
