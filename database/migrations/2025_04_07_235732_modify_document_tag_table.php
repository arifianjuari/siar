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
        // Karena kita mengalami masalah dengan indeks yang digunakan oleh foreign key,
        // kita akan melewatkan perubahan ini dan langsung mengubah tabel SPO saja
        // Dalam kasus ini, kita akan tetap menggunakan BIGINT untuk document_id

        // Tidak ada tindakan yang dilakukan pada document_tag
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada tindakan yang perlu di-rollback
    }
};
