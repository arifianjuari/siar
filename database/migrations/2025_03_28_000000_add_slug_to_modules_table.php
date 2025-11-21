<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('code');
        });

        // Mengisi nilai slug berdasarkan code untuk data yang sudah ada
        DB::table('modules')->get()->each(function ($module) {
            DB::table('modules')
                ->where('id', $module->id)
                ->update(['slug' => Str::slug($module->code)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
