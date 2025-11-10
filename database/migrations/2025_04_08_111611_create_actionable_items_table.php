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
        Schema::create('actionable_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->string('actionable_type'); // Nama model: 'RiskAnalysis', 'Document', 'SPO', dll.
            $table->unsignedBigInteger('actionable_id'); // ID dari model tersebut
            $table->string('action_type'); // Jenis tindakan: 'recommendation_short', 'review', 'implementation', dll.
            $table->string('reference')->nullable(); // Referensi tambahan
            $table->boolean('is_mandatory')->default(false);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indeks untuk meningkatkan performa query
            $table->index(['actionable_type', 'actionable_id']);
            $table->index('action_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actionable_items');
    }
};
