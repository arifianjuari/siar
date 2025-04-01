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
            ->orderBy('order')
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
                ->route('tenant.tags.index')
                ->with('success', 'Tag berhasil dihapus!');
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
        // Logging untuk debug
        \Illuminate\Support\Facades\Log::info('attachTagToDocument dipanggil - DEBUG', [
            'request_method' => $request->method(),
            'server_request_method' => $_SERVER['REQUEST_METHOD'] ?? 'TIDAK DIKETAHUI',
            '_method' => $request->input('_method'),
            'is_delete' => $request->isMethod('delete') || $request->input('_method') === 'DELETE',
            'all_inputs' => $request->all(),
            'all_headers' => $request->header(),
            'ajax' => $request->ajax(),
            'cookies' => $request->cookie()
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
        if ($request->isMethod('delete') || $request->input('_method') === 'DELETE') {
            \Illuminate\Support\Facades\Log::info('Metode DELETE terdeteksi, memanggil detachTagFromDocument', [
                'request_method' => $request->method(),
                'input_method' => $request->input('_method')
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
    }

    /**
     * Contoh penggunaan untuk mendapatkan semua dokumen dengan tag tertentu
     */
    public function getDocumentsByTag($slug)
    {
        $tenantId = auth()->user()->tenant_id;

        // Dapatkan tag berdasarkan slug dan tenant
        $tag = Tag::where('slug', $slug)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Dapatkan semua dokumen dengan tag ini
        $riskReports = $tag->morphedByMany(RiskReport::class, 'document', 'document_tag')
            ->where('tenant_id', $tenantId)
            ->get();

        $documents = $tag->morphedByMany(Document::class, 'document', 'document_tag')
            ->where('tenant_id', $tenantId)
            ->get();

        return view('tenant.tags.documents', compact('tag', 'riskReports', 'documents'));
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

        // Validasi Input
        $validator = Validator::make($request->all(), [
            'tag_name' => 'required|string|max:255',
            'document_id' => 'required|integer',
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
            // Tambahkan elseif untuk tipe dokumen lain jika ada

            if (!$documentModel) {
                return response()->json(['success' => false, 'error' => 'Tipe dokumen tidak dikenali.'], 400);
            }

            // Cari dokumen
            $document = $documentModel::where('id', $documentId)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            // Lampirkan tag ke dokumen jika belum terpasang
            if (!$document->tags()->where('tags.id', $tag->id)->exists()) {
                $document->tags()->attach($tag->id);
                Log::info('Tag baru dibuat dan/atau dilampirkan', [
                    'user_id' => auth()->id(),
                    'tag_id' => $tag->id,
                    'tag_name' => $tag->name,
                    'document_id' => $documentId,
                    'document_type' => $documentType,
                    'tenant_id' => $tenantId
                ]);
            } else {
                Log::info('Tag sudah terpasang sebelumnya, tidak melampirkan lagi', [
                    'user_id' => auth()->id(),
                    'tag_id' => $tag->id,
                    'tag_name' => $tag->name,
                    'document_id' => $documentId,
                    'document_type' => $documentType,
                    'tenant_id' => $tenantId
                ]);
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
            return response()->json(['success' => false, 'error' => 'Terjadi kesalahan internal.'], 500);
        }
    }
}
