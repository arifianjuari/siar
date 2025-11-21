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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('ceo_rank')->nullable()->after('ceo')->comment('Pangkat Direktur / Pimpinan Utama Rumah Sakit');
            $table->string('ceo_nrp')->nullable()->after('ceo_rank')->comment('NRP/NIK Direktur / Pimpinan Utama Rumah Sakit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['ceo_rank', 'ceo_nrp']);
        });
    }
};
