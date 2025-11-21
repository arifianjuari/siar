<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cp_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_pathway_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('day')->unsigned();
            $table->integer('order')->unsigned();
            $table->enum('category', ['assessment', 'laboratory', 'radiology', 'medication', 'procedure', 'consultation', 'nutrition', 'education', 'other'])->default('other');
            $table->json('criteria')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_steps');
    }
};
