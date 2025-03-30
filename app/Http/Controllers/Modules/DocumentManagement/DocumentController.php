<?php

namespace App\Http\Controllers\Modules\DocumentManagement;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('module:document-management');
        $this->middleware('check.permission:document-management,can_view')->only(['index', 'show']);
        $this->middleware('check.permission:document-management,can_create')->only(['create', 'store']);
        $this->middleware('check.permission:document-management,can_edit')->only(['edit', 'update']);
        $this->middleware('check.permission:document-management,can_delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        $query = Document::where('tenant_id', $tenantId);

        // Filtering
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('document_title', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->input('category') != '') {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('confidentiality_level') && $request->input('confidentiality_level') != '') {
            $query->where('confidentiality_level', $request->input('confidentiality_level'));
        }

        // Filter berdasarkan tag
        if ($request->has('tag') && $request->input('tag') != '') {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }

        $documents = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get all unique categories for filter
        $categories = Document::where('tenant_id', $tenantId)
            ->select('category')
            ->distinct()
            ->pluck('category');

        // Get available tags for filter
        $availableTags = \App\Models\Tag::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('modules.DocumentManagement.documents.index', compact('documents', 'categories', 'availableTags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = session('tenant_id');

        // Dapatkan daftar dokumen yang bisa menggantikan dokumen ini
        $documents = Document::where('tenant_id', $tenantId)
            ->orderBy('document_title')
            ->get(['id', 'document_title', 'document_number']);

        // Definisikan opsi untuk tipe dokumen
        $documentTypes = [
            'policy' => 'Kebijakan',
            'guideline' => 'Pedoman',
            'spo' => 'SPO',
            'program' => 'Program',
            'evidence' => 'Bukti'
        ];

        // Definisikan opsi untuk ruang lingkup dokumen
        $documentScopes = [
            'rumahsakit' => 'Rumah Sakit',
            'unitkerja' => 'Unit Kerja'
        ];

        return view('modules.DocumentManagement.documents.create', compact('documents', 'documentTypes', 'documentScopes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        Log::info('Membuat dokumen baru', [
            'tenant_id' => $tenantId,
            'user_id' => Auth::id()
        ]);

        $validator = Validator::make($request->all(), [
            'document_title' => 'required|string|max:255',
            'document_number' => 'required|string|max:50',
            'document_date' => 'nullable|date',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'confidentiality_level' => 'required|in:public,internal,confidential',
            'document_type' => 'nullable|string|max:50',
            'document_scope' => 'nullable|string|max:50',
            'is_regulation' => 'nullable|boolean',
            'revision_number' => 'nullable|string|max:50',
            'revision_date' => 'nullable|date',
            'storage_location' => 'nullable|string',
            'distribution_note' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $filePath = null;
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
        }

        $document = new Document();
        $document->tenant_id = $tenantId; // Pastikan tenant_id diisi
        $document->document_title = $request->document_title;
        $document->document_number = $request->document_number;
        $document->document_date = $request->document_date;
        $document->category = $request->category;
        $document->description = $request->description;
        $document->confidentiality_level = $request->confidentiality_level;
        $document->document_type = $request->document_type;
        $document->document_scope = $request->document_scope;
        $document->is_regulation = $request->has('is_regulation');
        $document->revision_number = $request->revision_number;
        $document->revision_date = $request->revision_date;
        $document->storage_location = $request->storage_location;
        $document->distribution_note = $request->distribution_note;
        $document->file_path = $filePath;
        $document->is_active = true;
        $document->uploaded_by = Auth::id();

        $document->save();

        // Log dokumen yang dibuat
        Log::info('Dokumen berhasil dibuat', [
            'document_id' => $document->id,
            'tenant_id' => $document->tenant_id,
            'title' => $document->document_title
        ]);

        // Sync tags if provided
        if ($request->has('tag_ids')) {
            $document->tags()->sync($request->tag_ids);
        }

        return redirect()
            ->route('modules.document-management.documents.index')
            ->with('success', 'Dokumen berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        // Cek apakah dokumen milik tenant yang sama
        if ($document->tenant_id !== session('tenant_id')) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini');
        }

        return view('modules.DocumentManagement.documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        // Cek apakah dokumen milik tenant yang sama
        if ($document->tenant_id !== session('tenant_id')) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini');
        }

        $tenantId = session('tenant_id');

        // Dapatkan daftar dokumen yang bisa menggantikan dokumen ini
        $documents = Document::where('tenant_id', $tenantId)
            ->where('id', '!=', $document->id) // Kecualikan dokumen saat ini
            ->orderBy('document_title')
            ->get(['id', 'document_title', 'document_number']);

        // Definisikan opsi untuk tipe dokumen
        $documentTypes = [
            'policy' => 'Kebijakan',
            'guideline' => 'Pedoman',
            'spo' => 'SPO',
            'program' => 'Program',
            'evidence' => 'Bukti'
        ];

        // Definisikan opsi untuk ruang lingkup dokumen
        $documentScopes = [
            'rumahsakit' => 'Rumah Sakit',
            'unitkerja' => 'Unit Kerja'
        ];

        return view('modules.DocumentManagement.documents.edit', compact('document', 'documents', 'documentTypes', 'documentScopes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tenantId = session('tenant_id');
        $document = Document::where('tenant_id', $tenantId)->findOrFail($id);

        Log::info('Mengupdate dokumen', [
            'document_id' => $id,
            'tenant_id' => $tenantId,
            'user_id' => Auth::id()
        ]);

        $validator = Validator::make($request->all(), [
            'document_title' => 'required|string|max:255',
            'document_number' => 'required|string|max:50',
            'document_date' => 'nullable|date',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'confidentiality_level' => 'required|in:public,internal,confidential',
            'document_type' => 'nullable|string|max:50',
            'document_scope' => 'nullable|string|max:50',
            'is_regulation' => 'nullable|boolean',
            'revision_number' => 'nullable|string|max:50',
            'revision_date' => 'nullable|date',
            'storage_location' => 'nullable|string',
            'distribution_note' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $filePath = $document->file_path;
        if ($request->hasFile('document_file')) {
            // Remove old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
        }

        $document->tenant_id = $tenantId; // Pastikan tenant_id diisi
        $document->document_title = $request->document_title;
        $document->document_number = $request->document_number;
        $document->document_date = $request->document_date;
        $document->category = $request->category;
        $document->description = $request->description;
        $document->confidentiality_level = $request->confidentiality_level;
        $document->document_type = $request->document_type;
        $document->document_scope = $request->document_scope;
        $document->is_regulation = $request->has('is_regulation');
        $document->revision_number = $request->revision_number;
        $document->revision_date = $request->revision_date;
        $document->storage_location = $request->storage_location;
        $document->distribution_note = $request->distribution_note;
        $document->file_path = $filePath;

        $document->save();

        // Log dokumen yang diupdate
        Log::info('Dokumen berhasil diupdate', [
            'document_id' => $document->id,
            'tenant_id' => $document->tenant_id,
            'title' => $document->document_title
        ]);

        // Sync tags if provided
        if ($request->has('tag_ids')) {
            $document->tags()->sync($request->tag_ids);
        } else {
            $document->tags()->detach();
        }

        return redirect()
            ->route('modules.document-management.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Cek apakah dokumen milik tenant yang sama
        if ($document->tenant_id !== session('tenant_id')) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini');
        }

        // Hapus file jika ada
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('modules.document-management.documents.index')
            ->with('success', 'Dokumen berhasil dihapus');
    }

    /**
     * Create a new revision of the document.
     */
    public function revise($id)
    {
        $tenantId = session('tenant_id');
        $old = Document::where('tenant_id', $tenantId)->findOrFail($id);

        // Verifikasi akses
        if (!$this->hasPermission('document-management', 'can_create')) {
            abort(403, 'Anda tidak memiliki izin untuk membuat revisi dokumen');
        }

        // Buat replika dokumen lama
        $new = $old->replicate();
        $new->revision_number = $this->generateNextRevision($old->revision_number);
        $new->revision_date = now();
        $new->is_active = true;
        $new->superseded_by_id = null;
        $new->uploaded_by = auth()->id();
        $new->save();

        // Duplikasi tag dokumen lama ke dokumen baru
        foreach ($old->tags as $tag) {
            $new->tags()->attach($tag->id);
        }

        // Update dokumen lama
        $old->is_active = false;
        $old->superseded_by_id = $new->id;
        $old->save();

        return redirect()->route('modules.document-management.documents.edit', $new->id)
            ->with('success', 'Revisi dokumen berhasil dibuat dengan nomor revisi ' . $new->revision_number);
    }

    /**
     * Generate the next revision letter.
     */
    private function generateNextRevision($current)
    {
        if (!$current) return 'A';
        return chr(ord(strtoupper($current)) + 1);
    }

    /**
     * Check if user has a specific permission.
     */
    private function hasPermission($module, $permission)
    {
        $user = auth()->user();

        // Superadmin dan tenant-admin selalu memiliki akses
        if ($user->role && ($user->role->slug === 'superadmin' || $user->role->slug === 'tenant-admin')) {
            return true;
        }

        // Periksa izin spesifik
        return \App\Helpers\PermissionHelper::hasPermission($module, $permission);
    }
}
