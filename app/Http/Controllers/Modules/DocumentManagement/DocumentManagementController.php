<?php

namespace App\Http\Controllers\Modules\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\RiskReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentManagementController extends Controller
{
    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('module:document-management');
    }

    /**
     * Menampilkan dashboard modul manajemen dokumen
     */
    public function dashboard()
    {
        // Default tenant ID jika tidak ada dalam sesi
        $defaultTenantId = 2;

        // Coba dapatkan tenant_id dari sesi, gunakan default jika tidak ada
        $tenantId = session('tenant_id') ?? $defaultTenantId;

        // Log tenant_id yang sedang digunakan
        Log::info('Dashboard Document Management diakses', [
            'user_id' => Auth::id() ?? 'unknown',
            'active_tenant_id' => $tenantId,
            'session_tenant_id' => session('tenant_id'),
            'default_tenant_id' => $defaultTenantId
        ]);

        // Jalankan pembaruan tenant_id otomatis
        $this->updateDocumentTenants($tenantId);

        // Validasi data dari tabel documents (gabungkan tenant 1 dan tenant aktif)
        // Karena ada kemungkinan dokumen disimpan di tenant 1 (System)
        $validTenantIds = [1, $tenantId];
        $documentCount = Document::whereIn('tenant_id', $validTenantIds)->count();

        Log::info('Jumlah dokumen ditemukan', [
            'count' => $documentCount,
            'tenant_ids' => $validTenantIds
        ]);

        // Periksa jumlah relasi di tabel documentables
        $documentsRelationCount = DB::table('documentables')->count();
        Log::info('Jumlah relasi di tabel documentables', ['count' => $documentsRelationCount]);

        // Periksa data dokumen yang tidak memiliki tenant_id
        $nullTenantCount = Document::whereNull('tenant_id')->count();
        Log::info('Dokumen tanpa tenant_id', ['count' => $nullTenantCount]);

        // Validasi data relasi dokumen dengan RiskReport
        $relatedToRisk = 0;

        if ($documentsRelationCount > 0) {
            $relatedToRisk = DB::table('documentables')
                ->join('documents', 'documentables.document_id', '=', 'documents.id')
                ->where('documentables.documentable_type', 'App\\Models\\RiskReport')
                ->whereIn('documents.tenant_id', $validTenantIds)
                ->distinct('documents.id')
                ->count('documents.id');
        }

        Log::info('Dokumen terkait risk report', [
            'count' => $relatedToRisk,
            'valid_tenant_ids' => $validTenantIds
        ]);

        // Validasi kolom category dan document_date
        $docsNoCategory = Document::whereIn('tenant_id', $validTenantIds)
            ->whereNull('category')
            ->count();

        $docsNoDate = Document::whereIn('tenant_id', $validTenantIds)
            ->whereNull('document_date')
            ->count();

        Log::info('Dokumen tanpa kategori', ['count' => $docsNoCategory]);
        Log::info('Dokumen tanpa tanggal', ['count' => $docsNoDate]);

        // Cek apakah ada sample dokumen yang terkait risk report
        $rawSql = collect();

        if ($documentsRelationCount > 0) {
            $rawSql = DB::table('documentables')
                ->join('documents', 'documentables.document_id', '=', 'documents.id')
                ->leftJoin('risk_reports', function ($join) use ($validTenantIds) {
                    $join->on('documentables.documentable_id', '=', 'risk_reports.id')
                        ->where('documentables.documentable_type', 'App\\Models\\RiskReport');
                })
                ->whereIn('documents.tenant_id', $validTenantIds)
                ->select('documents.id', 'documents.document_title', 'risk_reports.document_title as risk_title')
                ->limit(5)
                ->get();
        }

        Log::info('Sample dokumen dan risk reports terkait', [
            'data' => $rawSql,
            'count' => $rawSql->count()
        ]);

        // Statistik dasar
        $stats = [
            'total' => $documentCount,
        ];

        // Log nilai confidentiality_level di RiskReport untuk debugging
        $confidentialityLevels = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->selectRaw('confidentiality_level, count(*) as total')
            ->groupBy('confidentiality_level')
            ->get();

        Log::info('Nilai confidentiality_level di tabel risk_reports', [
            'levels' => $confidentialityLevels->toArray()
        ]);

        // Hitung dokumen berdasarkan confidentiality_level
        $publicDocuments = Document::whereIn('tenant_id', $validTenantIds)
            ->where('confidentiality_level', 'public')
            ->count();

        $internalDocuments = Document::whereIn('tenant_id', $validTenantIds)
            ->where('confidentiality_level', 'internal')
            ->count();

        $confidentialDocuments = Document::whereIn('tenant_id', $validTenantIds)
            ->where('confidentiality_level', 'confidential')
            ->count();

        // Hitung dokumen dari risk_reports (tidak perlu memeriksa file_path)
        $publicRiskDocuments = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->where('confidentiality_level', 'Publik')
            ->count();

        $internalRiskDocuments = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->where('confidentiality_level', 'Internal')
            ->count();

        $rahasiaRiskDocuments = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->where('confidentiality_level', 'Rahasia')
            ->count();

        // Total dokumen dari risk_reports
        $totalRiskDocuments = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->count();

        // Log data lebih rinci untuk debugging
        Log::info('Detail jumlah dokumen berdasarkan confidentiality_level', [
            'documents_public' => $publicDocuments,
            'documents_internal' => $internalDocuments,
            'documents_confidential' => $confidentialDocuments,
            'risk_reports_publik' => $publicRiskDocuments,
            'risk_reports_internal' => $internalRiskDocuments,
            'risk_reports_rahasia' => $rahasiaRiskDocuments,
            'total_risk_documents' => $totalRiskDocuments
        ]);

        // Gabungkan data ke stats
        $stats['public'] = $publicDocuments + $publicRiskDocuments;
        $stats['internal'] = $internalDocuments + $internalRiskDocuments;
        $stats['confidential'] = $confidentialDocuments + $rahasiaRiskDocuments;
        $stats['risk_management'] = $relatedToRisk + $totalRiskDocuments;

        // Update total
        $stats['total'] = $documentCount + $totalRiskDocuments;

        // Log untuk debugging
        Log::info('Statistik dashboard dokumen', [
            'stats' => $stats,
            'valid_tenant_ids' => $validTenantIds
        ]);

        // Dokumen terbaru (berdasarkan document_date)
        $latestDocuments = Document::whereIn('tenant_id', $validTenantIds)
            ->orderByDesc('document_date')
            ->limit(5)
            ->get();

        // Tambahkan dokumen dari risk_reports yang memiliki file
        $latestRiskDocuments = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->orderByDesc('created_at')
            ->limit(5)
            ->select(
                'id',
                'document_title',
                'document_number',
                'file_path',
                'created_at',
                'document_date',
                'document_type',
                'confidentiality_level'
            )
            ->get();

        // Gabungkan dokumen dari kedua sumber
        if ($latestRiskDocuments->count() > 0) {
            $combinedLatestDocuments = $latestDocuments->concat($latestRiskDocuments)
                ->sortByDesc('document_date')
                ->take(5);
            $latestDocuments = $combinedLatestDocuments;
        }

        // Kategori dokumen
        $categories = Document::whereIn('tenant_id', $validTenantIds)
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Tambahkan pengelompokan berdasarkan modul (dokumen berdasarkan sumbernya)
        $moduleCategories = [];

        // 1. Dokumen dari modul Manajemen Dokumen
        $documentCount = Document::whereIn('tenant_id', $validTenantIds)
            ->whereNotNull('document_number')
            ->count();

        if ($documentCount > 0) {
            $moduleCategories[] = [
                'module' => 'document-management',
                'display_name' => 'Manajemen Dokumen',
                'icon' => 'fa-file-alt',
                'total' => $documentCount,
                'color' => 'primary'
            ];
        }

        // 2. Dokumen dari modul Manajemen Risiko
        $riskReportCount = RiskReport::whereIn('tenant_id', $validTenantIds)
            ->whereNotNull('document_number')
            ->count();

        if ($riskReportCount > 0) {
            $moduleCategories[] = [
                'module' => 'risk-management',
                'display_name' => 'Manajemen Risiko',
                'icon' => 'fa-exclamation-triangle',
                'total' => $riskReportCount,
                'color' => 'danger'
            ];
        }

        // 3. Tambahkan modul lain jika ada
        // ...

        // Urutkan berdasarkan jumlah terbanyak
        usort($moduleCategories, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        Log::info('Module categories', [
            'data' => $moduleCategories
        ]);

        // Ambil tag populer untuk ditampilkan di dashboard
        $popularTags = DB::table('document_tag')
            ->join('tags', 'document_tag.tag_id', '=', 'tags.id')
            ->whereIn('tags.tenant_id', $validTenantIds)
            ->select('tags.id', 'tags.name', 'tags.slug')
            ->selectRaw('COUNT(document_tag.document_id) as document_count')
            ->groupBy('tags.id', 'tags.name', 'tags.slug')
            ->orderByDesc('document_count')
            ->limit(10)
            ->get();

        Log::info('Popular tags', [
            'tags' => $popularTags->toArray()
        ]);

        return view('modules.DocumentManagement.dashboard', compact(
            'stats',
            'latestDocuments',
            'categories',
            'moduleCategories',
            'popularTags'
        ));
    }

    /**
     * Memperbarui tenant_id pada dokumen yang kosong
     * 
     * @param int $tenantId
     * @return void
     */
    private function updateDocumentTenants($tenantId = 2)
    {
        try {
            // 1. Update semua dokumen yang belum memiliki tenant_id
            $updatedCount = Document::whereNull('tenant_id')->update(['tenant_id' => $tenantId]);

            Log::info('Dokumen dengan tenant_id null diperbarui', ['count' => $updatedCount]);

            // 2. Update dokumen berdasarkan uploader jika ada
            $documentsWithUploader = Document::whereNull('tenant_id')
                ->whereNotNull('uploaded_by')
                ->get();

            $updatedUploaderCount = 0;

            foreach ($documentsWithUploader as $doc) {
                $uploader = User::find($doc->uploaded_by);
                if ($uploader && $uploader->tenant_id) {
                    $doc->tenant_id = $uploader->tenant_id;
                    $doc->save();
                    $updatedUploaderCount++;
                }
            }

            Log::info('Dokumen diperbarui berdasarkan uploader', ['count' => $updatedUploaderCount]);

            // 3. Update dokumen yang terkait dengan Risk Report melalui documentables
            $documentIds = DB::table('documentables')
                ->join('documents', 'documentables.document_id', '=', 'documents.id')
                ->join('risk_reports', 'documentables.documentable_id', '=', 'risk_reports.id')
                ->where('documentables.documentable_type', 'App\\Models\\RiskReport')
                ->whereNull('documents.tenant_id')
                ->where('risk_reports.tenant_id', $tenantId)
                ->pluck('documents.id')
                ->toArray();

            $updatedRelatedCount = 0;

            if (!empty($documentIds)) {
                $updatedRelatedCount = Document::whereIn('id', $documentIds)
                    ->update(['tenant_id' => $tenantId]);
            }

            Log::info('Dokumen terkait risk report diperbarui', [
                'count' => $updatedRelatedCount,
                'document_ids' => $documentIds
            ]);

            // 4. Jika tidak ada relasi documentables, coba tambahkan relasi baru
            $documentsRelationCount = DB::table('documentables')->count();

            if ($documentsRelationCount == 0) {
                Log::info('Tidak ada relasi documentables, mencoba membuat relasi baru');

                // Ambil risk reports untuk tenant aktif
                $riskReports = RiskReport::where('tenant_id', $tenantId)->get();
                $relationCreated = 0;

                // Ambil semua dokumen yang ada (dari tenant 1 atau tenant aktif)
                $documents = Document::whereIn('tenant_id', [1, $tenantId])->get();

                if ($riskReports->count() > 0 && $documents->count() > 0) {
                    // Ambil risk report pertama sebagai contoh
                    $firstRiskReport = $riskReports->first();

                    // Buat relasi untuk semua dokumen dengan risk report pertama
                    foreach ($documents as $doc) {
                        try {
                            // Periksa apakah relasi sudah ada
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

                                $relationCreated++;
                            }
                        } catch (\Exception $e) {
                            Log::error('Error saat membuat relasi dokumen', [
                                'document_id' => $doc->id,
                                'risk_report_id' => $firstRiskReport->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    Log::info('Relasi documentables baru dibuat', [
                        'count' => $relationCreated,
                        'risk_report_id' => $firstRiskReport->id,
                        'documents_count' => $documents->count()
                    ]);
                } else {
                    Log::info('Tidak dapat membuat relasi baru', [
                        'risk_reports_count' => $riskReports->count(),
                        'documents_count' => $documents->count()
                    ]);
                }
            }

            return $updatedCount + $updatedUploaderCount + $updatedRelatedCount;
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui tenant_id dokumen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Menampilkan dokumen berdasarkan tag yang dipilih
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function documentsByTag(Request $request, $slug)
    {
        // Default tenant ID jika tidak ada dalam sesi
        $defaultTenantId = 2;
        $tenantId = session('tenant_id') ?? $defaultTenantId;
        $validTenantIds = [1, $tenantId];

        // Ambil filter modul dari request, default 'all'
        $selectedModule = $request->input('module', 'all');

        // Cari tag berdasarkan slug
        $tag = \App\Models\Tag::where('slug', $slug)
            ->whereIn('tenant_id', $validTenantIds)
            ->firstOrFail();

        // Inisialisasi koleksi kosong
        $documents = collect();
        $riskReports = collect();
        $correspondences = collect();

        // Query kondisional berdasarkan filter modul
        if ($selectedModule === 'all' || $selectedModule === 'document-management') {
            // Dokumen dari modul Manajemen Dokumen dengan tag ini
            $documentIds = DB::table('document_tag')
                ->where('tag_id', $tag->id)
                ->where('document_type', 'App\\Models\\Document')
                ->pluck('document_id');

            $documents = Document::whereIn('id', $documentIds)
                ->whereIn('tenant_id', $validTenantIds)
                ->orderByDesc('document_date')
                ->get();
        }

        if ($selectedModule === 'all' || $selectedModule === 'risk-management') {
            // Dokumen dari modul Manajemen Risiko dengan tag ini
            $riskReportIds = DB::table('document_tag')
                ->where('tag_id', $tag->id)
                ->where('document_type', 'App\\Models\\RiskReport')
                ->pluck('document_id');

            $riskReports = RiskReport::whereIn('id', $riskReportIds)
                ->whereIn('tenant_id', $validTenantIds)
                ->orderByDesc('created_at')
                ->select(
                    'id',
                    'document_title',
                    'document_number',
                    'file_path',
                    'created_at',
                    'document_date',
                    'document_type',
                    'confidentiality_level'
                )
                ->get();
        }

        if ($selectedModule === 'all' || $selectedModule === 'correspondence') {
            // Dokumen dari modul Korespondensi dengan tag ini
            $correspondenceIds = DB::table('document_tag')
                ->where('tag_id', $tag->id)
                ->where('document_type', 'App\\Models\\Correspondence')
                ->pluck('document_id');

            $correspondences = \App\Models\Correspondence::whereIn('id', $correspondenceIds)
                ->whereIn('tenant_id', $validTenantIds)
                ->orderByDesc('created_at')
                ->select(
                    'id',
                    'document_title',
                    'document_number',
                    'file_path',
                    'created_at',
                    'document_date',
                    'document_type',
                    'confidentiality_level'
                )
                ->get();
        }

        // Gabungkan hasil query yang dijalankan
        $combinedDocuments = $documents
            ->concat($riskReports)
            ->concat($correspondences)
            ->sortByDesc(function ($item) {
                return $item->document_date ?? $item->created_at;
            });

        // Kirim filter aktif ke view
        return view('modules.DocumentManagement.documents-by-tag', compact('tag', 'combinedDocuments', 'selectedModule'));
    }

    /**
     * Menampilkan dokumen berdasarkan jenis confidentiality_level
     *
     * @param string $type (all, public, internal, confidential)
     * @return \Illuminate\View\View
     */
    public function documentsByType($type)
    {
        // Default tenant ID jika tidak ada dalam sesi
        $defaultTenantId = 2;
        $tenantId = session('tenant_id') ?? $defaultTenantId;
        $validTenantIds = [1, $tenantId];

        // Logging untuk debugging
        Log::info('Dokumen berdasarkan tipe diakses', [
            'type' => $type,
            'tenant_id' => $tenantId,
            'user_id' => Auth::id() ?? 'unknown'
        ]);

        // Siapkan query untuk Document
        $documentsQuery = Document::whereIn('tenant_id', $validTenantIds);

        // Siapkan query untuk RiskReport
        $riskReportsQuery = RiskReport::whereIn('tenant_id', $validTenantIds);

        // Filter berdasarkan tipe
        if ($type !== 'all') {
            // Filter dokumen dari modul Document Management
            $documentsQuery->where('confidentiality_level', $type);

            // Filter dokumen dari modul Risk Management
            // Perhatikan perbedaan format (publik/Publik, internal/Internal, rahasia/Rahasia)
            if ($type === 'public') {
                $riskReportsQuery->where('confidentiality_level', 'Publik');
            } elseif ($type === 'internal') {
                $riskReportsQuery->where('confidentiality_level', 'Internal');
            } elseif ($type === 'confidential') {
                $riskReportsQuery->where('confidentiality_level', 'Rahasia');
            }
        }

        // Ambil dokumen dari Document Management
        $documents = $documentsQuery->orderByDesc('document_date')->get();

        // Ambil dokumen dari Risk Management
        $riskReports = $riskReportsQuery->orderByDesc('created_at')
            ->select(
                'id',
                'document_title',
                'document_number',
                'file_path',
                'created_at',
                'document_date',
                'document_type',
                'confidentiality_level'
            )
            ->get();

        // Gabungkan kedua jenis dokumen
        $combinedDocuments = $documents->concat($riskReports)
            ->sortByDesc('document_date');

        // Tentukan judul halaman berdasarkan tipe
        $typeTitle = 'Semua Dokumen';
        if ($type === 'public') {
            $typeTitle = 'Dokumen Publik';
        } elseif ($type === 'internal') {
            $typeTitle = 'Dokumen Internal';
        } elseif ($type === 'confidential') {
            $typeTitle = 'Dokumen Rahasia';
        }

        Log::info('Hasil query dokumen berdasarkan tipe', [
            'type' => $type,
            'documents_count' => $documents->count(),
            'risk_reports_count' => $riskReports->count(),
            'combined_count' => $combinedDocuments->count()
        ]);

        return view('modules.DocumentManagement.documents-by-type', compact('combinedDocuments', 'typeTitle', 'type'));
    }
}
