<?php

namespace App\Http\Controllers\Modules\RiskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskReport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;

class RiskReportController extends Controller
{
    /**
     * Constructor dengan middleware untuk pemeriksaan izin
     */
    public function __construct()
    {
        // Pastikan bahwa middleware modul sudah dijalankan
        $this->middleware('module:risk-management');

        // Tambahkan middleware izin untuk setiap aksi yang perlu diproteksi
        $this->middleware('check.permission:risk-management,can_create')->only(['create', 'store']);
        $this->middleware('check.permission:risk-management,can_edit')->only(['edit', 'update', 'markInReview', 'approve']);
        $this->middleware('check.permission:risk-management,can_delete')->only('destroy');
        $this->middleware('check.permission:risk-management,can_export')->only(['exportWordAwal', 'exportWordAkhir']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Log aktivitas
        Log::info('User melihat daftar laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Query untuk mengambil data asli dari database dengan relasi analysis
        $query = RiskReport::with('analysis')->where('tenant_id', auth()->user()->tenant_id);

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

        return view('modules.RiskManagement.risk-reports.index', compact('riskReports'));
    }

    /**
     * Show dashboard for risk management.
     */
    public function dashboard()
    {
        // Ambil statistik aktual dari database
        $stats = [
            'total' => RiskReport::where('tenant_id', auth()->user()->tenant_id)->count(),
            'open' => RiskReport::where('tenant_id', auth()->user()->tenant_id)->where('status', 'open')->count(),
            'in_review' => RiskReport::where('tenant_id', auth()->user()->tenant_id)->where('status', 'in_review')->count(),
            'resolved' => RiskReport::where('tenant_id', auth()->user()->tenant_id)->where('status', 'resolved')->count(),
            'low_risk' => RiskReport::where('tenant_id', auth()->user()->tenant_id)
                ->whereIn('risk_level', ['Rendah', 'Low', 'rendah', 'low'])
                ->count(),
            'medium_risk' => RiskReport::where('tenant_id', auth()->user()->tenant_id)
                ->whereIn('risk_level', ['Sedang', 'Medium', 'sedang', 'medium'])
                ->count(),
            'high_risk' => RiskReport::where('tenant_id', auth()->user()->tenant_id)
                ->whereIn('risk_level', ['Tinggi', 'High', 'tinggi', 'high'])
                ->count(),
        ];

        // Data untuk chart bulanan - ambil data aktual 12 bulan terakhir
        $monthlyData = [];
        $monthLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->format('M');

            $count = RiskReport::where('tenant_id', auth()->user()->tenant_id)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyData[] = $count;
        }

        // Ambil 5 laporan terbaru dari database
        $recentReports = RiskReport::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->risk_title,
                    'category' => $report->risk_category,
                    'risk_level' => strtolower($report->risk_level),
                    'status' => $report->status,
                    'created_at' => $report->created_at,
                ];
            });

        return view('modules.RiskManagement.dashboard', compact(
            'stats',
            'monthlyData',
            'monthLabels',
            'recentReports'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log aktivitas
        Log::info('User mencoba membuat laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Ambil daftar unit kerja yang aktif untuk tenant saat ini
        $workUnits = \App\Models\WorkUnit::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.RiskManagement.risk-reports.create', compact('workUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log aktivitas
        Log::info('User mencoba menyimpan laporan risiko', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null
        ]);

        // Dapatkan daftar nama unit kerja yang valid
        $validWorkUnitNames = \App\Models\WorkUnit::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->pluck('name')
            ->toArray();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'risk_title' => 'required|string|max:255',
            'chronology' => 'required|string',
            'reporter_unit' => ['required', 'string', function ($attribute, $value, $fail) use ($validWorkUnitNames) {
                if (!in_array($value, $validWorkUnitNames)) {
                    $fail('Unit pelapor yang dipilih tidak valid.');
                }
            }],
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

        // Mencatat ke log
        Log::info('Laporan risiko berhasil dibuat', [
            'report_id' => $riskReport->id,
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id
        ]);

        return redirect()->route('modules.risk-management.risk-reports.index')
            ->with('success', 'Laporan risiko berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk melihat laporan dari tenant lain.');
        }

        return view('modules.RiskManagement.risk-reports.show', compact('riskReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit laporan dari tenant lain.');
        }

        // Ambil daftar unit kerja yang aktif untuk tenant saat ini
        $workUnits = \App\Models\WorkUnit::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.RiskManagement.risk-reports.edit', compact('riskReport', 'workUnits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk memperbarui laporan dari tenant lain.');
        }

        // Dapatkan daftar nama unit kerja yang valid
        $validWorkUnitNames = \App\Models\WorkUnit::where('tenant_id', auth()->user()->tenant_id)
            ->where('is_active', true)
            ->pluck('name')
            ->toArray();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'risk_title' => 'required|string|max:255',
            'chronology' => 'required|string',
            'reporter_unit' => ['required', 'string', function ($attribute, $value, $fail) use ($validWorkUnitNames) {
                if (!in_array($value, $validWorkUnitNames)) {
                    $fail('Unit pelapor yang dipilih tidak valid.');
                }
            }],
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

        // Update data laporan
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

        return redirect()->route('modules.risk-management.risk-reports.index')
            ->with('success', 'Laporan risiko berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus laporan dari tenant lain.');
        }

        // Pemeriksaan izin manual tambahan
        if (!\App\Helpers\PermissionHelper::hasPermission('risk-management', 'can_delete')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus laporan risiko.');
        }

        // Hapus laporan
        $riskReport->delete();

        return redirect()->route('modules.risk-management.risk-reports.index')
            ->with('success', 'Laporan risiko berhasil dihapus');
    }

    /**
     * Mark the report as in review.
     */
    public function markInReview($id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status laporan dari tenant lain.');
        }

        // Pemeriksaan izin manual tambahan
        if (!\App\Helpers\PermissionHelper::hasPermission('risk-management', 'can_edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status laporan risiko.');
        }

        $riskReport->status = 'in_review';
        $riskReport->reviewed_by = auth()->id();
        $riskReport->reviewed_at = now();
        $riskReport->save();

        return redirect()->route('modules.risk-management.risk-reports.index')
            ->with('success', 'Status laporan risiko berhasil diubah menjadi In Review');
    }

    /**
     * Approve the report.
     */
    public function approve($id)
    {
        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui laporan dari tenant lain.');
        }

        // Pemeriksaan izin manual tambahan
        if (!\App\Helpers\PermissionHelper::hasPermission('risk-management', 'can_edit')) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui laporan risiko.');
        }

        $riskReport->status = 'resolved';
        $riskReport->approved_by = auth()->id();
        $riskReport->approved_at = now();
        $riskReport->save();

        return redirect()->route('modules.risk-management.risk-reports.index')
            ->with('success', 'Laporan risiko berhasil disetujui');
    }

    /**
     * Generate QR code for risk report.
     */
    public function generateQr($id)
    {
        // Log aktivitas
        Log::info('User mencoba mengakses QR code', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $id
        ]);

        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses QR code ini.');
        }

        // Dapatkan informasi penandatangan
        $signerName = auth()->user()->name ?? 'Unknown';
        $signerRole = auth()->user()->role->name ?? 'Unknown';
        $signerDate = now()->format('d-m-Y H:i');

        // Buat informasi QR yang lebih jelas dan terstruktur
        $qrContent = "=== TANDA TANGAN DIGITAL ===\n\n";
        $qrContent .= "LAPORAN RISIKO #{$riskReport->id}\n\n";
        $qrContent .= "Judul: {$riskReport->risk_title}\n";
        $qrContent .= "Unit: {$riskReport->reporter_unit}\n";
        $qrContent .= "Tanggal Kejadian: {$riskReport->occurred_at->format('d-m-Y')}\n";
        $qrContent .= "Level Risiko: {$riskReport->risk_level}\n\n";
        $qrContent .= "PENANDATANGAN:\n";
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
            ->header('Content-Disposition', 'inline; filename="qr-code-' . $id . '.svg"');
    }

    /**
     * Export risk report to Word format (Laporan Awal).
     */
    public function exportWordAwal($id)
    {
        // Log aktivitas
        Log::info('User mengekspor laporan awal ke Word', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $id
        ]);

        $riskReport = RiskReport::findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor laporan dari tenant lain.');
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
        // Log aktivitas
        Log::info('User mengekspor laporan akhir ke PDF', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'report_id' => $id
        ]);

        $riskReport = RiskReport::with('analysis.analyst')->findOrFail($id);

        // Pastikan laporan berada dalam tenant yang sama
        if ($riskReport->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor laporan dari tenant lain.');
        }

        // Generate QR code langsung untuk dimasukkan ke PDF
        $qrCodeData = null;
        $qrCodeAnalysis = null;

        // QR Code untuk laporan yang sudah disetujui
        if ($riskReport->status === 'resolved' && $riskReport->approved_by) {
            // Dapatkan informasi pengguna yang menyetujui
            $approver = \App\Models\User::find($riskReport->approved_by);
            $approverName = $approver ? $approver->name : 'Unknown';
            $approverRole = $approver && $approver->role ? $approver->role->name : 'Unknown';

            // Buat konten QR code yang lebih lengkap
            $qrContent = "=== TANDA TANGAN DIGITAL ===\n\n";
            $qrContent .= "LAPORAN RISIKO #{$riskReport->id}\n\n";
            $qrContent .= "Judul: {$riskReport->risk_title}\n";
            $qrContent .= "Unit: {$riskReport->reporter_unit}\n";
            $qrContent .= "Status: DISETUJUI\n\n";
            $qrContent .= "PENANDATANGAN:\n";
            $qrContent .= "Nama: {$approverName}\n";
            $qrContent .= "Jabatan: {$approverRole}\n";
            $qrContent .= "Tanggal: " . $riskReport->approved_at->format('d/m/Y H:i');

            // Generate QR code sebagai SVG yang tidak memerlukan imagick
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($qrContent);

            $qrCodeData = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        }

        // QR Code untuk analisis yang sudah selesai
        if ($riskReport->analysis && $riskReport->analysis->analysis_status === 'completed') {
            $analystName = $riskReport->analysis->analyst ? $riskReport->analysis->analyst->name : 'Tidak diketahui';

            // Buat konten QR code untuk analisis yang sudah selesai
            $qrContent = "=== VERIFIKASI ANALISIS RISIKO ===\n\n";
            $qrContent .= "NO. KASUS: {$riskReport->id}\n";
            $qrContent .= "JUDUL: {$riskReport->risk_title}\n";
            $qrContent .= "STATUS: {$riskReport->analysis->status_label}\n\n";
            $qrContent .= "DIANALISIS OLEH:\n";
            $qrContent .= "Nama: {$analystName}\n";
            $qrContent .= "Tanggal: " . ($riskReport->analysis->analyzed_at ? $riskReport->analysis->analyzed_at->format('d-m-Y H:i') : now()->format('d-m-Y H:i'));

            // Generate QR code sebagai SVG yang tidak memerlukan imagick
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($qrContent);

            $qrCodeAnalysis = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        }

        // Render view ke HTML
        $html = view('risk_reports.laporan_akhir', [
            'riskReport' => $riskReport,
            'qrCodeData' => $qrCodeData,
            'qrCodeAnalysis' => $qrCodeAnalysis
        ])->render();

        // Gunakan Dompdf untuk konversi HTML ke PDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF ke browser
        return $dompdf->stream("laporan_akhir_{$id}.pdf");
    }
}
