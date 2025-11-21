<?php

/**
 * Debug routes for troubleshooting
 * These should be removed in production or protected behind auth
 */

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('debug')->name('debug.')->group(function () {
    
    // Check modules directory
    Route::get('/modules-filesystem', function () {
        $modulesPath = base_path('modules');
        
        $debug = [
            'path' => $modulesPath,
            'exists' => is_dir($modulesPath),
            'readable' => is_readable($modulesPath),
            'writable' => is_writable($modulesPath),
        ];
        
        if (is_dir($modulesPath)) {
            // Try glob
            $globResult = @glob($modulesPath . '/*', GLOB_ONLYDIR);
            $debug['glob_result'] = $globResult;
            $debug['glob_failed'] = ($globResult === false);
            
            // Try scandir
            try {
                $scandirResult = scandir($modulesPath);
                $debug['scandir_result'] = $scandirResult;
                
                // Filter directories
                $directories = [];
                foreach ($scandirResult as $item) {
                    if ($item === '.' || $item === '..') continue;
                    $fullPath = $modulesPath . '/' . $item;
                    if (is_dir($fullPath)) {
                        $directories[] = [
                            'name' => $item,
                            'path' => $fullPath,
                            'has_module_json' => file_exists($fullPath . '/module.json'),
                            'has_config' => file_exists($fullPath . '/Config/config.php'),
                        ];
                    }
                }
                $debug['directories'] = $directories;
            } catch (\Exception $e) {
                $debug['scandir_error'] = $e->getMessage();
            }
        }
        
        // Check database modules
        $dbModules = \App\Models\Module::all()->map(function($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'slug' => $m->slug,
                'code' => $m->code,
            ];
        });
        $debug['db_modules'] = $dbModules;
        $debug['db_count'] = $dbModules->count();
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    })->name('modules.filesystem');
    
    // Test module sync (dry run)
    Route::get('/modules-sync-test', function () {
        $controller = new \App\Http\Controllers\SuperAdmin\ModuleManagementController();
        
        // Use reflection to call private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('discoverModulesFromFilesystem');
        $method->setAccessible(true);
        
        try {
            $result = $method->invoke($controller);
            
            return response()->json([
                'success' => true,
                'modules_found' => count($result),
                'modules' => $result,
            ], 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500, [], JSON_PRETTY_PRINT);
        }
    })->name('modules.sync.test');
});
