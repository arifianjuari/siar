<?php

namespace Modules\WorkUnit\Http\Controllers;

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
        return view('work-unit::index', compact('flattenedWorkUnits'))->with('debug_message', 'View loaded at ' . now());
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

        return view('work-unit::form', compact('users', 'potentialParents'));
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

        return view('work-unit::form', compact('workUnit', 'users', 'potentialParents'));
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
        $workUnit = WorkUnit::with([
            'parent.headOfUnit',
            'parent.users.role',
            'children.headOfUnit',
            'children.users.role',
            'children.children.headOfUnit',
            'children.children.users.role',
            'children.children.children.headOfUnit',
            'children.children.children.users.role',
            'headOfUnit',
            'users.role'
        ])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        // Ambil statistik risiko
        $riskStats = [
            'total' => $workUnit->riskReports()->count(),
            // ... existing stats ...
        ];

        // Ambil statistik korespondensi
        $correspondenceStats = [
            'total' => $workUnit->correspondences()->count(),
            // ... existing stats ...
        ];

        // Ambil data risiko terbaru
        $riskReports = $workUnit->riskReports()
            ->latest()
            ->take(5)
            ->get();

        // Ambil data korespondensi terbaru
        $correspondences = $workUnit->correspondences()
            ->latest()
            ->take(5)
            ->get();

        // Ambil aktivitas terbaru
        $period = request('period', 'this_month');
        $recentActivities = $this->getRecentActivities($workUnit, $period);
        $periodLabel = $this->getPeriodLabel($period);

        // Kelompokkan pengguna berdasarkan peran untuk setiap unit
        $workUnit->users_by_role = $this->groupUsersByRole($workUnit->users);
        if ($workUnit->parent) {
            $workUnit->parent->users_by_role = $this->groupUsersByRole($workUnit->parent->users);
        }

        // Rekursif untuk semua anak dan turunannya
        $this->processUnitHierarchy($workUnit->children);

        return view('work-unit::dashboard', compact(
            'workUnit',
            'riskStats',
            'correspondenceStats',
            'riskReports',
            'correspondences',
            'recentActivities',
            'periodLabel'
        ));
    }

    /**
     * Memproses hierarki unit kerja secara rekursif.
     */
    private function processUnitHierarchy($units)
    {
        if ($units->isEmpty()) {
            return;
        }

        foreach ($units as $unit) {
            // Kelompokkan pengguna untuk unit ini
            $unit->users_by_role = $this->groupUsersByRole($unit->users);

            // Proses anak-anaknya secara rekursif
            if ($unit->children && $unit->children->isNotEmpty()) {
                $this->processUnitHierarchy($unit->children);
            }
        }
    }

    /**
     * Mengelompokkan pengguna berdasarkan peran mereka.
     */
    private function groupUsersByRole($users)
    {
        $groupedUsers = [];
        foreach ($users as $user) {
            if ($user->role) {
                $roleName = $user->role->name;
                if (!isset($groupedUsers[$roleName])) {
                    $groupedUsers[$roleName] = [];
                }
                $groupedUsers[$roleName][] = $user;
            }
        }
        return $groupedUsers;
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

        return view('work-unit::global-dashboard', compact(
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

    /**
     * Mendapatkan aktivitas terbaru berdasarkan periode.
     */
    private function getRecentActivities(WorkUnit $workUnit, string $period)
    {
        $startDate = null;
        $endDate = null;

        switch ($period) {
            case 'this_month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
        }

        $recentActivities = collect();

        // Mendapatkan data dari Modul Manajemen Risiko
        try {
            if (class_exists('\\App\\Models\\RiskReport')) {
                $query = $workUnit->riskReports();
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                $riskReports = $query->latest()->take(5)->get();

                foreach ($riskReports as $report) {
                    $statusClass = match ($report->status) {
                        'in_review' => 'bg-warning',
                        'resolved' => 'bg-success',
                        default => 'bg-primary'
                    };

                    $statusText = match ($report->status) {
                        'in_review' => 'Dalam Peninjauan',
                        'resolved' => 'Selesai',
                        default => 'Terbuka'
                    };

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
                $query = $workUnit->correspondences();
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                $correspondences = $query->latest()->take(5)->get();

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

        return $recentActivities->sortByDesc('created_at')->take(10);
    }

    /**
     * Mendapatkan label periode berdasarkan filter yang dipilih.
     */
    private function getPeriodLabel(string $period): string
    {
        return match ($period) {
            'all' => 'Semua Data',
            'last_month' => 'Bulan Lalu',
            'this_year' => 'Tahun Ini',
            default => 'Bulan Ini'
        };
    }
}
