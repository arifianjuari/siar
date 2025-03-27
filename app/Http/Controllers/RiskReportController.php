<?php

namespace App\Http\Controllers;

use App\Models\RiskReport;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiskReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // TEMPORARY: Bebaskan semua role untuk melihat daftar laporan
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User melihat daftar laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Semua role dapat melihat semua laporan dalam tenant mereka
        $query = RiskReport::where('tenant_id', auth()->user()->tenant_id);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal kejadian
        if ($request->filled('occurred_at')) {
            $query->whereDate('occurred_at', $request->occurred_at);
        }

        // Filter berdasarkan judul risiko (gunakan LIKE)
        if ($request->filled('risk_title')) {
            $query->where('risk_title', 'LIKE', '%' . $request->risk_title . '%');
        }

        // Dapatkan hasil query
        $riskReports = $query->orderBy('created_at', 'desc')->get();

        return view('risk_reports.index', compact('riskReports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // TEMPORARY: Bebaskan semua role untuk dapat membuat laporan
        // Dapatkan role dari relasi role
        $userRole = auth()->user()->role->slug ?? null;

        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mencoba membuat laporan risiko', [
            'user_id' => auth()->id(),
            'role' => $userRole,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Tampilkan form kosong untuk membuat risk report baru
        return view('risk_reports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat menyimpan laporan
        // Dapatkan role dari relasi role untuk logging
        $userRole = auth()->user()->role->slug ?? null;

        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mencoba menyimpan laporan risiko', [
            'user_id' => auth()->id(),
            'role' => $userRole,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'risk_title' => 'required|string|max:255',
            'chronology' => 'required|string',
            'reporter_unit' => 'required|string',
            'risk_type' => 'nullable|in:KTD,KNC,KTC,KPC',
            'risk_category' => 'required|string',
            'occurred_at' => 'required|date',
            'impact' => 'required|string',
            'probability' => 'required|string',
            'risk_level' => 'required|string',
            'recommendation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buat risk report baru
        $riskReport = new RiskReport($request->all());
        $riskReport->tenant_id = auth()->user()->tenant_id;
        $riskReport->created_by = auth()->id();
        $riskReport->status = 'open'; // Selalu dimulai dengan status open
        $riskReport->save();

        // Mencatat ke activity log
        $this->logActivity(
            'create',
            $riskReport,
            null,
            'Membuat laporan risiko baru: ' . $riskReport->risk_title
        );

        return redirect()->route('modules.risk-management.risk-reports.index')->with('success', 'Laporan risiko berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(RiskReport $riskReport)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat melihat laporan
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User melihat laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $riskReport->id
        ]);

        return view('risk_reports.show', compact('riskReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RiskReport $riskReport)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat mengedit laporan
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mencoba mengedit laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $riskReport->id
        ]);

        return view('risk_reports.edit', compact('riskReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RiskReport $riskReport)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat update laporan
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mencoba update laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $riskReport->id
        ]);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'risk_title' => 'required|string|max:255',
            'chronology' => 'required|string',
            'reporter_unit' => 'required|string',
            'risk_type' => 'nullable|in:KTD,KNC,KTC,KPC',
            'risk_category' => 'required|string',
            'occurred_at' => 'required|date',
            'impact' => 'required|string',
            'probability' => 'required|string',
            'risk_level' => 'required|string',
            'recommendation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Simpan data awal sebelum diubah untuk audit trail
        $originalData = $riskReport->getAttributes();

        // Update data laporan tanpa mengubah status
        $riskReport->risk_title = $request->risk_title;
        $riskReport->chronology = $request->chronology;
        $riskReport->reporter_unit = $request->reporter_unit;
        $riskReport->risk_type = $request->risk_type;
        $riskReport->risk_category = $request->risk_category;
        $riskReport->occurred_at = $request->occurred_at;
        $riskReport->impact = $request->impact;
        $riskReport->probability = $request->probability;
        $riskReport->risk_level = $request->risk_level;
        $riskReport->recommendation = $request->recommendation;
        $riskReport->save();

        // Mencatat perubahan ke activity log
        $updatedData = $riskReport->getAttributes();
        $changes = $this->getDataChanges($originalData, $updatedData);

        if (!empty($changes)) {
            $this->logActivity(
                'update',
                $riskReport,
                $changes,
                'Memperbarui laporan risiko: ' . $riskReport->risk_title
            );
        }

        return redirect()->route('modules.risk-management.risk-reports.index')->with('success', 'Laporan risiko berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RiskReport $riskReport)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat menghapus laporan
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User menghapus laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $riskReport->id
        ]);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus laporan dari tenant lain.');
        }

        // Simpan data sebelum dihapus untuk log activity
        $reportId = $riskReport->id;
        $reportTitle = $riskReport->risk_title;

        // Hapus laporan
        $riskReport->delete();

        // Mencatat ke activity log
        $this->logActivity(
            'delete',
            new RiskReport(['id' => $reportId]),
            null,
            'Menghapus laporan risiko: ' . $reportTitle
        );

        return redirect()->route('modules.risk-management.risk-reports.index')->with('success', 'Laporan risiko berhasil dihapus');
    }

    /**
     * Mark a risk report as in review
     */
    public function markInReview($id)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat mengubah status laporan ke in_review
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mereview laporan dari tenant lain.');
        }

        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mengubah status laporan risiko ke in_review', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $riskReport->id
        ]);

        // Update status
        $riskReport->status = 'in_review';
        $riskReport->reviewed_by = auth()->id();
        $riskReport->reviewed_at = now();
        $riskReport->save();

        // Mencatat ke activity log
        $this->logActivity(
            'update',
            $riskReport,
            ['status' => ['old' => 'open', 'new' => 'in_review']],
            'Mengubah status laporan risiko menjadi In Review: ' . $riskReport->risk_title
        );

        return redirect()->back()->with('success', 'Status laporan risiko berhasil diubah menjadi In Review');
    }

    /**
     * Approve a risk report
     */
    public function approve($id)
    {
        // TEMPORARY: Bebaskan semua role untuk dapat mengubah status laporan ke resolved
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui laporan dari tenant lain.');
        }

        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mengubah status laporan risiko ke resolved', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $riskReport->id
        ]);

        // Update status
        $riskReport->status = 'resolved';
        $riskReport->approved_by = auth()->id();
        $riskReport->approved_at = now();
        $riskReport->save();

        // Mencatat ke activity log
        $this->logActivity(
            'update',
            $riskReport,
            ['status' => ['old' => $riskReport->getOriginal('status'), 'new' => 'resolved']],
            'Menyetujui dan menyelesaikan laporan risiko: ' . $riskReport->risk_title
        );

        return redirect()->back()->with('success', 'Laporan risiko telah disetujui dan diselesaikan');
    }

    /**
     * Helper method untuk memeriksa apakah pengguna dapat mengakses laporan.
     */
    private function canAccessReport(RiskReport $riskReport)
    {
        // Dapatkan role dari relasi role
        $userRole = auth()->user()->role->slug ?? null;

        // Superadmin bisa akses semua laporan
        if ($userRole === 'superadmin') {
            return true;
        }

        // Cek apakah laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            return false;
        }

        // Staf hanya bisa akses laporan miliknya
        if ($userRole === 'staf' && $riskReport->created_by !== auth()->id()) {
            return false;
        }

        // Admin RS tidak bisa melihat detail laporan
        if ($userRole === 'admin-rs') {
            return false;
        }

        // Role lain (Manajemen Operasional, Manajemen Eksekutif, Manajemen Strategis, Auditor Internal)
        // bisa melihat semua laporan dalam tenantnya
        return in_array($userRole, [
            'manajemen-operasional',
            'manajemen-eksekutif',
            'manajemen-strategis',
            'auditor-internal'
        ]);
    }

    /**
     * Helper method untuk memeriksa apakah pengguna dapat mengubah status laporan.
     */
    private function canChangeStatus(RiskReport $riskReport, string $newStatus)
    {
        // Dapatkan role dari relasi role
        $userRole = auth()->user()->role->slug ?? null;

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            return false;
        }

        // Untuk status in_review, hanya Manajemen Operasional yang bisa
        if ($newStatus === 'in_review') {
            return $userRole === 'manajemen-operasional' && $riskReport->status === 'open';
        }

        // Untuk status resolved, hanya Manajemen Eksekutif yang bisa
        if ($newStatus === 'resolved') {
            return $userRole === 'manajemen-eksekutif' && $riskReport->status === 'in_review';
        }

        return false;
    }

    /**
     * Helper method untuk memeriksa apakah pengguna dapat mengakses dashboard.
     */
    private function canAccessDashboard()
    {
        // Semua role bisa mengakses dashboard
        return true;
    }

    /**
     * Helper method untuk memeriksa apakah pengguna dapat mengekspor laporan.
     */
    private function canExportReport(RiskReport $riskReport)
    {
        // Dapatkan role dari relasi role
        $userRole = auth()->user()->role->slug ?? null;

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            return false;
        }

        // Hanya Manajemen Eksekutif, Manajemen Strategis, dan Auditor Internal yang bisa ekspor
        return in_array($userRole, ['manajemen-eksekutif', 'manajemen-strategis', 'auditor-internal']);
    }

    /**
     * Helper method untuk memeriksa apakah pengguna dapat menandatangani laporan (QR).
     */
    private function canSignReport(RiskReport $riskReport)
    {
        // Dapatkan role dari relasi role
        $userRole = auth()->user()->role->slug ?? null;

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            return false;
        }

        // Hanya Manajemen Eksekutif yang bisa tanda tangan QR
        return $userRole === 'manajemen-eksekutif';
    }

    /**
     * Generate QR code for approved risk report.
     */
    public function generateQr($id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Check if user can sign the report
        if (!$this->canSignReport($riskReport)) {
            abort(403, 'Anda tidak memiliki izin untuk menandatangani laporan ini.');
        }

        // Check if the report is approved
        if ($riskReport->status !== 'resolved') {
            return response()->json(['error' => 'Laporan belum disetujui.'], 400);
        }

        // Get approver name and role
        $approverUser = $riskReport->approver ?? null;
        $approverName = $approverUser ? $approverUser->name : 'Unknown';
        $approverRole = $approverUser ? $approverUser->role : 'Unknown';

        // Create QR code content
        $qrContent = "Disetujui oleh: {$approverName}\n";
        $qrContent .= "Jabatan: {$approverRole}\n";
        $qrContent .= "di: " . $riskReport->approved_at->format('d/m/Y H:i') . "\n";
        $qrContent .= "No.Laporan: {$riskReport->id}";

        // Generate QR code as PNG with higher quality
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($qrContent);

        return response($qrCode)->header('Content-Type', 'image/png');
    }

    /**
     * Export risk report to Word format (Laporan Awal).
     */
    public function exportWordAwal($id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Check if user can export the report
        if (!$this->canExportReport($riskReport)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses laporan ini.');
        }

        return response()->view('risk_reports.laporan_awal', ['riskReport' => $riskReport])
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="laporan_awal_' . $id . '.doc"');
    }

    /**
     * Export risk report to Word format (Laporan Akhir).
     */
    public function exportWordAkhir($id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Check if user can export the report
        if (!$this->canExportReport($riskReport)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses laporan ini.');
        }

        // For completed reports, include QR code data (if approved)
        $qrCodeData = null;
        if ($riskReport->status === 'resolved' && $riskReport->approved_by && $riskReport->approved_at) {
            // Get approver name and role
            $approverUser = $riskReport->approver ?? null;
            $approverName = $approverUser ? $approverUser->name : 'Unknown';
            $approverRole = $approverUser ? $approverUser->role : 'Unknown';

            // Create QR code content
            $qrContent = "Disetujui oleh: {$approverName}\n";
            $qrContent .= "Jabatan: {$approverRole}\n";
            $qrContent .= "di: " . $riskReport->approved_at->format('d/m/Y H:i') . "\n";
            $qrContent .= "No.Laporan: {$riskReport->id}";

            // Generate QR code as base64 image for embedding in Word
            $qrCodeData = base64_encode(QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($qrContent));
        }

        return response()->view('risk_reports.laporan_akhir', [
            'riskReport' => $riskReport,
            'qrCodeData' => $qrCodeData
        ])
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="laporan_akhir_' . $id . '.doc"');
    }

    /**
     * Display dashboard with statistics.
     */
    public function dashboard()
    {
        // TEMPORARY: Bebaskan semua role untuk dapat mengakses dashboard
        // Log aktivitas
        \Illuminate\Support\Facades\Log::info('User mengakses dashboard manajemen risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Ambil data tenant saat ini
        $tenantId = auth()->user()->tenant_id;

        // Superadmin bisa melihat semua tenant, tapi kita tidak implementasikan disini
        // karena dashboard dibatasi hanya untuk role tertentu yang terikat tenant

        // Query untuk total laporan
        $totalReports = RiskReport::where('tenant_id', $tenantId)->count();

        // Query untuk laporan berdasarkan status
        $reportsByStatus = RiskReport::where('tenant_id', $tenantId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Pastikan semua status ada
        $statusData = [
            'open' => $reportsByStatus['open'] ?? 0,
            'in_review' => $reportsByStatus['in_review'] ?? 0,
            'resolved' => $reportsByStatus['resolved'] ?? 0
        ];

        // Query untuk laporan berdasarkan risk_level
        $reportsByRiskLevel = RiskReport::where('tenant_id', $tenantId)
            ->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();

        // Laporan per bulan (12 bulan terakhir)
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $monthlyReports = RiskReport::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Format data untuk chart
        $labels = [];
        $data = [];

        // Inisialisasi array data bulanan
        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths(11 - $i);
            $labels[] = $month->format('M Y');
            $data[$month->format('Y-m')] = 0;
        }

        // Isi data dari database
        foreach ($monthlyReports as $report) {
            $key = sprintf('%04d-%02d', $report->year, $report->month);
            if (isset($data[$key])) {
                $data[$key] = $report->total;
            }
        }

        return view('risk_reports.dashboard', [
            'totalReports' => $totalReports,
            'statusData' => $statusData,
            'reportsByRiskLevel' => $reportsByRiskLevel,
            'labels' => $labels,
            'data' => array_values($data)
        ]);
    }

    /**
     * Helper method untuk mencatat aktivitas pengguna.
     */
    private function logActivity($action, RiskReport $model, $changes = null, $description = null)
    {
        ActivityLog::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => RiskReport::class,
            'model_id' => $model->id,
            'changes' => $changes,
            'description' => $description,
        ]);
    }

    /**
     * Helper method untuk menghitung perubahan data.
     */
    private function getDataChanges($originalData, $updatedData)
    {
        $changes = [];
        $fieldsToIgnore = ['updated_at', 'created_at', 'id', 'tenant_id', 'created_by'];

        foreach ($updatedData as $key => $value) {
            // Abaikan field yang tidak perlu dicatat perubahannya
            if (in_array($key, $fieldsToIgnore)) {
                continue;
            }

            // Cek apakah field berubah
            if (isset($originalData[$key]) && $originalData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value
                ];
            }
        }

        return $changes;
    }
}
