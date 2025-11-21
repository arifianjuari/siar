<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Module;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Module::create([
            'name' => 'Manajemen Dokumen',
            'code' => 'DOC_MGMT',
            'slug' => 'document-management',
            'icon' => 'fa-file-alt',
            'description' => 'Modul untuk mengelola dokumen dari berbagai sumber'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('slug', 'document-management')->delete();
    }
};
