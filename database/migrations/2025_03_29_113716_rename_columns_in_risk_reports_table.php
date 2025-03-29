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
        Schema::table('risk_reports', function (Blueprint $table) {
            // Rename 'riskreport_number' to 'document_number'
            $table->renameColumn('riskreport_number', 'document_number');

            // Rename 'risk_title' to 'document_title'
            $table->renameColumn('risk_title', 'document_title');

            // We can't directly rename timestamp columns like 'created_at'
            // So, we'll create a new column 'document_date' and copy data from 'created_at'
            $table->timestamp('document_date')->nullable()->after('created_at');
        });

        // Copy data from created_at to document_date
        DB::statement('UPDATE risk_reports SET document_date = created_at');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            // Reverse the column renames
            $table->renameColumn('document_number', 'riskreport_number');
            $table->renameColumn('document_title', 'risk_title');

            // Drop the document_date column
            $table->dropColumn('document_date');
        });
    }
};
