<?php

use App\Models\Module;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modules = Module::all();

echo "ID | Name | Code | Slug\n";
echo "---|---|---|---\n";
foreach ($modules as $module) {
    echo "{$module->id} | {$module->name} | " . ($module->code ?? 'N/A') . " | " . ($module->slug ?? 'N/A') . "\n";
}
