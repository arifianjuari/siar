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
        Schema::create('kendali_mutu', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dokumen');
            $table->date('tanggal');
            $table->string('jenis_dokumen');
            $table->string('status');
            $table->text('deskripsi')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendali_mutu');
    }
};
