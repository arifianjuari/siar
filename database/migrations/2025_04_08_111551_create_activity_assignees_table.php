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
        Schema::create('activity_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->enum('assignee_type', ['user', 'work_unit']);
            $table->unsignedBigInteger('assignee_id');
            $table->string('role')->nullable(); // RACI: responsible, accountable, consulted, informed
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();

            // Indeks untuk meningkatkan performa query
            $table->index(['assignee_type', 'assignee_id']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_assignees');
    }
};
