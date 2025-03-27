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
            $table->text('description')->nullable()->after('name');
            $table->text('address')->nullable()->after('description');
            $table->string('phone', 20)->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('logo')->nullable()->after('email');
            $table->json('settings')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'address',
                'phone',
                'email',
                'logo',
                'settings'
            ]);
        });
    }
};
