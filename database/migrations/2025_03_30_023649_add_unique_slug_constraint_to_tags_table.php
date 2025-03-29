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
        Schema::table('tags', function (Blueprint $table) {
            // Tambahkan unique constraint untuk tenant_id + slug
            $table->unique(['tenant_id', 'slug'], 'tags_tenant_id_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            // Hapus unique constraint
            $table->dropUnique('tags_tenant_id_slug_unique');
        });
    }
};
