<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Restore ID 128 to original
$setting128 = \App\Setting::find(128);
if ($setting128) {
    $setting128->display_name = 'Compact Mode (58mm Receipt)';
    $setting128->save();
    echo "Restored ID 128 to 'Compact Mode (58mm Receipt)'\n";
}

// Remove incorrect 129
$setting129 = \App\Setting::find(129);
if ($setting129) {
    $setting129->delete();
    echo "Removed incorrect ID 129\n";
}

// Add new settings with IDs 130 and 131
\App\Setting::insert([
    ['id' => 130, 'name' => 'qz_tray_printer_name', 'display_name' => 'QZ Tray Printer Name', 'value' => ''],
    ['id' => 131, 'name' => 'enable_auto_printing', 'display_name' => 'Enable Auto Printing', 'value' => 'NO']
]);
echo "Added ID 130 (QZ Tray Printer Name) and 131 (Enable Auto Printing)\n";
?>