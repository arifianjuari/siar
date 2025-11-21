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
        Schema::create('document_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->unsignedBigInteger('document_id');
            $table->string('document_type');
            $table->timestamps();

            // Unique untuk menghindari duplikasi
            $table->unique(['tag_id', 'document_id', 'document_type'], 'document_tag_unique');

            // Index untuk mempercepat query
            $table->index(['document_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_tag');
    }
};
