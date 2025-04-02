<?php

namespace App\Http\Controllers\Modules\Correspondence;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondence;
use App\Models\Tag;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CorrespondenceController extends Controller
{
    /**
     * Constructor dengan middleware untuk pemeriksaan izin
     */
    public function __construct()
    {
        // Pastikan bahwa middleware modul sudah dijalankan
        $this->middleware('module:correspondence');

        // Tambahkan middleware izin untuk setiap aksi yang perlu diproteksi
        $this->middleware('check.permission:correspondence,can_create')->only(['create', 'store']);
        $this->middleware('check.permission:correspondence,can_edit')->only(['edit', 'update']);
        $this->middleware('check.permission:correspondence,can_delete')->only('destroy');
        $this->middleware('check.permission:correspondence,can_export')->only(['exportPdf', 'exportWord']);
    }

    /**
     * Display dashboard with stats and charts.
     */
    public function dashboard()
    {
        $tenant_id = session('tenant_id');

        // Statistik dasar
        $totalCorrespondences = Correspondence::where('tenant_id', $tenant_id)->count();
        $totalRegulasi = Correspondence::where('tenant_id', $tenant_id)
            ->where('document_type', 'Regulasi')
            ->count();
        $totalBukti = Correspondence::where('tenant_id', $tenant_id)
            ->where('document_type', 'Bukti')
            ->count();

        // Data untuk grafik bulanan
        $monthlyData = [];
        $monthLabels = [];

        // Gunakan startOfMonth untuk memastikan konsistensi
        $startDate = Carbon::now()->startOfMonth()->subMonths(11);

        for ($i = 0; $i < 12; $i++) {
            $currentDate = (clone $startDate)->addMonths($i);
            $monthName = $currentDate->translatedFormat('M');
            $monthLabels[] = $monthName;

            $monthlyData[] = Correspondence::where('tenant_id', $tenant_id)
                ->whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->count();
        }

        // Stats untuk dashboard
        $stats = [
            'total' => $totalCorrespondences,
            'regulasi' => $totalRegulasi,
            'bukti' => $totalBukti
        ];

        // Data untuk chart
        $chartData = [
            'labels' => $monthLabels,
            'datasets' => [
                [
                    'label' => 'Surat/Nota Dinas',
                    'data' => $monthlyData
                ]
            ]
        ];

        return view('modules.Correspondence.dashboard', compact('stats', 'chartData'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenant_id = session('tenant_id');

        // Base query
        $query = Correspondence::with('tags')->where('tenant_id', $tenant_id);

        // Filter berdasarkan tag
        if ($request->filled('tag')) {
            $tagSlug = $request->input('tag');
            $tag = Tag::where('slug', $tagSlug)
                ->where('tenant_id', $tenant_id)
                ->first();

            if ($tag) {
                $correspondenceIds = $tag->correspondences()->pluck('correspondences.id');
                $query->whereIn('id', $correspondenceIds);
            }
        }

        // Filter lainnya
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('confidentiality_level')) {
            $query->where('confidentiality_level', $request->confidentiality_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_title', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $correspondences = $query->paginate(10);

        return view('modules.Correspondence.letters.index', compact('correspondences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenant_id = session('tenant_id');
        $tags = Tag::where('tenant_id', $tenant_id)->orderBy('name')->get();
        $users = User::where('tenant_id', $tenant_id)->orderBy('name')->get();

        return view('modules.Correspondence.letters.create', compact('tags', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenant_id = session('tenant_id');

        // Validasi input
        $validator = Validator::make($request->all(), [
            'document_title' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:100',
            'document_type' => 'required|in:Regulasi,Bukti',
            'document_version' => 'required|string|max:20',
            'document_date' => 'required|date',
            'confidentiality_level' => 'required|in:Internal,Publik,Rahasia',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'sender_name' => 'required|string|max:255',
            'sender_position' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_position' => 'required|string|max:255',
            'cc_list' => 'nullable|string',
            'signed_at_location' => 'required|string|max:255',
            'signed_at_date' => 'required|date',
            'signatory_name' => 'required|string|max:255',
            'signatory_position' => 'required|string|max:255',
            'signatory_rank' => 'nullable|string|max:255',
            'signatory_nrp' => 'nullable|string|max:100',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'signature_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'document_link' => 'nullable|string|url|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create new correspondence
        $correspondence = new Correspondence();
        $correspondence->tenant_id = $tenant_id;
        $correspondence->document_title = $request->document_title;
        $correspondence->document_number = $request->document_number;
        $correspondence->document_type = $request->document_type;
        $correspondence->document_version = $request->document_version;
        $correspondence->document_date = $request->document_date;
        $correspondence->confidentiality_level = $request->confidentiality_level;
        $correspondence->subject = $request->subject;
        $correspondence->body = $request->body;
        $correspondence->reference_to = $request->reference_to;
        $correspondence->sender_name = $request->sender_name;
        $correspondence->sender_position = $request->sender_position;
        $correspondence->recipient_name = $request->recipient_name;
        $correspondence->recipient_position = $request->recipient_position;
        $correspondence->cc_list = $request->cc_list;
        $correspondence->signed_at_location = $request->signed_at_location;
        $correspondence->signed_at_date = $request->signed_at_date;
        $correspondence->signatory_name = $request->signatory_name;
        $correspondence->signatory_position = $request->signatory_position;
        $correspondence->signatory_rank = $request->signatory_rank;
        $correspondence->signatory_nrp = $request->signatory_nrp;
        $correspondence->created_by = Auth::id();
        $correspondence->file_path = null;
        $correspondence->signature_file = null;
        $correspondence->document_link = $request->document_link;

        // Upload document file if provided
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('correspondences', $fileName, 'public');
            $correspondence->file_path = $filePath;
        }

        // Upload signature file if provided
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $fileName = time() . '_signature_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('signatures', $fileName, 'public');
            $correspondence->signature_file = $filePath;
        }

        $correspondence->save();

        // === Perubahan Penanganan Tag Mulai ===
        $tagIds = [];
        if ($request->has('tags')) {
            foreach ($request->tags as $tagName) {
                // Cari tag berdasarkan nama dan tenant_id, atau buat jika tidak ada
                $tag = Tag::firstOrCreate(
                    [
                        'name' => trim($tagName),
                        'tenant_id' => $tenant_id
                    ],
                    [
                        'slug' => Str::slug(trim($tagName)), // Buat slug otomatis
                        'tenant_id' => $tenant_id
                    ]
                );
                $tagIds[] = $tag->id;
            }
        }

        // Sync tags berdasarkan ID yang terkumpul
        $correspondence->tags()->sync($tagIds);
        // === Perubahan Penanganan Tag Selesai ===

        // Attach documents if provided
        if ($request->has('document_ids')) {
            $documentData = [];
            foreach ($request->document_ids as $docId) {
                $documentData[$docId] = ['relation_type' => 'related'];
            }
            $correspondence->documents()->sync($documentData);
        }

        // Redirect dengan pesan sukses
        return redirect()
            ->route('modules.correspondence.letters.show', $correspondence->id)
            ->with('success', 'Surat berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant_id = session('tenant_id');

        // Debug: enable query logging
        DB::enableQueryLog();

        $correspondence = Correspondence::with(['tags', 'creator'])
            ->where('tenant_id', $tenant_id)
            ->findOrFail($id);

        // Debug: get query log
        $queryLog = DB::getQueryLog();
        Log::debug('Show correspondence query log', $queryLog);

        // Cek apakah sudah ada tag untuk tenant ini
        $existingTagCount = DB::table('tags')->where('tenant_id', $tenant_id)->count();

        // Jika tidak ada tag, buat satu tag default
        if ($existingTagCount == 0) {
            $tag = new Tag();
            $tag->name = 'Surat Penting';
            $tag->slug = Str::slug('Surat Penting');
            $tag->tenant_id = $tenant_id;
            $tag->order = 1;
            $tag->save();

            Log::debug('Tag default dibuat karena tidak ada tag', ['tag_id' => $tag->id, 'tag_name' => $tag->name]);

            // Lampirkan tag ke correspondence
            try {
                DB::table('document_tag')->insert([
                    'tag_id' => $tag->id,
                    'document_id' => $correspondence->id,
                    'document_type' => 'App\\Models\\Correspondence',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::debug('Tag default dilampirkan ke correspondence dengan cara manual', [
                    'tag_id' => $tag->id,
                    'correspondence_id' => $correspondence->id
                ]);

                // Muat ulang relasi
                $correspondence->load('tags');
            } catch (\Exception $e) {
                Log::error('Gagal melampirkan tag ke correspondence', [
                    'tag_id' => $tag->id,
                    'correspondence_id' => $correspondence->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('modules.Correspondence.letters.show', compact('correspondence'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenant_id = session('tenant_id');
        $correspondence = Correspondence::where('tenant_id', $tenant_id)->findOrFail($id);
        $tags = Tag::where('tenant_id', $tenant_id)->orderBy('name')->get();
        $users = User::where('tenant_id', $tenant_id)->orderBy('name')->get();

        return view('modules.Correspondence.letters.edit', compact('correspondence', 'tags', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tenant_id = session('tenant_id');

        // Find the correspondence
        $correspondence = Correspondence::where('tenant_id', $tenant_id)->findOrFail($id);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'document_title' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:100',
            'document_type' => 'required|in:Regulasi,Bukti',
            'document_version' => 'required|string|max:20',
            'document_date' => 'required|date',
            'confidentiality_level' => 'required|in:Internal,Publik,Rahasia',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'sender_name' => 'required|string|max:255',
            'sender_position' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_position' => 'required|string|max:255',
            'cc_list' => 'nullable|string',
            'signed_at_location' => 'required|string|max:255',
            'signed_at_date' => 'required|date',
            'signatory_name' => 'required|string|max:255',
            'signatory_position' => 'required|string|max:255',
            'signatory_rank' => 'nullable|string|max:255',
            'signatory_nrp' => 'nullable|string|max:100',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'signature_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'document_link' => 'nullable|string|url|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update correspondence
        $correspondence->document_title = $request->document_title;
        $correspondence->document_number = $request->document_number;
        $correspondence->document_type = $request->document_type;
        $correspondence->document_version = $request->document_version;
        $correspondence->document_date = $request->document_date;
        $correspondence->confidentiality_level = $request->confidentiality_level;
        $correspondence->subject = $request->subject;
        $correspondence->body = $request->body;
        $correspondence->reference_to = $request->reference_to;
        $correspondence->sender_name = $request->sender_name;
        $correspondence->sender_position = $request->sender_position;
        $correspondence->recipient_name = $request->recipient_name;
        $correspondence->recipient_position = $request->recipient_position;
        $correspondence->cc_list = $request->cc_list;
        $correspondence->signed_at_location = $request->signed_at_location;
        $correspondence->signed_at_date = $request->signed_at_date;
        $correspondence->signatory_name = $request->signatory_name;
        $correspondence->signatory_position = $request->signatory_position;
        $correspondence->signatory_rank = $request->signatory_rank;
        $correspondence->signatory_nrp = $request->signatory_nrp;
        $correspondence->document_link = $request->document_link;

        // Upload document file if provided
        if ($request->hasFile('document_file')) {
            // Delete old file if exists
            if ($correspondence->file_path) {
                Storage::disk('public')->delete($correspondence->file_path);
            }

            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('correspondences', $fileName, 'public');
            $correspondence->file_path = $filePath;
        }

        // Upload signature file if provided
        if ($request->hasFile('signature_file')) {
            // Delete old file if exists
            if ($correspondence->signature_file) {
                Storage::disk('public')->delete($correspondence->signature_file);
            }

            $file = $request->file('signature_file');
            $fileName = time() . '_signature_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('signatures', $fileName, 'public');
            $correspondence->signature_file = $filePath;
        }

        $correspondence->save();

        // Sync tags if provided
        if ($request->has('tags')) {
            // Hapus semua tag dulu
            $correspondence->tags()->detach();

            // Tambahkan tag baru
            foreach ($request->tags as $tagSlug) {
                $correspondence->attachTagBySlug($tagSlug);
            }
        }

        // Sync documents if provided
        if ($request->has('document_ids')) {
            $documentData = [];
            foreach ($request->document_ids as $docId) {
                $documentData[$docId] = ['relation_type' => 'related'];
            }
            $correspondence->documents()->sync($documentData);
        }

        // Redirect dengan pesan sukses
        return redirect()
            ->route('modules.correspondence.letters.show', $correspondence->id)
            ->with('success', 'Surat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tenant_id = session('tenant_id');
        $correspondence = Correspondence::where('tenant_id', $tenant_id)->findOrFail($id);

        // Hapus file terkait jika ada
        if ($correspondence->file_path) {
            Storage::disk('public')->delete($correspondence->file_path);
        }

        if ($correspondence->signature_file) {
            Storage::disk('public')->delete($correspondence->signature_file);
        }

        // Hapus relasi dengan tags
        $correspondence->tags()->detach();

        // Hapus relasi dengan documents
        $correspondence->documents()->detach();

        // Hapus correspondence
        $correspondence->delete();

        return redirect()
            ->route('modules.correspondence.letters.index')
            ->with('success', 'Surat berhasil dihapus');
    }

    /**
     * Generate QR code for the correspondence.
     */
    public function generateQr(string $id)
    {
        $tenant_id = session('tenant_id');
        $correspondence = Correspondence::where('tenant_id', $tenant_id)->findOrFail($id);

        // Buat konten QR code dalam format teks yang lebih mudah dibaca
        $qrContent = "=== SURAT / NOTA DINAS ===\n\n";
        $qrContent .= "Nomor: {$correspondence->document_number}\n";
        $qrContent .= "Tanggal: " . $correspondence->document_date->format('d-m-Y') . "\n";
        $qrContent .= "Perihal: {$correspondence->subject}\n";
        $qrContent .= "Penandatangan: {$correspondence->signatory_name}\n\n";
        $qrContent .= "URL: " . route('modules.correspondence.letters.show', $correspondence->id);

        // Coba alternatif pembuatan QR code (metode #3) - paling sederhana
        return response(
            QrCode::format('svg')->size(200)->generate($qrContent)
        )->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Generate QR code as embedded Base64 for the correspondence.
     * Route alternatif jika SVG normal tidak berfungsi
     */
    public function generateQrBase64(string $id)
    {
        $tenant_id = session('tenant_id');
        $correspondence = Correspondence::where('tenant_id', $tenant_id)->findOrFail($id);

        // Buat konten QR code dalam format teks yang lebih mudah dibaca
        $qrContent = "=== SURAT / NOTA DINAS ===\n\n";
        $qrContent .= "Nomor: {$correspondence->document_number}\n";
        $qrContent .= "Tanggal: " . $correspondence->document_date->format('d-m-Y') . "\n";
        $qrContent .= "Perihal: {$correspondence->subject}\n";
        $qrContent .= "Penandatangan: {$correspondence->signatory_name}\n\n";
        $qrContent .= "URL: " . route('modules.correspondence.letters.show', $correspondence->id);

        // Generate QR code dengan format SVG dan konversi ke base64
        $svgQrCode = QrCode::format('svg')->size(200)->generate($qrContent);
        $base64 = base64_encode($svgQrCode);
        $dataUri = 'data:image/svg+xml;base64,' . $base64;

        return view('modules.Correspondence.letters.qr-embed', [
            'dataUri' => $dataUri,
            'correspondence' => $correspondence
        ]);
    }

    /**
     * Export the specified resource as PDF.
     */
    public function exportPdf(string $id)
    {
        $tenant_id = session('tenant_id');
        $correspondence = Correspondence::with(['tags', 'creator'])
            ->where('tenant_id', $tenant_id)
            ->findOrFail($id);

        // Buat QR code untuk surat
        $qrContent = "=== SURAT / NOTA DINAS ===\n\n";
        $qrContent .= "Nomor: {$correspondence->document_number}\n";
        $qrContent .= "Tanggal: " . $correspondence->document_date->format('d-m-Y') . "\n";
        $qrContent .= "Perihal: {$correspondence->subject}\n";
        $qrContent .= "Penandatangan: {$correspondence->signatory_name}\n\n";
        $qrContent .= "URL: " . route('modules.correspondence.letters.show', $correspondence->id);

        // Generate QR code dengan format SVG dan konversi ke base64
        $svgQrCode = QrCode::format('svg')->size(200)->generate($qrContent);
        $base64 = base64_encode($svgQrCode);
        $qrCodeDataUri = 'data:image/svg+xml;base64,' . $base64;

        // Generate view dengan QR code
        $html = view('modules.Correspondence.letters.pdf', compact('correspondence', 'qrCodeDataUri'))->render();

        // Setup PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF
        return $dompdf->stream('Surat_' . $correspondence->document_number . '.pdf');
    }

    /**
     * Export the specified resource as Word.
     */
    public function exportWord(string $id)
    {
        $tenant_id = session('tenant_id');
        $correspondence = Correspondence::with(['tags', 'creator'])
            ->where('tenant_id', $tenant_id)
            ->findOrFail($id);

        // Generate file name
        $fileName = 'Surat_' . Str::slug($correspondence->document_number) . '.docx';

        // Generate view
        $view = view('modules.Correspondence.letters.word', compact('correspondence'))->render();

        // Create a temporary HTML file
        $tmpFile = tempnam(sys_get_temp_dir(), 'correspondence');
        file_put_contents($tmpFile, $view);

        // Use pandoc to convert HTML to DOCX
        $outputFile = tempnam(sys_get_temp_dir(), 'correspondence') . '.docx';
        shell_exec('pandoc ' . $tmpFile . ' -o ' . $outputFile);

        // Return the file for download
        return response()->download($outputFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Search correspondences.
     */
    public function search(Request $request)
    {
        return $this->index($request);
    }
}
