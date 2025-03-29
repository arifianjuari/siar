<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already exists
        $columns = DB::select('SHOW COLUMNS FROM tags WHERE Field = ?', ['slug']);

        if (empty($columns)) {
            Schema::table('tags', function (Blueprint $table) {
                $table->string('slug')->after('name');

                // Tambahkan unique constraint baru untuk slug
                $table->unique(['tenant_id', 'slug']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            // Drop unique constraint untuk slug
            $table->dropUnique(['tenant_id', 'slug']);

            // Just in case, check if column exists before dropping
            $columns = DB::select('SHOW COLUMNS FROM tags WHERE Field = ?', ['slug']);
            if (!empty($columns)) {
                $table->dropColumn('slug');
            }
        });
    }
};
