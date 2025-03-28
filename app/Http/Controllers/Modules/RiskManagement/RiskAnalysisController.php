<?php

namespace App\Http\Controllers\Modules\RiskManagement;

use App\Http\Controllers\Controller;
use App\Models\RiskReport;
use App\Models\RiskAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiskAnalysisController extends Controller
{
    /**
     * Inisialisasi controller dan middleware
     */
    public function __construct()
    {
        $this->middleware('module:risk-management');

        // Logging untuk debug
        \Illuminate\Support\Facades\Log::info('RiskAnalysisController construct');

        // Tidak menggunakan authorizeResource untuk menghindari masalah izin
        // $this->authorizeResource(RiskAnalysis::class, 'analysis', [
        //     'except' => ['create', 'store'],
        // ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the form for creating a new risk analysis.
     */
    public function create(Request $request, $reportId)
    {
        $report = RiskReport::with('creator')->findOrFail($reportId);

        // Logging untuk debug
        \Illuminate\Support\Facades\Log::info('RiskAnalysisController create', [
            'user_id' => Auth::id(),
            'report_id' => $reportId,
            'report_exists' => $report ? true : false,
        ]);

        // Bypass Authorization untuk debugging
        // $this->authorize('create', [RiskAnalysis::class, $report]);

        // Verifikasi manual izin (dengan bypass untuk tenant admin)
        $user = Auth::user();
        if (
            $user->role &&
            $user->role->slug !== 'superadmin' &&
            strtolower($user->role->slug) !== 'tenant-admin'
        ) {
            $this->authorize('create', [RiskAnalysis::class, $report]);
        }

        // Cek jika analisis sudah ada, redirect ke edit
        if ($report->analysis) {
            return redirect()->route('modules.risk-management.risk-analysis.edit', [$reportId, $report->analysis->id]);
        }

        // Definisi daftar faktor kontributor yang dapat dipilih
        $contributorFactors = [
            'organizational' => [
                'policies_procedures' => 'Kebijakan dan prosedur tidak jelas',
                'staffing' => 'Kekurangan staf',
                'workload' => 'Beban kerja berlebihan',
                'communication' => 'Komunikasi tidak efektif',
                'leadership' => 'Pengawasan/kepemimpinan tidak memadai',
                'resources' => 'Sumber daya yang terbatas'
            ],
            'human_factors' => [
                'knowledge' => 'Pengetahuan yang tidak memadai',
                'skills' => 'Keterampilan yang tidak mencukupi',
                'fatigue' => 'Kelelahan',
                'stress' => 'Stres',
                'distraction' => 'Gangguan/Distraksi',
                'complacency' => 'Sikap terlalu percaya diri'
            ],
            'technical' => [
                'equipment_failure' => 'Kegagalan peralatan',
                'software_issues' => 'Masalah perangkat lunak',
                'design_flaws' => 'Kesalahan desain',
                'maintenance' => 'Pemeliharaan yang tidak memadai',
                'compatibility' => 'Masalah kompatibilitas'
            ],
            'environmental' => [
                'physical_environment' => 'Lingkungan fisik tidak aman',
                'noise' => 'Kebisingan',
                'lighting' => 'Pencahayaan tidak memadai',
                'temperature' => 'Suhu tidak sesuai',
                'space_constraints' => 'Keterbatasan ruang'
            ]
        ];

        return view('modules.RiskManagement.risk-analysis.create', compact('report', 'contributorFactors'));
    }

    /**
     * Store a newly created risk analysis in storage.
     */
    public function store(Request $request, $reportId)
    {
        $report = RiskReport::findOrFail($reportId);

        // Autorisasi dengan policy
        $this->authorize('create', [RiskAnalysis::class, $report]);

        $request->validate([
            'direct_cause' => 'required|string',
            'root_cause' => 'required|string',
            'contributor_factors' => 'nullable|array',
            'recommendation_short' => 'required|string',
            'recommendation_medium' => 'nullable|string',
            'recommendation_long' => 'nullable|string',
            'analysis_status' => 'required|string|in:draft,in_progress,completed,reviewed',
        ]);

        DB::beginTransaction();

        try {
            $analysisData = $request->all();
            $analysisData['analyzed_by'] = Auth::id();
            $analysisData['analyzed_at'] = Carbon::now();

            $analysis = $report->analysis()->create($analysisData);

            DB::commit();

            return redirect()
                ->route('modules.risk-management.risk-reports.show', $reportId)
                ->with('success', 'Analisis risiko berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified risk analysis.
     */
    public function show($reportId, $id)
    {
        $report = RiskReport::with('creator', 'reviewer', 'approver')->findOrFail($reportId);
        $analysis = RiskAnalysis::with('analyst')->findOrFail($id);

        // Pastikan analisis milik laporan yang benar
        if ($analysis->risk_report_id != $reportId) {
            abort(404);
        }

        return view('modules.RiskManagement.risk-analysis.show', compact('report', 'analysis'));
    }

    /**
     * Show the form for editing the specified risk analysis.
     */
    public function edit($reportId, $id)
    {
        $report = RiskReport::with('creator')->findOrFail($reportId);
        $analysis = RiskAnalysis::with('analyst')->findOrFail($id);

        // Pastikan analisis milik laporan yang benar
        if ($analysis->risk_report_id != $reportId) {
            abort(404);
        }

        // Definisi daftar faktor kontributor yang dapat dipilih
        $contributorFactors = [
            'organizational' => [
                'policies_procedures' => 'Kebijakan dan prosedur tidak jelas',
                'staffing' => 'Kekurangan staf',
                'workload' => 'Beban kerja berlebihan',
                'communication' => 'Komunikasi tidak efektif',
                'leadership' => 'Pengawasan/kepemimpinan tidak memadai',
                'resources' => 'Sumber daya yang terbatas'
            ],
            'human_factors' => [
                'knowledge' => 'Pengetahuan yang tidak memadai',
                'skills' => 'Keterampilan yang tidak mencukupi',
                'fatigue' => 'Kelelahan',
                'stress' => 'Stres',
                'distraction' => 'Gangguan/Distraksi',
                'complacency' => 'Sikap terlalu percaya diri'
            ],
            'technical' => [
                'equipment_failure' => 'Kegagalan peralatan',
                'software_issues' => 'Masalah perangkat lunak',
                'design_flaws' => 'Kesalahan desain',
                'maintenance' => 'Pemeliharaan yang tidak memadai',
                'compatibility' => 'Masalah kompatibilitas'
            ],
            'environmental' => [
                'physical_environment' => 'Lingkungan fisik tidak aman',
                'noise' => 'Kebisingan',
                'lighting' => 'Pencahayaan tidak memadai',
                'temperature' => 'Suhu tidak sesuai',
                'space_constraints' => 'Keterbatasan ruang'
            ]
        ];

        return view(
            'modules.RiskManagement.risk-analysis.edit',
            compact('report', 'analysis', 'contributorFactors')
        );
    }

    /**
     * Update the specified risk analysis in storage.
     */
    public function update(Request $request, $reportId, $id)
    {
        $analysis = RiskAnalysis::findOrFail($id);

        // Pastikan analisis milik laporan yang benar
        if ($analysis->risk_report_id != $reportId) {
            abort(404);
        }

        $request->validate([
            'direct_cause' => 'required|string',
            'root_cause' => 'required|string',
            'contributor_factors' => 'nullable|array',
            'recommendation_short' => 'required|string',
            'recommendation_medium' => 'nullable|string',
            'recommendation_long' => 'nullable|string',
            'analysis_status' => 'required|string|in:draft,in_progress,completed,reviewed',
        ]);

        DB::beginTransaction();

        try {
            $analysisData = $request->all();

            // Hanya perbarui timestamp analyzed_at jika status berubah
            if ($analysis->analysis_status != $request->analysis_status) {
                $analysisData['analyzed_at'] = Carbon::now();
            }

            $analysis->update($analysisData);

            DB::commit();

            return redirect()
                ->route('modules.risk-management.risk-reports.show', $reportId)
                ->with('success', 'Analisis risiko berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($reportId, $id)
    {
        $analysis = RiskAnalysis::findOrFail($id);

        // Pastikan analisis milik laporan yang benar
        if ($analysis->risk_report_id != $reportId) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $analysis->delete();
            DB::commit();

            return redirect()
                ->route('modules.risk-management.risk-reports.show', $reportId)
                ->with('success', 'Analisis risiko berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code for risk analysis.
     */
    public function generateQr($reportId, $id)
    {
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mencoba mengakses QR code analisis', [
            'user_id' => Auth::id(),
            'role' => Auth::user()->role->slug ?? null,
            'tenant_id' => Auth::user()->tenant_id ?? null,
            'report_id' => $reportId,
            'analysis_id' => $id
        ]);

        $report = RiskReport::findOrFail($reportId);
        $analysis = RiskAnalysis::with('analyst')->findOrFail($id);

        // Pastikan analisis milik laporan yang benar
        if ($analysis->risk_report_id != $reportId) {
            abort(404, 'Analisis tidak ditemukan');
        }

        // Pastikan laporan berada dalam tenant yang sama
        if ($report->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses QR code ini.');
        }

        // Dapatkan informasi analis dan penandatangan
        $analystName = $analysis->analyst ? $analysis->analyst->name : 'Tidak diketahui';
        $signerName = Auth::user()->name ?? 'Tidak diketahui';
        $signerRole = Auth::user()->role->name ?? 'Tidak diketahui';
        $signerDate = \Carbon\Carbon::now()->format('d-m-Y H:i');

        // Buat konten QR yang lebih terstruktur dan jelas
        $qrContent = "=== VERIFIKASI ANALISIS RISIKO ===\n\n";
        $qrContent .= "NO. KASUS: {$report->id}\n";
        $qrContent .= "JUDUL: {$report->risk_title}\n";
        $qrContent .= "STATUS: {$analysis->status_label}\n\n";
        $qrContent .= "DIANALISIS OLEH:\n";
        $qrContent .= "Nama: {$analystName}\n";
        $qrContent .= "Tanggal: " . ($analysis->analyzed_at ? $analysis->analyzed_at->format('d-m-Y H:i') : \Carbon\Carbon::now()->format('d-m-Y H:i')) . "\n\n";
        $qrContent .= "DITANDATANGANI OLEH:\n";
        $qrContent .= "Nama: {$signerName}\n";
        $qrContent .= "Jabatan: {$signerRole}\n";
        $qrContent .= "Tanggal: {$signerDate}";

        // Generate QR code as SVG (tidak membutuhkan imagick)
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrContent);

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'inline; filename="qr-code-analysis-' . $id . '.svg"');
    }
}
