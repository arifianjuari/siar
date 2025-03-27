<?php

namespace App\Http\Controllers\Modules\RiskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskReport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        // Query untuk mengambil data asli dari database
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

        return view('risk_reports.create', compact('workUnits'));
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

        return view('risk_reports.show', compact('riskReport'));
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

        return view('risk_reports.edit', compact('riskReport', 'workUnits'));
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
}
