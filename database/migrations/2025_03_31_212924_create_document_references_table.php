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
        Schema::create('document_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('reference_type', [
                'UU',
                'Peraturan Pemerintah',
                'Permenkes',
                'Surat Edaran',
                'SE Internal',
                'Pedoman',
                'SOP',
                'Surat Keputusan',
                'Dokumen Lainnya'
            ]);
            $table->string('reference_number');
            $table->text('title');
            $table->string('issued_by');
            $table->date('issued_date');
            $table->string('related_unit')->nullable();
            $table->text('file_url')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_references');
    }
};
