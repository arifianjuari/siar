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
        Schema::table('clinical_pathways', function (Blueprint $table) {
            // Add structured_data column for storing JSON with CP steps, unit cost, and evaluation results
            if (!Schema::hasColumn('clinical_pathways', 'structured_data')) {
                $table->json('structured_data')->nullable()->after('procedure_name');
            }

            // Need to check if status has the right values but Laravel doesn't provide a direct way
            // Instead we'll use DB facade to alter the column if it exists
            if (Schema::hasColumn('clinical_pathways', 'status')) {
                // Use a raw statement to modify the enum
                DB::statement("ALTER TABLE clinical_pathways MODIFY COLUMN status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft'");
            } else {
                // If it doesn't exist, add it
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('procedure_name');
            }

            // Drop columns that don't match the required structure
            if (Schema::hasColumn('clinical_pathways', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('clinical_pathways', 'start_date')) {
                $table->dropColumn('start_date');
            }

            if (Schema::hasColumn('clinical_pathways', 'is_active')) {
                $table->dropColumn('is_active');
            }

            // Ensure required fields exist
            if (!Schema::hasColumn('clinical_pathways', 'code')) {
                $table->string('code')->unique()->after('name');
            }

            if (!Schema::hasColumn('clinical_pathways', 'effective_date')) {
                $table->date('effective_date')->after('status');
            }

            if (!Schema::hasColumn('clinical_pathways', 'expiry_date')) {
                $table->date('expiry_date')->after('effective_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_pathways', function (Blueprint $table) {
            // Remove the structured_data column
            $table->dropColumn('structured_data');

            // Do not revert the other structure changes to maintain compatibility
        });
    }
};
