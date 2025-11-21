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
        Schema::create('performance_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('name');
            $table->enum('measurement_type', ['positive', 'negative', 'custom']);
            $table->text('custom_formula')->nullable();
            $table->string('unit')->nullable();
            $table->enum('category', ['financial', 'customer', 'process', 'learning']);
            $table->boolean('is_shared')->default(false);
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_indicators');
    }
};
