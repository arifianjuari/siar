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
        Schema::create('tenant_module_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('module')->comment('Kode modul (contoh: risk-management)');
            $table->string('feature')->comment('Nama fitur dalam modul (contoh: risk_analysis)');
            $table->string('config_key')->nullable()->comment('Kunci konfigurasi tambahan (opsional)');
            $table->json('config_value')->nullable()->comment('Nilai konfigurasi dalam format JSON');
            $table->json('allowed_roles')->nullable()->comment('Daftar ID role yang diizinkan mengakses fitur');
            $table->timestamps();

            // Indeks untuk pencarian
            $table->index(['tenant_id', 'module', 'feature']);

            // Uniqueness untuk mencegah duplikasi
            $table->unique(['tenant_id', 'module', 'feature', 'config_key'], 'tenant_module_config_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_module_configs');
    }
};
