<?php

namespace Modules\WorkUnit\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkUnitFixController extends Controller
{
    /**
     * Display the fixed work units page.
     * This is a temporary solution to display the correct work unit list.
     */
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info('WorkUnitFixController@index dipanggil', [
            'user_id' => Auth::id(),
            'tenant_id' => $tenantId,
            'time' => now()->toDateTimeString()
        ]);

        // Ambil semua unit kerja untuk tenant ini
        $allUnits = WorkUnit::with('headOfUnit')
            ->where('tenant_id', $tenantId)
            ->orderBy('order')->orderBy('unit_name') // Urutkan berdasarkan order, lalu nama
            ->get();

        // Tampilkan informasi tentang unit yang ditemukan
        \Illuminate\Support\Facades\Log::info('Unit kerja ditemukan', [
            'count' => $allUnits->count(),
            'unit_ids' => $allUnits->pluck('id')->toArray(),
            'unit_names' => $allUnits->pluck('unit_name')->toArray()
        ]);

        // Bangun struktur hierarki
        $workUnitsHierarchy = $this->buildHierarchy($allUnits);

        // Fungsi rekursif untuk meratakan hierarki dengan level kedalaman
        $flattenedWorkUnits = $this->flattenHierarchy($workUnitsHierarchy);

        // Return view langsung tanpa kondisi-kondisi lain
        return view('work-unit::fixed-index', [
            'flattenedWorkUnits' => $flattenedWorkUnits,
            'debug_message' => 'Fixed view loaded at ' . now()
        ]);
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
     * Menampilkan dashboard untuk unit kerja tertentu.
     */
    public function dashboard(string $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $workUnit = WorkUnit::where('tenant_id', $tenantId)
            ->with(['headOfUnit', 'parent']) // Load relasi yang mungkin diperlukan
            ->findOrFail($id);

        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info('WorkUnitFixController@dashboard dipanggil', [
            'work_unit_id' => $id,
            'work_unit_name' => $workUnit->unit_name,
            'user_id' => Auth::id(),
            'time' => now()->toDateTimeString()
        ]);

        // Ambil ID unit ini dan semua subunitnya secara rekursif
        $unitIds = $this->getUnitAndSubunitIds($workUnit);

        // Hitung jumlah laporan berdasarkan tingkat risiko untuk unit ini
        $riskStats = [
            'total' => 5,  // Temporary data
            'low' => 2,
            'medium' => 1,
            'high' => 1,
            'extreme' => 1,
            'open' => 2,
            'in_review' => 1,
            'resolved' => 2
        ];

        // Statistik korespondensi (data sementara)
        $correspondenceStats = [
            'total' => 3,
            'incoming' => 2,
            'outgoing' => 1,
            'regulasi' => 0,
            'this_month' => 1
        ];

        // Data dokumen berdasarkan tingkat kerahasiaan
        $documentStats = [
            'public' => 1,
            'internal' => 2,
            'confidential' => 0
        ];

        // Data dummy untuk tabel
        $riskReports = collect([
            (object)[
                'id' => 1,
                'document_title' => 'Laporan Risiko #1',
                'risk_level' => 'Tinggi',
                'status' => 'open',
                'created_at' => now()->subDays(5),
                'analysis' => true
            ],
            (object)[
                'id' => 2,
                'document_title' => 'Laporan Risiko #2',
                'risk_level' => 'Rendah',
                'status' => 'resolved',
                'created_at' => now()->subDays(10),
                'analysis' => false
            ],
        ]);

        // Data korespondensi dummy
        $correspondences = collect([
            (object)[
                'id' => 1,
                'subject' => 'Surat Pemberitahuan',
                'document_number' => 'KSP/2025/001',
                'document_type' => 'Surat',
                'document_date' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'type' => 'incoming'
            ],
            (object)[
                'id' => 2,
                'subject' => 'Surat Balasan',
                'document_number' => 'KSP/2025/002',
                'document_type' => 'Surat',
                'document_date' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'type' => 'outgoing'
            ],
        ]);

        // Label periode waktu
        $periodLabel = 'Data 30 hari terakhir';

        return view('work-unit::dashboard', compact(
            'workUnit',
            'riskReports',
            'riskStats',
            'correspondences',
            'correspondenceStats',
            'documentStats',
            'periodLabel'
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
