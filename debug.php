<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$uk = App\Models\UnitKerja::where('kode', 'SEK')->first();
echo "Found: " . ($uk ? $uk->id : 'No') . "\n";
if ($uk) {
    try {
        $uk->delete();
        echo "Deleted successfully.\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
