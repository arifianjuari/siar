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
        // Kita akan menghapus dan membuat ulang kolom reference_type

        // 1. Buat kolom temporary
        Schema::table('document_references', function (Blueprint $table) {
            $table->string('reference_type_temp')->nullable()->after('reference_type');
        });

        // 2. Pindahkan data dari kolom lama ke kolom sementara dengan konversi nilai
        $records = DB::table('document_references')->get();
        foreach ($records as $record) {
            $newValue = $this->convertOldToNew($record->reference_type);
            DB::table('document_references')
                ->where('id', $record->id)
                ->update(['reference_type_temp' => $newValue]);
        }

        // 3. Hapus kolom lama
        Schema::table('document_references', function (Blueprint $table) {
            $table->dropColumn('reference_type');
        });

        // 4. Buat kolom baru dengan nama asli dan kontraint enum yang baru
        Schema::table('document_references', function (Blueprint $table) {
            $table->enum('reference_type', [
                'Peraturan Perundangan',
                'Peraturan Kapolri',
                'Surat Keputusan',
                'Surat Eksternal',
                'Surat Internal',
                'Pedoman',
                'SOP',
                'Dokumen Lainnya'
            ])->after('reference_type_temp');
        });

        // 5. Pindahkan data dari kolom sementara ke kolom baru
        $records = DB::table('document_references')->get();
        foreach ($records as $record) {
            if (!empty($record->reference_type_temp)) {
                DB::table('document_references')
                    ->where('id', $record->id)
                    ->update(['reference_type' => $record->reference_type_temp]);
            }
        }

        // 6. Hapus kolom sementara
        Schema::table('document_references', function (Blueprint $table) {
            $table->dropColumn('reference_type_temp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Gunakan pendekatan yang sama untuk rollback

        // 1. Buat kolom temporary
        Schema::table('document_references', function (Blueprint $table) {
            $table->string('reference_type_temp')->nullable()->after('reference_type');
        });

        // 2. Pindahkan data dari kolom yang ada ke kolom sementara dengan konversi nilai
        $records = DB::table('document_references')->get();
        foreach ($records as $record) {
            $oldValue = $this->convertNewToOld($record->reference_type);
            DB::table('document_references')
                ->where('id', $record->id)
                ->update(['reference_type_temp' => $oldValue]);
        }

        // 3. Hapus kolom yang ada
        Schema::table('document_references', function (Blueprint $table) {
            $table->dropColumn('reference_type');
        });

        // 4. Buat kolom baru dengan nama asli dan kontraint enum yang lama
        Schema::table('document_references', function (Blueprint $table) {
            $table->enum('reference_type', [
                'UU',
                'Peraturan Pemerintah',
                'Permenkes',
                'Surat Edaran',
                'SE Internal',
                'Pedoman',
                'SOP',
                'Surat Keputusan',
                'Dokumen Lainnya'
            ])->after('reference_type_temp');
        });

        // 5. Pindahkan data dari kolom sementara ke kolom asli
        $records = DB::table('document_references')->get();
        foreach ($records as $record) {
            if (!empty($record->reference_type_temp)) {
                DB::table('document_references')
                    ->where('id', $record->id)
                    ->update(['reference_type' => $record->reference_type_temp]);
            }
        }

        // 6. Hapus kolom sementara
        Schema::table('document_references', function (Blueprint $table) {
            $table->dropColumn('reference_type_temp');
        });
    }

    /**
     * Memetakan nilai enum lama ke nilai enum baru
     */
    private function convertOldToNew($oldValue)
    {
        $mapping = [
            'UU' => 'Peraturan Perundangan',
            'Peraturan Pemerintah' => 'Peraturan Perundangan',
            'Permenkes' => 'Peraturan Perundangan',
            'Surat Edaran' => 'Surat Eksternal',
            'SE Internal' => 'Surat Internal',
            'Pedoman' => 'Pedoman',
            'SOP' => 'SOP',
            'Surat Keputusan' => 'Surat Keputusan',
            'Dokumen Lainnya' => 'Dokumen Lainnya'
        ];

        return $mapping[$oldValue] ?? 'Dokumen Lainnya';
    }

    /**
     * Memetakan nilai enum baru ke nilai enum lama untuk rollback
     */
    private function convertNewToOld($newValue)
    {
        $mapping = [
            'Peraturan Perundangan' => 'UU',
            'Peraturan Kapolri' => 'Peraturan Pemerintah',
            'Surat Keputusan' => 'Surat Keputusan',
            'Surat Eksternal' => 'Surat Edaran',
            'Surat Internal' => 'SE Internal',
            'Pedoman' => 'Pedoman',
            'SOP' => 'SOP',
            'Dokumen Lainnya' => 'Dokumen Lainnya'
        ];

        return $mapping[$newValue] ?? 'Dokumen Lainnya';
    }
};
