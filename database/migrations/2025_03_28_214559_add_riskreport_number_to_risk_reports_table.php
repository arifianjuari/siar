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
        Schema::table('risk_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('risk_reports', 'riskreport_number')) {
                $table->string('riskreport_number', 20)->after('id')->nullable(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            if (Schema::hasColumn('risk_reports', 'riskreport_number')) {
                $table->dropColumn('riskreport_number');
            }
        });
    }
};
