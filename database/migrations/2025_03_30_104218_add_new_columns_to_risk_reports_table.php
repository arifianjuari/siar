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
            $table->enum('document_type', ['Regulasi', 'Bukti'])->nullable()->after('document_title');
            $table->string('document_version')->nullable()->after('document_type');
            $table->enum('confidentiality_level', ['public', 'internal', 'confidential'])->default('internal')->after('document_version');
            $table->string('file_path')->nullable()->after('confidentiality_level');
            $table->timestamp('next_review')->nullable()->after('approved_at');
            $table->integer('review_cycle_months')->nullable()->after('next_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            $table->dropColumn([
                'document_type',
                'document_version',
                'confidentiality_level',
                'file_path',
                'next_review',
                'review_cycle_months'
            ]);
        });
    }
};
