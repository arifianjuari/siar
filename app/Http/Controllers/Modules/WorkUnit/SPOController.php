<?php

namespace App\Http\Controllers\Modules\WorkUnit;

use App\Http\Controllers\Controller;
use App\Models\SPO;
use App\Models\WorkUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SPOController extends Controller
{
    /**
     * Get roman numeral for a given month.
     */
    private function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romans[$month] ?? '';
    }

    /**
     * Generate a suggested document number.
     */
    private function generateDocumentNumber(): string
    {
        // Hitung jumlah SPO yang ada di tenant ini untuk mendapatkan nomor urut
        $count = SPO::where('tenant_id', Auth::user()->tenant_id)->count();
        $sequenceNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        // Format: [nomor urut]/[bulan romawi]/[tahun]/SPO
        return $sequenceNumber . '/' . $this->getRomanMonth(date('n')) . '/' . date('Y') . '/SPO';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $workUnits = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('unit_name', 'asc')
            ->get();

        $query = SPO::with(['workUnit', 'approver', 'creator'])
            ->where('tenant_id', Auth::user()->tenant_id);

        // Filter by work unit if specified
        if ($request->has('work_unit_id') && $request->work_unit_id) {
            $query->where('work_unit_id', $request->work_unit_id);
        }

        // Filter by document type if specified
        if ($request->has('document_type') && $request->document_type) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by status if specified
        if ($request->has('status') && $request->status) {
            $query->where('status_validasi', $request->status);
        }

        // Search by title or document number
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('document_title', 'like', "%{$searchTerm}%")
                    ->orWhere('document_number', 'like', "%{$searchTerm}%");
            });
        }

        // Urutkan berdasarkan tanggal update terbaru
        $spos = $query->orderBy('updated_at', 'desc')->paginate(10);

        $documentTypes = [
            'Kebijakan' => 'Kebijakan',
            'Pedoman' => 'Pedoman',
            'SPO' => 'SPO',
            'Perencanaan' => 'Perencanaan',
            'Program' => 'Program'
        ];

        $statusValidasi = [
            'Draft' => 'Draft',
            'Disetujui' => 'Disetujui',
            'Kadaluarsa' => 'Kadaluarsa',
            'Revisi' => 'Revisi'
        ];

        return view('modules.work-unit.spo.index', compact('spos', 'workUnits', 'documentTypes', 'statusValidasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $workUnits = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('unit_name', 'asc')
            ->get();

        $linkedUnits = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('unit_name', 'asc')
            ->get();

        $documentTypes = [
            'Kebijakan' => 'Kebijakan',
            'Pedoman' => 'Pedoman',
            'SPO' => 'SPO',
            'Perencanaan' => 'Perencanaan',
            'Program' => 'Program'
        ];

        $confidentialityLevels = [
            'Internal' => 'Internal',
            'Publik' => 'Publik',
            'Rahasia' => 'Rahasia'
        ];

        // Default values
        $defaultValues = [
            'document_type' => 'SPO',
            'document_version' => '1.0',
            'document_number' => $this->generateDocumentNumber(),
            'document_date' => date('Y-m-d'),
            'confidentiality_level' => 'Internal',
            'review_cycle_months' => 12
        ];

        return view('modules.work-unit.spo.create', compact(
            'workUnits',
            'linkedUnits',
            'documentTypes',
            'confidentialityLevels',
            'defaultValues'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'work_unit_id' => 'required|exists:work_units,id',
            'document_title' => 'required|string|max:255',
            'document_type' => ['required', Rule::in(['Kebijakan', 'Pedoman', 'SPO', 'Perencanaan', 'Program'])],
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'document_version' => 'required|string|max:10',
            'confidentiality_level' => ['required', Rule::in(['Internal', 'Publik', 'Rahasia'])],
            'file_url' => 'nullable|url|max:255',
            'review_cycle_months' => 'required|integer|min:1|max:60',
            'definition' => 'nullable|string',
            'purpose' => 'nullable|string',
            'policy' => 'nullable|string',
            'procedure' => 'nullable|string',
            'reference' => 'nullable|string',
            'linked_unit' => 'nullable|array',
            'linked_unit.*' => 'exists:work_units,id',
        ]);

        try {
            DB::beginTransaction();

            // Hitung next review date
            $nextReview = Carbon::parse($request->document_date)
                ->addMonths($request->review_cycle_months);

            // Create SPO record
            $spo = new SPO();
            $spo->id = Str::uuid();
            $spo->tenant_id = Auth::user()->tenant_id;
            $spo->work_unit_id = $request->work_unit_id;
            $spo->document_title = $request->document_title;
            $spo->document_type = $request->document_type;
            $spo->document_number = $request->document_number;
            $spo->document_date = $request->document_date;
            $spo->document_version = $request->document_version;
            $spo->confidentiality_level = $request->confidentiality_level;
            $spo->file_path = $request->file_url; // Simpan URL dokumen
            $spo->next_review = $nextReview;
            $spo->review_cycle_months = $request->review_cycle_months;
            $spo->status_validasi = 'Draft';
            $spo->definition = $request->definition;
            $spo->purpose = $request->purpose;
            $spo->policy = $request->policy;
            $spo->procedure = $request->procedure;
            $spo->reference = $request->reference;
            $spo->linked_unit = $request->has('linked_unit') ? json_encode($request->linked_unit) : null;
            $spo->created_by = Auth::id();
            $spo->save();

            DB::commit();

            return redirect()->route('work-units.spo.index')
                ->with('success', 'Dokumen SPO berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SPO $spo)
    {
        $spo->load(['workUnit', 'approver', 'creator']);

        // Ambil linked work units
        $linkedUnits = collect([]);
        if ($spo->linked_unit) {
            $linkedUnitIds = json_decode($spo->linked_unit);
            $linkedUnits = WorkUnit::whereIn('id', $linkedUnitIds)->get();
        }

        return view('modules.work-unit.spo.show', compact('spo', 'linkedUnits'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SPO $spo)
    {
        // Check if user is authorized to edit this SPO using policy
        $this->authorize('update', $spo);

        $workUnits = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('unit_name', 'asc')
            ->get();

        $linkedUnits = WorkUnit::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_active', true)
            ->orderBy('unit_name', 'asc')
            ->get();

        $documentTypes = [
            'Kebijakan' => 'Kebijakan',
            'Pedoman' => 'Pedoman',
            'SPO' => 'SPO',
            'Perencanaan' => 'Perencanaan',
            'Program' => 'Program'
        ];

        $confidentialityLevels = [
            'Internal' => 'Internal',
            'Publik' => 'Publik',
            'Rahasia' => 'Rahasia'
        ];

        $statusValidasi = [
            'Draft' => 'Draft',
            'Disetujui' => 'Disetujui',
            'Kadaluarsa' => 'Kadaluarsa',
            'Revisi' => 'Revisi'
        ];

        // Decode linked units untuk form
        $spoLinkedUnits = $spo->linked_unit ? json_decode($spo->linked_unit) : [];

        return view('modules.work-unit.spo.edit', compact(
            'spo',
            'workUnits',
            'linkedUnits',
            'documentTypes',
            'confidentialityLevels',
            'statusValidasi',
            'spoLinkedUnits'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SPO $spo)
    {
        // Check if user is authorized to update this SPO using policy
        $this->authorize('update', $spo);

        $request->validate([
            'work_unit_id' => 'required|exists:work_units,id',
            'document_title' => 'required|string|max:255',
            'document_type' => ['required', Rule::in(['Kebijakan', 'Pedoman', 'SPO', 'Perencanaan', 'Program'])],
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'document_version' => 'required|string|max:10',
            'confidentiality_level' => ['required', Rule::in(['Internal', 'Publik', 'Rahasia'])],
            'file_url' => 'nullable|url|max:255',
            'review_cycle_months' => 'required|integer|min:1|max:60',
            'status_validasi' => ['required', Rule::in(['Draft', 'Disetujui', 'Kadaluarsa', 'Revisi'])],
            'definition' => 'nullable|string',
            'purpose' => 'nullable|string',
            'policy' => 'nullable|string',
            'procedure' => 'nullable|string',
            'reference' => 'nullable|string',
            'linked_unit' => 'nullable|array',
            'linked_unit.*' => 'exists:work_units,id',
        ]);

        try {
            DB::beginTransaction();

            // Hitung next review date berdasarkan tanggal dokumen baru
            $nextReview = Carbon::parse($request->document_date)
                ->addMonths($request->review_cycle_months);

            // Update SPO data
            $spo->work_unit_id = $request->work_unit_id;
            $spo->document_title = $request->document_title;
            $spo->document_type = $request->document_type;
            $spo->document_number = $request->document_number;
            $spo->document_date = $request->document_date;
            $spo->document_version = $request->document_version;
            $spo->confidentiality_level = $request->confidentiality_level;
            $spo->file_path = $request->file_url; // Update URL dokumen
            $spo->next_review = $nextReview;
            $spo->review_cycle_months = $request->review_cycle_months;
            $spo->status_validasi = $request->status_validasi;
            $spo->definition = $request->definition;
            $spo->purpose = $request->purpose;
            $spo->policy = $request->policy;
            $spo->procedure = $request->procedure;
            $spo->reference = $request->reference;
            $spo->linked_unit = $request->has('linked_unit') ? json_encode($request->linked_unit) : null;

            // Jika status berubah menjadi Disetujui, catat siapa yang menyetujui dan kapan
            if ($request->status_validasi == 'Disetujui' && $spo->status_validasi != 'Disetujui') {
                $spo->approved_by = Auth::id();
                $spo->approved_at = now();
            }

            $spo->save();

            DB::commit();

            return redirect()->route('work-units.spo.index')
                ->with('success', 'Dokumen SPO berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SPO $spo)
    {
        // Check if user is authorized to delete this SPO using policy
        $this->authorize('delete', $spo);

        try {
            // Delete SPO record
            $spo->delete();

            return redirect()->route('work-units.spo.index')
                ->with('success', 'Dokumen SPO berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for the specified SPO.
     */
    public function generatePdf(SPO $spo)
    {
        $spo->load(['workUnit', 'approver', 'creator']);

        // Ambil data tenant dari user yang aktif
        $tenant = Auth::user()->tenant;

        // Ambil linked work units
        $linkedUnits = collect([]);
        if ($spo->linked_unit) {
            $linkedUnitIds = json_decode($spo->linked_unit);
            $linkedUnits = WorkUnit::whereIn('id', $linkedUnitIds)->get();
        }

        // Buat QR code untuk SPO
        $qrContent = "=== STANDAR PROSEDUR OPERASIONAL ===\n\n";
        $qrContent .= "Nomor: {$spo->document_number}\n";
        $qrContent .= "Judul: {$spo->document_title}\n";
        $qrContent .= "Versi: {$spo->document_version}\n";
        $qrContent .= "Tgl. Berlaku: " . $spo->document_date->format('d/m/Y') . "\n";
        $qrContent .= "Tgl. Revisi: " . $spo->updated_at->format('d/m/Y') . "\n";
        $qrContent .= "Unit: " . ($spo->workUnit ? $spo->workUnit->unit_name : 'N/A') . "\n";
        $qrContent .= "URL: " . route('work-units.spo.show', $spo);

        // Generate QR code dengan format SVG dan konversi ke base64
        $svgQrCode = QrCode::format('svg')->size(200)->generate($qrContent);
        $base64 = base64_encode($svgQrCode);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . $base64;

        // Format data untuk tampilan PDF
        $data = [
            'spo' => $spo,
            'linkedUnits' => $linkedUnits,
            'tenant' => $tenant,
            'workUnit' => $spo->workUnit,
            'approver' => $spo->approver,
            'creator' => $spo->creator,
            'currentDate' => now()->format('d/m/Y'),
            'qrCodeDataUri' => $qrCodeDataUri
        ];

        // Generate PDF using dompdf
        $pdf = Pdf::loadView('modules.work-unit.spo.pdf', $data);

        // Set paper size to A4
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = Str::slug($spo->document_title) . '.pdf';

        // Download PDF
        return $pdf->download($filename);
    }

    /**
     * Generate QR code for the SPO.
     */
    public function generateQr(SPO $spo)
    {
        // Buat konten QR code dalam format teks yang lebih mudah dibaca
        $qrContent = "=== STANDAR PROSEDUR OPERASIONAL ===\n\n";
        $qrContent .= "Nomor: {$spo->document_number}\n";
        $qrContent .= "Judul: {$spo->document_title}\n";
        $qrContent .= "Versi: {$spo->document_version}\n";
        $qrContent .= "Tgl. Berlaku: " . $spo->document_date->format('d/m/Y') . "\n";
        $qrContent .= "Tgl. Revisi: " . $spo->updated_at->format('d/m/Y') . "\n";
        $qrContent .= "Unit: " . ($spo->workUnit ? $spo->workUnit->unit_name : 'N/A') . "\n";
        $qrContent .= "URL: " . route('work-units.spo.show', $spo);

        // Coba alternatif pembuatan QR code - paling sederhana
        return response(
            QrCode::format('svg')->size(200)->generate($qrContent)
        )->header('Content-Type', 'image/svg+xml');
    }
}
