<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        // Ambil semua unit kerja untuk tenant ini
        $allUnits = WorkUnit::with('headOfUnit')
            ->where('tenant_id', $tenantId)
            ->orderBy('order')->orderBy('unit_name') // Urutkan berdasarkan order, lalu nama
            ->get();

        // Bangun struktur hierarki
        $workUnitsHierarchy = $this->buildHierarchy($allUnits);

        // Fungsi rekursif untuk meratakan hierarki dengan level kedalaman
        $flattenedWorkUnits = $this->flattenHierarchy($workUnitsHierarchy);

        // Pesan debug untuk menentukan apakah view ini yang digunakan
        \Illuminate\Support\Facades\Log::info('WorkUnitController@index dipanggil', [
            'count' => count($flattenedWorkUnits),
            'user_id' => Auth::id(),
            'time' => now()->toDateTimeString()
        ]);

        // Tidak perlu pagination jika ingin menampilkan semua dalam hierarki
        return view('modules.WorkUnit.index', compact('flattenedWorkUnits'))->with('debug_message', 'View loaded at ' . now());
    }

    /**
     * Membangun struktur hierarki dari daftar unit kerja.
     */
    private function buildHierarchy($units, $parentId = null)
    {
        $branch = [];
        foreach ($units as $unit) {
            if ($unit->parent_id == $parentId) {
                $children = $this->buildHierarchy($units, $unit->id);
                if ($children) {
                    $unit->children_units = $children;
                }
                $branch[$unit->id] = $unit;
                // Hapus unit dari daftar awal agar tidak diproses lagi
                // unset($units[$key]); // Opsional, bisa mempengaruhi performa jika daftar besar
            }
        }
        return $branch;
    }

    /**
     * Meratakan struktur hierarki menjadi daftar flat dengan level kedalaman.
     */
    private function flattenHierarchy($hierarchy, $level = 0)
    {
        $result = [];
        foreach ($hierarchy as $unit) {
            $unit->depth = $level;
            $result[] = $unit;
            if (isset($unit->children_units) && count($unit->children_units) > 0) {
                $result = array_merge($result, $this->flattenHierarchy($unit->children_units, $level + 1));
            }
            // Hapus property children_units agar tidak membingungkan di view
            unset($unit->children_units);
        }
        return $result;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = Auth::user()->tenant_id;
        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
        $potentialParents = WorkUnit::where('tenant_id', $tenantId)
            ->orderBy('unit_name')
            ->get();

        return view('modules.WorkUnit.form', compact('users', 'potentialParents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateWorkUnit($request);
        $validated['tenant_id'] = Auth::user()->tenant_id;
        $validated['is_active'] = true;

        WorkUnit::create($validated);

        return redirect()->route('work-units.index')
            ->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $workUnit = WorkUnit::where('tenant_id', $tenantId)
            ->findOrFail($id);

        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        // Ambil unit kerja lain sebagai calon parent (kecuali dirinya sendiri)
        $potentialParents = WorkUnit::where('tenant_id', $tenantId)
            ->where('id', '!=', $id)
            ->orderBy('unit_name')
            ->get();

        return view('modules.WorkUnit.form', compact('workUnit', 'users', 'potentialParents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workUnit = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $validated = $this->validateWorkUnit($request, $workUnit->id);

        $workUnit->update($validated);

        return redirect()->route('work-units.index')
            ->with('success', 'Unit kerja berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workUnit = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        // Cek apakah memiliki unit kerja anak
        if ($workUnit->children()->count() > 0) {
            return redirect()->route('work-units.index')
                ->with('error', 'Unit kerja tidak dapat dihapus karena memiliki sub-unit.');
        }

        $workUnit->delete();

        return redirect()->route('work-units.index')
            ->with('success', 'Unit kerja berhasil dihapus.');
    }

    /**
     * Validasi data unit kerja.
     */
    private function validateWorkUnit(Request $request, $id = null)
    {
        return $request->validate([
            'unit_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('work_units', 'unit_code')
                    ->where('tenant_id', Auth::user()->tenant_id)
                    ->ignore($id)
            ],
            'unit_name' => 'required|string|max:255',
            'unit_type' => 'required|in:medical,non-medical,supporting',
            'head_of_unit_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:work_units,id',
            'order' => 'nullable|integer',
        ]);
    }

    /**
     * Menampilkan dashboard untuk unit kerja tertentu.
     */
    public function dashboard(string $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $workUnit = WorkUnit::with(['headOfUnit', 'parent'])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        // Menentukan periode waktu berdasarkan parameter
        $period = request('period', 'this_month');
        $periodLabel = 'Bulan Ini';

        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        if ($period === 'all') {
            $startDate = null;
            $endDate = null;
            $periodLabel = 'Semua Data';
        } elseif ($period === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
            $periodLabel = 'Bulan Lalu';
        } elseif ($period === 'this_year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
            $periodLabel = 'Tahun Ini';
        }

        // Statistik Risiko
        $riskStats = [
            'total' => 0,
            'low' => 0,
            'medium' => 0,
            'high' => 0,
            'extreme' => 0,
            'open' => 0,
            'in_review' => 0,
            'resolved' => 0
        ];

        // Statistik Korespondensi
        $correspondenceStats = [
            'total' => 0,
            'incoming' => 0,
            'outgoing' => 0,
            'regulasi' => 0,
            'this_month' => 0
        ];

        // Statistik Dokumen
        $documentStats = [
            'total' => 0,
            'public' => 0,
            'internal' => 0,
            'confidential' => 0
        ];

        $recentActivities = collect();
        $riskReports = collect();
        $correspondences = collect();

        // Mendapatkan data dari Modul Manajemen Risiko
        try {
            if (class_exists('\\App\\Models\\RiskReport')) {
                $query = \App\Models\RiskReport::where('work_unit_id', $id);

                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }

                $riskReports = $query->latest()->limit(5)->get();

                // Statistik risiko
                $allRiskReports = \App\Models\RiskReport::where('work_unit_id', $id);
                if ($startDate && $endDate) {
                    $allRiskReports->whereBetween('created_at', [$startDate, $endDate]);
                }

                $riskStats['total'] = $allRiskReports->count();
                $riskStats['low'] = $allRiskReports->where('risk_level', 'rendah')->orWhere('risk_level', 'low')->count();
                $riskStats['medium'] = $allRiskReports->where('risk_level', 'sedang')->orWhere('risk_level', 'medium')->count();
                $riskStats['high'] = $allRiskReports->where('risk_level', 'tinggi')->orWhere('risk_level', 'high')->count();
                $riskStats['extreme'] = $allRiskReports->where('risk_level', 'ekstrem')->orWhere('risk_level', 'extreme')->count();
                $riskStats['open'] = $allRiskReports->where('status', 'open')->count();
                $riskStats['in_review'] = $allRiskReports->where('status', 'in_review')->count();
                $riskStats['resolved'] = $allRiskReports->where('status', 'resolved')->count();

                // Menambahkan aktivitas terbaru dari risiko
                foreach ($riskReports as $report) {
                    $statusClass = 'bg-primary';
                    $statusText = 'Terbuka';

                    if ($report->status == 'in_review') {
                        $statusClass = 'bg-warning';
                        $statusText = 'Dalam Peninjauan';
                    } elseif ($report->status == 'resolved') {
                        $statusClass = 'bg-success';
                        $statusText = 'Selesai';
                    }

                    $recentActivities->push((object)[
                        'title' => $report->document_title,
                        'description' => 'Laporan risiko ' . strtolower($report->risk_level),
                        'module' => 'Manajemen Risiko',
                        'created_at' => $report->created_at,
                        'status' => $report->status,
                        'status_class' => $statusClass,
                        'status_text' => $statusText,
                        'url' => route('modules.risk-management.risk-reports.show', $report->id)
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Jika modul tidak tersedia, lanjutkan saja
        }

        // Mendapatkan data dari Modul Korespondensi
        try {
            if (class_exists('\\App\\Models\\Correspondence')) {
                $query = \App\Models\Correspondence::where('work_unit_id', $id);

                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }

                $correspondences = $query->latest()->limit(5)->get();

                // Statistik korespondensi
                $allCorrespondences = \App\Models\Correspondence::where('work_unit_id', $id);

                if ($startDate && $endDate) {
                    $allCorrespondences->whereBetween('created_at', [$startDate, $endDate]);
                }

                $correspondenceStats['total'] = $allCorrespondences->count();
                $correspondenceStats['incoming'] = $allCorrespondences->where('type', 'incoming')->count();
                $correspondenceStats['outgoing'] = $allCorrespondences->where('type', 'outgoing')->count();
                $correspondenceStats['regulasi'] = $allCorrespondences->where('document_type', 'regulasi')->count();

                // Surat bulan ini
                $thisMonthCorrespondences = \App\Models\Correspondence::where('work_unit_id', $id)
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count();
                $correspondenceStats['this_month'] = $thisMonthCorrespondences;

                // Menambahkan aktivitas terbaru dari korespondensi
                foreach ($correspondences as $correspondence) {
                    $title = $correspondence->subject ?? $correspondence->document_title ?? 'Surat #' . $correspondence->id;
                    $type = $correspondence->type == 'incoming' ? 'Surat Masuk' : 'Surat Keluar';

                    $recentActivities->push((object)[
                        'title' => $title,
                        'description' => $type . ($correspondence->document_number ? ' - ' . $correspondence->document_number : ''),
                        'module' => 'Korespondensi',
                        'created_at' => $correspondence->created_at,
                        'status' => $correspondence->type,
                        'status_class' => $correspondence->type == 'incoming' ? 'bg-primary' : 'bg-info',
                        'status_text' => $correspondence->type == 'incoming' ? 'Masuk' : 'Keluar',
                        'url' => route('modules.correspondence.letters.show', $correspondence->id)
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Jika modul tidak tersedia, lanjutkan saja
        }

        // Mendapatkan data dari Modul Manajemen Dokumen
        try {
            if (class_exists('\\App\\Models\\Document')) {
                // Statistik dokumen
                $allDocuments = \App\Models\Document::where('work_unit_id', $id);

                if ($startDate && $endDate) {
                    $allDocuments->whereBetween('created_at', [$startDate, $endDate]);
                }

                $documentStats['total'] = $allDocuments->count();
                $documentStats['public'] = $allDocuments->where('confidentiality', 'public')->count();
                $documentStats['internal'] = $allDocuments->where('confidentiality', 'internal')->count();
                $documentStats['confidential'] = $allDocuments->where('confidentiality', 'confidential')->count();

                // Mendapatkan dokumen terbaru untuk aktivitas
                $recentDocuments = \App\Models\Document::where('work_unit_id', $id)
                    ->latest()
                    ->limit(5)
                    ->get();

                // Menambahkan aktivitas terbaru dari dokumen
                foreach ($recentDocuments as $document) {
                    $recentActivities->push((object)[
                        'title' => $document->title,
                        'description' => 'Dokumen ' . ($document->document_type ?? 'baru'),
                        'module' => 'Manajemen Dokumen',
                        'created_at' => $document->created_at,
                        'status' => $document->confidentiality,
                        'status_class' => $document->confidentiality == 'public' ? 'bg-success' : ($document->confidentiality == 'internal' ? 'bg-warning' : 'bg-danger'),
                        'status_text' => ucfirst($document->confidentiality),
                        'url' => route('modules.document-management.documents.show', $document->id)
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Jika modul tidak tersedia, lanjutkan saja
        }

        // Urutkan aktivitas terbaru berdasarkan waktu
        $recentActivities = $recentActivities->sortByDesc('created_at')->take(10);

        return view('modules.WorkUnit.dashboard', compact(
            'workUnit',
            'riskStats',
            'correspondenceStats',
            'documentStats',
            'riskReports',
            'correspondences',
            'recentActivities',
            'periodLabel'
        ));
    }

    /**
     * Menampilkan dashboard global untuk seluruh unit kerja.
     */
    public function globalDashboard()
    {
        $tenantId = Auth::user()->tenant_id;

        // Mengambil statistik unit kerja
        $totalWorkUnits = WorkUnit::where('tenant_id', $tenantId)->count();
        $totalActiveWorkUnits = WorkUnit::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $unitsByType = WorkUnit::where('tenant_id', $tenantId)
            ->selectRaw('unit_type, count(*) as total')
            ->groupBy('unit_type')
            ->get()
            ->pluck('total', 'unit_type')
            ->toArray();

        // Unit kerja dengan risiko tertinggi
        $highRiskUnits = [];

        // Unit kerja dengan dokumen terbanyak
        $topUnits = WorkUnit::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('modules.WorkUnit.global-dashboard', compact(
            'totalWorkUnits',
            'totalActiveWorkUnits',
            'unitsByType',
            'highRiskUnits',
            'topUnits'
        ));
    }

    /**
     * Mendapatkan ID unit kerja dan semua subunitnya secara rekursif.
     */
    private function getUnitAndSubunitIds(WorkUnit $workUnit)
    {
        $ids = [$workUnit->id];
        $children = WorkUnit::where('parent_id', $workUnit->id)->get();
        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getUnitAndSubunitIds($child));
        }
        return $ids;
    }
}
