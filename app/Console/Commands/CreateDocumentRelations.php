<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\RiskReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDocumentRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:create-relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat relasi antara dokumen dan risk report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai membuat relasi dokumen...');

        try {
            // Ambil semua dokumen
            $documents = Document::all();
            $this->info('Total dokumen: ' . $documents->count());

            // Ambil semua risk reports
            $riskReports = RiskReport::all();
            $this->info('Total risk reports: ' . $riskReports->count());

            // Jika ada dokumen dan risk report
            if ($documents->count() > 0 && $riskReports->count() > 0) {
                // Ambil risk report pertama
                $firstRiskReport = $riskReports->first();
                $this->info('Menggunakan risk report ID: ' . $firstRiskReport->id);

                // Buat relasi untuk semua dokumen dengan risk report pertama
                $created = 0;
                foreach ($documents as $doc) {
                    try {
                        // Cek apakah relasi sudah ada
                        $existingRelation = DB::table('documentables')
                            ->where('document_id', $doc->id)
                            ->where('documentable_id', $firstRiskReport->id)
                            ->where('documentable_type', 'App\\Models\\RiskReport')
                            ->exists();

                        if (!$existingRelation) {
                            // Buat relasi baru
                            DB::table('documentables')->insert([
                                'document_id' => $doc->id,
                                'documentable_id' => $firstRiskReport->id,
                                'documentable_type' => 'App\\Models\\RiskReport',
                                'relation_type' => 'related',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);

                            $created++;
                            $this->info('Membuat relasi untuk dokumen ID: ' . $doc->id);
                        } else {
                            $this->info('Relasi sudah ada untuk dokumen ID: ' . $doc->id . ', dilewati.');
                        }
                    } catch (\Exception $e) {
                        $this->error('Error untuk dokumen ID ' . $doc->id . ': ' . $e->getMessage());
                        Log::error('Error saat membuat relasi dokumen', [
                            'document_id' => $doc->id,
                            'risk_report_id' => $firstRiskReport->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                $this->info('Total relasi dibuat: ' . $created);
            } else {
                $this->warn('Tidak ada dokumen atau risk report yang tersedia.');
            }

            // Periksa jumlah relasi yang dibuat
            $countRelations = DB::table('documentables')->count();
            $this->info('Total relasi di tabel documentables: ' . $countRelations);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Error saat menjalankan CreateDocumentRelations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}
