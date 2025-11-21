<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'planned',
                'pending',  // Nilai baru
                'ongoing',
                'completed',
                'cancelled'
            ])->default('draft')->change();
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'planned',
                'ongoing',
                'completed',
                'cancelled'
            ])->default('draft')->change();
        });
    }
};
