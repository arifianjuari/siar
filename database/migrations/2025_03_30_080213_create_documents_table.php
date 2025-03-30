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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('document_number');
            $table->string('document_title');
            $table->timestamp('document_date')->nullable();
            $table->string('category');
            $table->text('description')->nullable();
            $table->enum('confidentiality_level', ['public', 'internal', 'confidential']);
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->foreign('uploaded_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
