<?php

namespace App\Http\Controllers\Modules\Correspondence;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondence;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Constructor dengan middleware untuk pemeriksaan izin
     */
    public function __construct()
    {
        // Pastikan bahwa middleware modul sudah dijalankan
        $this->middleware('module:correspondence');

        // Tambahkan middleware izin untuk setiap aksi yang perlu diproteksi
        $this->middleware('check.permission:correspondence,can_generate_reports')->only(['index', 'generate', 'export']);
    }

    /**
     * Display a listing of report options.
     */
    public function index()
    {
        return view('modules.Correspondence.reports.index');
    }

    /**
     * Generate report for preview.
     */
    public function generate(Request $request)
    {
        $tenant_id = session('tenant_id');

        // Validasi input
        $request->validate([
            'report_type' => 'required|in:daily,monthly,quarterly,yearly,custom',
            'start_date' => 'required_if:report_type,custom|date',
            'end_date' => 'required_if:report_type,custom|date|after_or_equal:start_date',
            'month' => 'required_if:report_type,monthly|integer|between:1,12',
            'year' => 'required_if:report_type,monthly,quarterly,yearly|integer',
            'quarter' => 'required_if:report_type,quarterly|integer|between:1,4',
            'document_type' => 'nullable|in:all,Regulasi,Bukti',
            'confidentiality_level' => 'nullable|in:all,Internal,Publik,Rahasia',
        ]);

        // Menentukan rentang tanggal
        $startDate = null;
        $endDate = null;

        switch ($request->report_type) {
            case 'daily':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;

            case 'monthly':
                $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                break;

            case 'quarterly':
                $startMonth = ($request->quarter - 1) * 3 + 1;
                $startDate = Carbon::createFromDate($request->year, $startMonth, 1)->startOfMonth();
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                break;

            case 'yearly':
                $startDate = Carbon::createFromDate($request->year, 1, 1)->startOfYear();
                $endDate = $startDate->copy()->endOfYear();
                break;

            case 'custom':
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                break;
        }

        // Base query
        $query = Correspondence::where('tenant_id', $tenant_id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter berdasarkan tipe dokumen jika dipilih
        if ($request->document_type && $request->document_type != 'all') {
            $query->where('document_type', $request->document_type);
        }

        // Filter berdasarkan level kerahasiaan jika dipilih
        if ($request->confidentiality_level && $request->confidentiality_level != 'all') {
            $query->where('confidentiality_level', $request->confidentiality_level);
        }

        // Mendapatkan hasil
        $correspondences = $query->orderBy('created_at', 'desc')->get();

        // Menghitung statistik
        $totalCorrespondences = $correspondences->count();
        $totalByDocumentType = $correspondences->groupBy('document_type')
            ->map(function ($items) {
                return $items->count();
            });

        $totalByConfidentialityLevel = $correspondences->groupBy('confidentiality_level')
            ->map(function ($items) {
                return $items->count();
            });

        // Data untuk grafik
        $createdByDay = $correspondences->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        })->map(function ($items) {
            return $items->count();
        });

        // Menyiapkan data untuk tampilan laporan
        $reportTitle = $this->generateReportTitle($request->report_type, $startDate, $endDate, $request);
        $reportPeriod = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');

        $stats = [
            'total' => $totalCorrespondences,
            'by_document_type' => $totalByDocumentType,
            'by_confidentiality_level' => $totalByConfidentialityLevel
        ];

        $chartData = [
            'labels' => $createdByDay->keys()->toArray(),
            'counts' => $createdByDay->values()->toArray()
        ];

        // Menyimpan parameter untuk ekspor (batasi agar session payload kecil)
        session(['report_params' => $request->only([
            'report_type',
            'start_date',
            'end_date',
            'month',
            'year',
            'quarter',
            'document_type',
            'confidentiality_level',
        ])]);

        return view('modules.Correspondence.reports.preview', compact(
            'correspondences',
            'stats',
            'chartData',
            'reportTitle',
            'reportPeriod'
        ));
    }

    /**
     * Export report to PDF or Excel.
     */
    public function export(Request $request)
    {
        // Validasi input
        $request->validate([
            'format' => 'required|in:pdf,excel',
        ]);

        // Mendapatkan parameter laporan dari session
        $reportParams = session('report_params');

        if (!$reportParams) {
            return redirect()->route('modules.correspondence.reports.index')
                ->with('error', 'Parameter laporan tidak ditemukan. Silakan generate laporan terlebih dahulu.');
        }

        // Memanggil method generate kembali untuk mendapatkan data
        $request->merge($reportParams);
        $result = $this->generate($request);

        if ($request->format == 'pdf') {
            // Generate PDF
            $html = view('modules.Correspondence.reports.pdf', [
                'correspondences' => $result->getData()['correspondences'],
                'stats' => $result->getData()['stats'],
                'reportTitle' => $result->getData()['reportTitle'],
                'reportPeriod' => $result->getData()['reportPeriod']
            ])->render();

            // Setup PDF
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Stream PDF
            return $dompdf->stream('Laporan_Korespondensi_' . Carbon::now()->format('Ymd_His') . '.pdf');
        } else {
            // Generate Excel (implementasi dasar, bisa menggunakan library seperti PhpSpreadsheet)
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="Laporan_Korespondensi_' . Carbon::now()->format('Ymd_His') . '.csv"',
            ];

            $correspondences = $result->getData()['correspondences'];

            $callback = function () use ($correspondences) {
                $file = fopen('php://output', 'w');

                // Header
                fputcsv($file, [
                    'Nomor Dokumen',
                    'Judul Dokumen',
                    'Tipe Dokumen',
                    'Tanggal Dokumen',
                    'Level Kerahasiaan',
                    'Subjek',
                    'Pengirim',
                    'Penerima',
                    'Tanggal Dibuat'
                ]);

                // Data
                foreach ($correspondences as $correspondence) {
                    fputcsv($file, [
                        $correspondence->document_number,
                        $correspondence->document_title,
                        $correspondence->document_type,
                        $correspondence->document_date,
                        $correspondence->confidentiality_level,
                        $correspondence->subject,
                        $correspondence->sender_name . ' (' . $correspondence->sender_position . ')',
                        $correspondence->recipient_name . ' (' . $correspondence->recipient_position . ')',
                        $correspondence->created_at->format('d/m/Y H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    /**
     * Generate report title based on parameters.
     */
    private function generateReportTitle($reportType, $startDate, $endDate, $request)
    {
        $title = 'Laporan Korespondensi - ';

        switch ($reportType) {
            case 'daily':
                $title .= 'Harian: ' . Carbon::now()->format('d/m/Y');
                break;

            case 'monthly':
                $months = [
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember'
                ];
                $title .= 'Bulanan: ' . $months[$request->month] . ' ' . $request->year;
                break;

            case 'quarterly':
                $title .= 'Triwulan ' . $request->quarter . ' Tahun ' . $request->year;
                break;

            case 'yearly':
                $title .= 'Tahunan: ' . $request->year;
                break;

            case 'custom':
                $title .= 'Kustom: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
                break;
        }

        return $title;
    }
}
