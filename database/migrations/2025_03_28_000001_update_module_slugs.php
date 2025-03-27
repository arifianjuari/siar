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
        // Memperbaiki slug untuk modul-modul standar
        $standardModules = [
            'user-management' => 'user-management',
            'product-management' => 'product-management',
            'risk-management' => 'risk-management',
        ];

        foreach ($standardModules as $code => $slug) {
            DB::table('modules')
                ->where('code', $code)
                ->orWhere('code', 'LIKE', "%{$code}%")
                ->update(['slug' => $slug]);
        }

        // Pastikan semua modul lain memiliki slug
        DB::table('modules')
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->get()
            ->each(function ($module) {
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
        // Tidak perlu melakukan rollback karena ini hanya memperbaiki data
    }
};
