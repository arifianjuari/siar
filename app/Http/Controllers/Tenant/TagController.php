<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\RiskReport;
use App\Models\Document;
use App\Models\Correspondence;
use App\Models\SPO;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        $search = $request->get('search');

        $tags = Tag::forTenant($tenantId)
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        Log::info('User melihat daftar tag', [
            'user_id' => auth()->id(),
            'role' => auth()->user()->role->slug ?? null,
            'tenant_id' => $tenantId
        ]);

        return view('tenant.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        // Get parent tags for dropdown
        $parentTags = Tag::forTenant($tenantId)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('tenant.tags.create', compact('parentTags'));
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tags,id',
            'order' => 'nullable|integer'
        ]);

        // Set default order if not provided
        if (!isset($validatedData['order'])) {
            $maxOrder = Tag::forTenant($tenantId)->max('order');
            $validatedData['order'] = $maxOrder + 1;
        }

        // Add tenant_id
        $validatedData['tenant_id'] = $tenantId;

        $tag = Tag::create($validatedData);

        Log::info('User membuat tag baru', [
            'user_id' => auth()->id(),
            'tag_id' => $tag->id,
            'tenant_id' => $tenantId
        ]);

        return redirect()
            ->route('tenant.tags.index')
            ->with('success', 'Tag berhasil dibuat!');
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag)
    {
        $tenantId = session('tenant_id');

        // Ensure the tag belongs to the current tenant
        if ($tag->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        // Load children and parent
        $tag->load(['children', 'parent']);

        return view('tenant.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag)
    {
        $tenantId = session('tenant_id');

        // Ensure the tag belongs to the current tenant
        if ($tag->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        // Get parent tags for dropdown (excluding this tag and its children)
        $parentTags = Tag::forTenant($tenantId)
            ->where('id', '!=', $tag->id)
            ->whereNotIn('parent_id', [$tag->id])
            ->orderBy('name')
            ->get();

        return view('tenant.tags.edit', compact('tag', 'parentTags'));
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $tenantId = session('tenant_id');

        // Ensure the tag belongs to the current tenant
        if ($tag->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tags')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($tag->id)
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tags,id',
            'order' => 'nullable|integer'
        ]);

        // Prevent circular references in parent-child relationships
        if ($validatedData['parent_id'] && $tag->id == $validatedData['parent_id']) {
            return redirect()
                ->back()
                ->with('error', 'Tag tidak dapat menjadi parent dari dirinya sendiri!')
                ->withInput();
        }

        $tag->update($validatedData);

        Log::info('User mengubah tag', [
            'user_id' => auth()->id(),
            'tag_id' => $tag->id,
            'tenant_id' => $tenantId
        ]);

        return redirect()
            ->route('tenant.tags.index')
            ->with('success', 'Tag berhasil diperbarui!');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag)
    {
        $tenantId = session('tenant_id');

        // Ensure the tag belongs to the current tenant
        if ($tag->tenant_id != $tenantId) {
            abort(403, 'Unauthorized action');
        }

        // Check if has children
        if ($tag->children()->count() > 0) {
            return redirect()
                ->route('tenant.tags.index')
                ->with('error', 'Tag tidak dapat dihapus karena memiliki sub-tag!');
        }

        // Begin a database transaction
        DB::beginTransaction();

        try {
            $tagId = $tag->id;
            $tagName = $tag->name;

            $tag->delete();

            Log::info('User menghapus tag', [
                'user_id' => auth()->id(),
                'tag_id' => $tagId,
                'tag_name' => $tagName,
                'tenant_id' => $tenantId
            ]);

            DB::commit();

            return redirect()
                ->route('tenant.tags.index');
        } catch (\Exception $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Error saat menghapus tag', [
                'user_id' => auth()->id(),
                'tag_id' => $tag->id ?? null,
                'tag_name' => $tag->name ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus tag: ' . $e->getMessage());
        }
    }

    /**
     * Update the order of tags through drag and drop
     */
    public function updateOrder(Request $request)
    {
        $tenantId = session('tenant_id');

        $tags = $request->input('tags', []);

        foreach ($tags as $tag) {
            $item = Tag::find($tag['id']);

            // Ensure the tag belongs to the current tenant
            if ($item && $item->tenant_id == $tenantId) {
                $item->update([
                    'order' => $tag['order'],
                    'parent_id' => $tag['parent_id'] ?? null
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Contoh penggunaan untuk menambahkan tag ke dokumen
     */
    public function attachTagToDocument(Request $request)
    {
        // Logging untuk debug LENGKAP
        \Illuminate\Support\Facades\Log::info('attachTagToDocument dipanggil - DEBUG LENGKAP', [
            'request_method' => $request->method(),
            'server_request_method' => $_SERVER['REQUEST_METHOD'] ?? 'TIDAK DIKETAHUI',
            '_method' => $request->input('_method'),
            'is_delete' => $request->isMethod('delete') || $request->input('_method') === 'DELETE',
            'all_inputs' => $request->all(),
            'all_headers' => $request->header(),
            'ajax' => $request->ajax(),
            'cookies' => $request->cookie(),
            'tag_id' => $request->input('tag_id'),
            'document_id' => $request->input('document_id'),
            'document_type' => $request->input('document_type'),
            'x_method_override' => $request->header('X-HTTP-Method-Override')
        ]);

        $tagId = $request->input('tag_id');
        $documentId = $request->input('document_id');
        $documentType = $request->input('document_type');
        $tenantId = auth()->user()->tenant_id;

        // Logging untuk debug
        \Illuminate\Support\Facades\Log::info('attachTagToDocument dipanggil', [
            'method' => $request->method(),
            'is_delete' => $request->isMethod('delete') || $request->input('_method') === 'DELETE',
            'tag_id' => $tagId,
            'document_id' => $documentId,
            'document_type' => $documentType,
            'request_all' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        // Jika method DELETE, lepaskan tag
        if (
            $request->isMethod('delete') ||
            $request->input('_method') === 'DELETE' ||
            $request->header('X-HTTP-Method-Override') === 'DELETE'
        ) {

            \Illuminate\Support\Facades\Log::info('Metode DELETE terdeteksi, memanggil detachTagFromDocument', [
                'request_method' => $request->method(),
                'input_method' => $request->input('_method'),
                'header_method_override' => $request->header('X-HTTP-Method-Override')
            ]);
            return $this->detachTagFromDocument($request);
        }

        // Validasi data
        if (empty($tagId) || empty($documentId) || empty($documentType)) {
            return redirect()->back()->with('error', 'Data tidak lengkap!');
        }

        // Validasi bahwa tag berada dalam tenant yang sama
        $tag = Tag::where('id', $tagId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Handle RiskReport
        if ($documentType === 'App\\Models\\RiskReport' || $documentType === 'risk_report') {
            try {
                $document = RiskReport::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Periksa apakah tag sudah terpasang
                if ($document->tags()->where('tags.id', $tagId)->exists()) {
                    \Illuminate\Support\Facades\Log::info('Tag sudah terpasang sebelumnya', [
                        'document_id' => $documentId,
                        'tag_id' => $tagId,
                        'document_type' => $documentType
                    ]);
                    return redirect()->back()->with('info', 'Tag ini sudah terpasang pada dokumen.');
                }

                // Tambahkan tag ke dokumen
                $document->tags()->attach($tagId);

                return redirect()->back()->with('success', 'Tag berhasil ditambahkan ke dokumen!');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error saat menambahkan tag ke RiskReport', [
                    'error' => $e->getMessage(),
                    'document_id' => $documentId,
                    'tag_id' => $tagId,
                    'document_type' => $documentType
                ]);
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        // Handle Document
        if ($documentType === 'App\\Models\\Document' || $documentType === 'document') {
            try {
                $document = Document::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Periksa apakah tag sudah terpasang
                if ($document->tags()->where('tags.id', $tagId)->exists()) {
                    \Illuminate\Support\Facades\Log::info('Tag sudah terpasang sebelumnya', [
                        'document_id' => $documentId,
                        'tag_id' => $tagId,
                        'document_type' => $documentType
                    ]);
                    return redirect()->back()->with('info', 'Tag ini sudah terpasang pada dokumen.');
                }

                // Tambahkan tag ke dokumen
                $document->tags()->attach($tagId);

                return redirect()->back()->with('success', 'Tag berhasil ditambahkan ke dokumen!');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error saat menambahkan tag ke Document', [
                    'error' => $e->getMessage(),
                    'document_id' => $documentId,
                    'tag_id' => $tagId,
                    'document_type' => $documentType
                ]);
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Tipe dokumen tidak dikenali!');
    }

    /**
     * Melepaskan tag dari dokumen
     */
    protected function detachTagFromDocument(Request $request)
    {
        // Log awal eksekusi dengan semua parameter
        \Illuminate\Support\Facades\Log::info('MULAI detachTagFromDocument', [
            'request_all' => $request->all(),
            'method' => $request->method(),
            'headers' => $request->header(),
            'user_id' => auth()->id()
        ]);

        try {
            // Jika request adalah JSON, ambil parameter dari JSON
            if ($request->isJson() || $request->expectsJson()) {
                $data = $request->json()->all();
                $tagId = $data['tag_id'] ?? null;
                $documentId = $data['document_id'] ?? null;
                $documentType = $data['document_type'] ?? null;
            } else {
                $tagId = $request->input('tag_id');
                $documentId = $request->input('document_id');
                $documentType = $request->input('document_type');
            }

            $tenantId = auth()->user()->tenant_id;

            // Logging untuk debug
            \Illuminate\Support\Facades\Log::info('detachTagFromDocument dipanggil', [
                'user_id' => auth()->id(),
                'tag_id' => $tagId,
                'document_id' => $documentId,
                'document_type' => $documentType,
                'tenant_id' => $tenantId,
                'request_method' => $request->method(),
                'request_all' => $request->all(),
                'request_method_field' => $request->input('_method'),
                'is_json' => $request->isJson(),
                'expects_json' => $request->expectsJson(),
                'ajax' => $request->ajax(),
                'headers' => $request->headers->all()
            ]);

            // Validasi data
            if (empty($tagId) || empty($documentId) || empty($documentType)) {
                \Illuminate\Support\Facades\Log::warning('Data tidak lengkap', [
                    'tag_id' => $tagId,
                    'document_id' => $documentId,
                    'document_type' => $documentType
                ]);

                // Jika request adalah AJAX, kembalikan respons JSON
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Data tidak lengkap! Silakan coba lagi.'
                    ], 400);
                }

                // Jika request adalah JSON, kembalikan respons JSON
                if ($request->isJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Data tidak lengkap! Silakan coba lagi.'
                    ], 400);
                }

                return redirect()->back()->with('error', 'Data tidak lengkap! Silakan coba lagi.');
            }

            // Validasi bahwa tag berada dalam tenant yang sama
            $tag = Tag::where('id', $tagId)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            // Handle berbagai jenis dokumen
            $detached = false;
            $model = null;

            // Handle RiskReport
            if ($documentType === 'App\\Models\\RiskReport' || $documentType === 'risk_report') {
                $document = RiskReport::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);
                $detached = true;
                $model = 'RiskReport';

                \Illuminate\Support\Facades\Log::info('Tag berhasil dilepaskan dari RiskReport', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }
            // Handle Document
            elseif ($documentType === 'App\\Models\\Document' || $documentType === 'document') {
                $document = Document::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);
                $detached = true;
                $model = 'Document';

                \Illuminate\Support\Facades\Log::info('Tag berhasil dilepaskan dari Document', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }
            // Handle Correspondence
            elseif ($documentType === 'App\\Models\\Correspondence' || $documentType === 'correspondence') {
                $document = \App\Models\Correspondence::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);

                \Illuminate\Support\Facades\Log::info('Tag berhasil dihapus dari Correspondence', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }
            // Handle SPO
            elseif ($documentType === 'App\\Models\\SPO' || $documentType === 'spo') {
                $document = \App\Models\SPO::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                \Illuminate\Support\Facades\Log::info('Dokumen SPO ditemukan, menghapus tag...', [
                    'spo_id' => $document->id,
                    'tag_id' => $tagId
                ]);

                // Lepaskan tag dari dokumen
                $result = $document->tags()->detach($tagId);
                $detached = true;
                $model = 'SPO';

                \Illuminate\Support\Facades\Log::info('Tag berhasil dihapus dari SPO', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId,
                    'rows_affected' => $result
                ]);
            }

            // Jika request adalah AJAX, kembalikan respons tanpa konten
            if ($request->ajax()) {
                if ($detached) {
                    return response()->noContent(); // 204 No Content untuk operasi sukses tanpa respons
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Tipe dokumen tidak dikenali!'
                    ], 400);
                }
            }

            // Jika request adalah JSON, kembalikan respons JSON
            if ($request->isJson() || $request->expectsJson()) {
                if ($detached) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Tag berhasil dihapus dari dokumen.',
                        'model' => $model
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Tipe dokumen tidak dikenali!'
                    ], 400);
                }
            }

            // Respons untuk non-JSON request
            if ($detached) {
                return redirect()->back()->with('success', 'Tag berhasil dihapus dari dokumen.');
            } else {
                return redirect()->back()->with('error', 'Tipe dokumen tidak dikenali!');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat menghapus tag', [
                'user_id' => auth()->id(),
                'tag_id' => $tagId ?? null,
                'document_id' => $documentId ?? null,
                'document_type' => $documentType ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Jika request adalah AJAX, kembalikan respons error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Terjadi kesalahan saat menghapus tag: ' . $e->getMessage()
                ], 500);
            }

            // Jika request adalah JSON, kembalikan respons JSON
            if ($request->isJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Terjadi kesalahan saat menghapus tag: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus tag: ' . $e->getMessage());
        }

        // Log akhir eksekusi yang sukses
        \Illuminate\Support\Facades\Log::info('SELESAI detachTagFromDocument - SUKSES', [
            'tag_id' => $tagId,
            'document_id' => $documentId,
            'document_type' => $documentType,
            'detached' => $detached,
            'model' => $model ?? null
        ]);
    }

    /**
     * Display documents associated with a specific tag slug, potentially filtered by module.
     */
    public function getDocumentsByTag(Request $request, $slug)
    {
        $tenantId = session('tenant_id');
        $selectedModule = $request->query('module', 'all'); // Get module filter from query string

        $tag = Tag::forTenant($tenantId)->where('slug', $slug)->firstOrFail();

        // --- Query Documents ---
        $documentsQuery = $tag->documents()->where('tenant_id', $tenantId);

        // --- Query Risk Reports ---
        $riskReportsQuery = $tag->riskReports()->where('tenant_id', $tenantId);

        // --- Query Correspondence ---
        $correspondenceQuery = $tag->correspondences()->where('tenant_id', $tenantId); // Query Correspondence

        // --- Query SPOs ---
        $sposQuery = $tag->spos()->where('tenant_id', $tenantId); // Query SPOs

        // Get document counts for each module
        $documentCount = $documentsQuery->count();
        $riskReportCount = $riskReportsQuery->count();
        $correspondenceCount = $correspondenceQuery->count();
        $spoCount = $sposQuery->count();

        // Create module summary array
        $moduleSummary = [
            [
                'name' => 'Manajemen Dokumen',
                'count' => $documentCount,
                'slug' => 'document-management',
                'icon' => 'fas fa-file-alt',
                'color' => 'primary'
            ],
            [
                'name' => 'Manajemen Risiko',
                'count' => $riskReportCount,
                'slug' => 'risk-management',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'danger'
            ],
            [
                'name' => 'Korespondensi',
                'count' => $correspondenceCount,
                'slug' => 'correspondence',
                'icon' => 'fas fa-envelope',
                'color' => 'info'
            ],
            [
                'name' => 'SPO',
                'count' => $spoCount,
                'slug' => 'spo',
                'icon' => 'fas fa-clipboard-list',
                'color' => 'success'
            ]
        ];

        // Filter out modules with 0 documents
        $moduleSummary = array_filter($moduleSummary, function ($module) {
            return $module['count'] > 0;
        });

        // Apply module filter
        $combinedDocuments = collect();

        if ($selectedModule === 'all') {
            $combinedDocuments = $documentsQuery->get()
                ->merge($riskReportsQuery->get())
                ->merge($correspondenceQuery->get()) // Merge Correspondence
                ->merge($sposQuery->get()); // Merge SPOs
        } elseif ($selectedModule === 'document-management') {
            $combinedDocuments = $documentsQuery->get();
        } elseif ($selectedModule === 'risk-management') {
            $combinedDocuments = $riskReportsQuery->get();
        } elseif ($selectedModule === 'correspondence') { // Filter for Correspondence
            $combinedDocuments = $correspondenceQuery->get();
        } elseif ($selectedModule === 'spo') { // Filter for SPO - Ganti 'spo' jika identifier berbeda
            $combinedDocuments = $sposQuery->get();
        } else {
            // Default to all if module is unknown or invalid
            $combinedDocuments = $documentsQuery->get()
                ->merge($riskReportsQuery->get())
                ->merge($correspondenceQuery->get())
                ->merge($sposQuery->get());
        }


        // Sort combined results (e.g., by creation date descending)
        // You might need a common date field or adjust the sorting logic
        $combinedDocuments = $combinedDocuments->sortByDesc(function ($item) {
            return $item->created_at ?? $item->document_date ?? now()->subYears(10); // Example sorting
        });


        Log::info('User melihat dokumen berdasarkan tag', [
            'user_id' => auth()->id(),
            'tag_id' => $tag->id,
            'tag_slug' => $slug,
            'module_filter' => $selectedModule,
            'tenant_id' => $tenantId,
        ]);

        return view('modules.DocumentManagement.documents-by-tag', compact('tag', 'combinedDocuments', 'selectedModule', 'moduleSummary'));
    }

    /**
     * Menghapus tag dari dokumen - endpoint khusus
     */
    public function deleteTag(Request $request)
    {
        $tagId = $request->input('tag_id');
        $documentId = $request->input('document_id');
        $documentType = $request->input('document_type');
        $tenantId = auth()->user()->tenant_id;

        // Logging untuk debugging
        \Illuminate\Support\Facades\Log::info('deleteTag dipanggil', [
            'user_id' => auth()->id(),
            'tag_id' => $tagId,
            'document_id' => $documentId,
            'document_type' => $documentType,
            'tenant_id' => $tenantId,
        ]);

        try {
            // Validasi data
            if (empty($tagId) || empty($documentId) || empty($documentType)) {
                return redirect()->back()->with('error', 'Data tidak lengkap!');
            }

            // Validasi bahwa tag berada dalam tenant yang sama
            $tag = Tag::where('id', $tagId)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            // Handle RiskReport
            if ($documentType === 'App\\Models\\RiskReport' || $documentType === 'risk_report') {
                $document = RiskReport::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);

                \Illuminate\Support\Facades\Log::info('Tag berhasil dihapus dari RiskReport', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }
            // Handle Document
            elseif ($documentType === 'App\\Models\\Document' || $documentType === 'document') {
                $document = Document::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);

                \Illuminate\Support\Facades\Log::info('Tag berhasil dihapus dari Document', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }
            // Handle Correspondence
            elseif ($documentType === 'App\\Models\\Correspondence' || $documentType === 'correspondence') {
                $document = \App\Models\Correspondence::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);

                \Illuminate\Support\Facades\Log::info('Tag berhasil dihapus dari Correspondence', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }
            // Handle SPO
            elseif ($documentType === 'App\\Models\\SPO' || $documentType === 'spo') {
                $document = \App\Models\SPO::where('id', $documentId)
                    ->where('tenant_id', $tenantId)
                    ->firstOrFail();

                // Lepaskan tag dari dokumen
                $document->tags()->detach($tagId);

                \Illuminate\Support\Facades\Log::info('Tag berhasil dihapus dari SPO', [
                    'document_id' => $documentId,
                    'tag_id' => $tagId
                ]);
            }

            // Jika ini adalah permintaan AJAX, kembalikan respons JSON
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            // Untuk permintaan non-AJAX, kembali ke halaman sebelumnya dengan pesan sukses
            return redirect()->back()->with('success', 'Tag berhasil dihapus dari dokumen.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat menghapus tag', [
                'user_id' => auth()->id(),
                'tag_id' => $tagId,
                'document_id' => $documentId,
                'document_type' => $documentType,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Jika ini adalah permintaan AJAX, kembalikan respons JSON dengan error
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }

            // Untuk permintaan non-AJAX, kembali ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus tag: ' . $e->getMessage());
        }
    }

    /**
     * Membuat tag baru jika belum ada dan melampirkannya ke dokumen.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAndAttachTag(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info('createAndAttachTag dipanggil', [
            'document_id' => $request->input('document_id'),
            'document_id_type' => gettype($request->input('document_id')),
            'document_type' => $request->input('document_type'),
            'tag_name' => $request->input('tag_name'),
            'request_all' => $request->all()
        ]);

        // Validasi Input
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|max:255',
            'document_id' => 'required',
            'document_type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()], 400);
        }

        $tagName = $request->input('tag_name');
        $documentId = $request->input('document_id');
        $documentType = $request->input('document_type');
        $slug = Str::slug($tagName);

        try {
            // Cari atau buat tag baru
            $tag = Tag::firstOrCreate(
                [
                    'slug' => $slug,
                    'tenant_id' => $tenantId
                ],
                [
                    'name' => $tagName,
                    'tenant_id' => $tenantId
                    // Anda bisa menambahkan default description atau order jika perlu
                ]
            );

            // Tentukan model dokumen berdasarkan document_type
            $documentModel = null;
            if ($documentType === 'App\\Models\\RiskReport' || $documentType === 'risk_report') {
                $documentModel = RiskReport::class;
            } elseif ($documentType === 'App\\Models\\Document' || $documentType === 'document') {
                $documentModel = Document::class;
            }
            // Tambahkan elseif untuk Correspondence
            elseif ($documentType === 'App\\Models\\Correspondence' || $documentType === 'correspondence') {
                $documentModel = \App\Models\Correspondence::class;
            }
            // Tambahkan elseif untuk SPO
            elseif ($documentType === 'App\\Models\\SPO' || $documentType === 'spo') {
                $documentModel = \App\Models\SPO::class;
            }
            // Tambahkan elseif untuk tipe dokumen lain jika ada

            if (!$documentModel) {
                return response()->json(['success' => false, 'error' => 'Tipe dokumen tidak dikenali.'], 400);
            }

            // Cari dokumen
            $document = $documentModel::where('id', $documentId)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            try {
                // Lampirkan tag ke dokumen
                $document->tags()->attach($tag->id);

                Log::info('Tag baru dibuat dan/atau dilampirkan', [
                    'user_id' => auth()->id(),
                    'tag_id' => $tag->id,
                    'tag_name' => $tag->name,
                    'document_id' => $documentId,
                    'document_type' => $documentType,
                    'tenant_id' => $tenantId
                ]);
            } catch (\Illuminate\Database\QueryException $qe) {
                // Cek apakah ini error duplikasi
                if (strpos($qe->getMessage(), 'Duplicate entry') !== false || $qe->getCode() == 23000) {
                    // Tag sudah terpasang, ini bukan error yang fatal
                    Log::info('Tag sudah terpasang sebelumnya, menampilkan sebagai sukses', [
                        'user_id' => auth()->id(),
                        'tag_id' => $tag->id,
                        'tag_name' => $tag->name,
                        'document_id' => $documentId,
                        'document_type' => $documentType,
                        'tenant_id' => $tenantId
                    ]);
                } else {
                    // Ini error database lain, lempar kembali
                    throw $qe;
                }
            }

            // Kembalikan response sukses dengan detail tag
            return response()->json([
                'success' => true,
                'tag' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Dokumen tidak ditemukan saat createAndAttachTag', [
                'error' => $e->getMessage(),
                'document_id' => $documentId,
                'document_type' => $documentType,
                'tenant_id' => $tenantId
            ]);
            return response()->json(['success' => false, 'error' => 'Dokumen tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Error saat createAndAttachTag', [
                'error' => $e->getMessage(),
                'tag_name' => $tagName,
                'document_id' => $documentId,
                'document_type' => $documentType,
                'tenant_id' => $tenantId,
                'trace' => $e->getTraceAsString()
            ]);
            Log::error('Error saat createAndAttachTag', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'tag_name' => $tagName,
                'document_id' => $documentId,
                'document_id_length' => strlen($documentId),
                'document_type' => $documentType,
                'tenant_id' => $tenantId,
                'trace' => $e->getTraceAsString(),
                'sql' => $e->getSql ?? null
            ]);
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
