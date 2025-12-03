<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LimitSessionSize
{
    /**
     * Keys yang TIDAK BOLEH dihapus (critical untuk auth)
     * 
     * @var array
     */
    protected $criticalKeys = [
        '_token',
        'tenant_id', 
        'url',
        '_previous',
        'is_superadmin',
        'auth_role',
        'user_verified',
        'current_tenant',
    ];

    /**
     * Prefix keys yang TIDAK BOLEH dihapus
     * 
     * @var array
     */
    protected $criticalPrefixes = [
        'login_web_',
        'password_hash_',
        'auth_',
    ];

    /**
     * Keys yang AMAN untuk dihapus
     * 
     * @var array
     */
    protected $safeToDeleteKeys = [
        '_old_input',
        'errors',
        '_flash',
        'flash_notification',
        'report_params',
        'form_data',
        'wizard_data',
        'export_data',
        'import_data',
    ];

    /**
     * Prefix keys yang AMAN untuk dihapus
     * 
     * @var array
     */
    protected $safeToDeletePrefixes = [
        'debug_',
        'debug-test',
        'debug_test',
        'temp_',
        'cache_',
        'preview_',
    ];

    /**
     * Handle an incoming request.
     *
     * Middleware ini mencegah session cookie menjadi terlalu besar
     * dengan membersihkan data yang tidak diperlukan.
     * 
     * PENTING: Middleware ini TIDAK akan menghapus data authentication.
     * Jika session masih terlalu besar setelah cleanup, akan log warning
     * tapi TIDAK akan flush session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya jalankan untuk cookie driver
        if (config('session.driver') !== 'cookie') {
            return $response;
        }

        try {
            $this->trimSessionIfNeeded($request);
        } catch (\Exception $e) {
            // Jangan biarkan error di middleware ini mengganggu response
            Log::error('LimitSessionSize middleware error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
        }

        return $response;
    }

    /**
     * Trim session jika diperlukan
     */
    protected function trimSessionIfNeeded(Request $request): void
    {
        $sessionData = $request->session()->all();
        $sessionSize = strlen(serialize($sessionData));

        // Threshold: 3KB untuk mulai cleanup (cookie max ~4KB)
        if ($sessionSize <= 3000) {
            return;
        }

        $keysBefore = array_keys($sessionData);
        $deletedKeys = [];

        // LANGKAH 1: Hapus key yang AMAN untuk dihapus
        foreach ($this->safeToDeleteKeys as $key) {
            if ($request->session()->has($key)) {
                $request->session()->forget($key);
                $deletedKeys[] = $key;
            }
        }

        // LANGKAH 2: Hapus key dengan prefix yang AMAN
        foreach ($request->session()->all() as $key => $value) {
            if ($this->hasAnyPrefix($key, $this->safeToDeletePrefixes)) {
                $request->session()->forget($key);
                $deletedKeys[] = $key;
            }
        }

        // Recalculate size
        $sessionData = $request->session()->all();
        $sessionSizeAfter = strlen(serialize($sessionData));

        // Log cleanup
        if (count($deletedKeys) > 0) {
            Log::info('LimitSessionSize: Cleaned up session', [
                'size_before' => $sessionSize,
                'size_after' => $sessionSizeAfter,
                'deleted_keys' => $deletedKeys,
                'remaining_keys' => array_keys($sessionData),
                'user_id' => auth()->id(),
            ]);
        }

        // LANGKAH 3: Jika masih terlalu besar, LOG WARNING tapi JANGAN flush
        // Ini mencegah user logout karena session terlalu besar
        if ($sessionSizeAfter > 3800) {
            Log::warning('LimitSessionSize: Session still large after cleanup - consider using database session driver', [
                'size_bytes' => $sessionSizeAfter,
                'remaining_keys' => array_keys($sessionData),
                'user_id' => auth()->id(),
                'recommendation' => 'Set SESSION_DRIVER=database in environment',
            ]);
            
            // TIDAK melakukan flush() - ini akan menyebabkan logout
            // Biarkan Laravel handle, mungkin akan truncate tapi auth tetap ada
        }
    }

    /**
     * Check if key is critical and should not be deleted
     */
    protected function isCriticalKey(string $key): bool
    {
        if (in_array($key, $this->criticalKeys)) {
            return true;
        }

        return $this->hasAnyPrefix($key, $this->criticalPrefixes);
    }

    /**
     * Check if key starts with any of the given prefixes
     */
    protected function hasAnyPrefix(string $key, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (Str::startsWith($key, $prefix)) {
                return true;
            }
        }
        return false;
    }
}
