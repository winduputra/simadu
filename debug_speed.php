<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nasPath = App\Models\SystemSetting::get('nas_path');
echo "NAS Path configured: " . var_export($nasPath, true) . "\n";

$start = microtime(true);
$exists = is_dir($nasPath);
echo "Checking is_dir takes: " . (microtime(true) - $start) . "s\n";
echo "Is directory? " . ($exists ? 'Yes' : 'No') . "\n";

if ($exists) {
    $start = microtime(true);
    $testFile = $nasPath . DIRECTORY_SEPARATOR . 'test_speed_' . time() . '.txt';
    $bytes = file_put_contents($testFile, 'test data');
    echo "Writing 9 bytes takes: " . (microtime(true) - $start) . "s\n";
    if ($bytes) {
        unlink($testFile);
    }
} else {
    echo "Directory does not exist!\n";
}
