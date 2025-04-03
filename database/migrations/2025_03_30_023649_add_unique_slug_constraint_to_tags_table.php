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
        // Dinonaktifkan karena indeks unik sudah ada
        // Schema::table('tags', function (Blueprint $table) {
        //     $table->unique(['tenant_id', 'slug']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dinonaktifkan
        // Schema::table('tags', function (Blueprint $table) {
        //     $table->dropUnique(['tenant_id', 'slug']);
        // });
    }
};
