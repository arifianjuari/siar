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
        if (!Schema::hasTable('work_units')) {
            Schema::create('work_units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->string('unit_code')->unique();
                $table->string('unit_name');
                $table->enum('unit_type', ['medical', 'non-medical', 'supporting']);
                $table->foreignId('head_of_unit_id')->constrained('users')->onDelete('restrict');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_units');
    }
};
