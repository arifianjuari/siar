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
        Schema::table('tenant_modules', function (Blueprint $table) {
            $table->timestamp('requested_at')->nullable()->after('is_active');
            $table->unsignedBigInteger('requested_by')->nullable()->after('requested_at');
            $table->timestamp('approved_at')->nullable()->after('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');

            // Tambahkan foreign key untuk requested_by dan approved_by
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_modules', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn('requested_at');
            $table->dropColumn('requested_by');
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
        });
    }
};
