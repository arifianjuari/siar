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
        Schema::table('actionable_items', function (Blueprint $table) {
            // Make polymorphic fields nullable since not all actionable items need them
            $table->string('actionable_type')->nullable()->change();
            $table->unsignedBigInteger('actionable_id')->nullable()->change();
            $table->string('action_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actionable_items', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->string('actionable_type')->nullable(false)->change();
            $table->unsignedBigInteger('actionable_id')->nullable(false)->change();
            $table->string('action_type')->nullable(false)->change();
        });
    }
};
