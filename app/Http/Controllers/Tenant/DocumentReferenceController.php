<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\DocumentReference;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage; // Dihapus karena tidak ada lagi upload file lokal
use Illuminate\Validation\Rule;

class DocumentReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $references = DocumentReference::latest()->paginate(10);
        $hideDefaultHeader = true;
        return view('tenant.document-references.index', compact('references', 'hideDefaultHeader'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $referenceTypes = DocumentReference::$referenceTypes;
        $hideDefaultHeader = true;
        return view('tenant.document-references.create', compact('referenceTypes', 'hideDefaultHeader'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_type' => ['required', Rule::in(array_keys(DocumentReference::$referenceTypes))],
            'reference_number' => 'required|string|max:255',
            'title' => 'required|string',
            'issued_by' => 'required|string|max:255',
            'issued_date' => 'required|date',
            'related_unit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'file_url' => 'nullable|string|max:2048', // Diubah dari file menjadi string (URL/path)
        ]);

        // Logika upload file dihapus
        // if ($request->hasFile('file')) {
        //     $file = $request->file('file');
        //     $path = $file->store('document-references', 'public');
        //     $validated['file_url'] = $path;
        // }

        DocumentReference::create($validated);

        return redirect()->route('tenant.document-references.index')
            ->with('success', 'Dokumen referensi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $reference = DocumentReference::findOrFail($id);
        $hideDefaultHeader = true;
        return view('tenant.document-references.show', compact('reference', 'hideDefaultHeader'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $reference = DocumentReference::findOrFail($id);
        $referenceTypes = DocumentReference::$referenceTypes;
        $hideDefaultHeader = true;
        return view('tenant.document-references.edit', compact('reference', 'referenceTypes', 'hideDefaultHeader'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reference = DocumentReference::findOrFail($id);

        $validated = $request->validate([
            'reference_type' => ['required', Rule::in(array_keys(DocumentReference::$referenceTypes))],
            'reference_number' => 'required|string|max:255',
            'title' => 'required|string',
            'issued_by' => 'required|string|max:255',
            'issued_date' => 'required|date',
            'related_unit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'file_url' => 'nullable|string|max:2048', // Diubah dari file menjadi string (URL/path)
        ]);

        // Logika upload file dihapus
        // if ($request->hasFile('file')) {
        //     // Delete old file if exists - tidak relevan lagi jika hanya menyimpan URL
        //     // if ($reference->file_url && Storage::disk('public')->exists($reference->file_url)) {
        //     //     Storage::disk('public')->delete($reference->file_url);
        //     // }
        //     
        //     $file = $request->file('file');
        //     $path = $file->store('document-references', 'public');
        //     $validated['file_url'] = $path;
        // }

        $reference->update($validated);

        return redirect()->route('tenant.document-references.index')
            ->with('success', 'Dokumen referensi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reference = DocumentReference::findOrFail($id);

        // Logika hapus file fisik dihapus, karena hanya menyimpan URL
        // if ($reference->file_url && Storage::disk('public')->exists($reference->file_url)) {
        //     Storage::disk('public')->delete($reference->file_url);
        // }

        $reference->delete();

        return redirect()->route('tenant.document-references.index')
            ->with('success', 'Dokumen referensi berhasil dihapus');
    }
}
