<?php

namespace App\Http\Middleware;

use App\Models\Document;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantDocumentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lewati jika belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ambil tenant_id dari user yang login
        $userTenantId = Auth::user()->tenant_id;

        // Jika tidak ada parameter document ID, lanjutkan
        if (!$request->route('id') && !$request->route('document')) {
            return $next($request);
        }

        // Ambil document ID dari route
        $documentId = $request->route('id') ?? $request->route('document');

        // Jika tidak ada document ID, lanjutkan
        if (!$documentId) {
            return $next($request);
        }

        try {
            // Cari dokumen
            $document = Document::find($documentId);

            // Jika dokumen tidak ditemukan, lanjutkan (akan 404 di controller)
            if (!$document) {
                return $next($request);
            }

            // Log untuk debugging
            Log::info('Tenant access check untuk dokumen', [
                'document_id' => $documentId,
                'document_tenant_id' => $document->tenant_id,
                'user_tenant_id' => $userTenantId,
                'user_id' => Auth::id(),
                'match' => ($document->tenant_id == $userTenantId || $document->tenant_id == 1)
            ]);

            // Jika tenant_id dokumen sama dengan tenant_id user,
            // atau dokumen memiliki tenant_id == 1 (System),
            // maka izinkan akses
            if ($document->tenant_id == $userTenantId || $document->tenant_id == 1) {
                return $next($request);
            }

            // Jika tidak cocok, kembalikan ke halaman sebelumnya dengan pesan error
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke dokumen ini.');
        } catch (\Exception $e) {
            Log::error('Error di middleware EnsureTenantDocumentAccess', [
                'document_id' => $documentId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memeriksa akses dokumen.');
        }
    }
}
