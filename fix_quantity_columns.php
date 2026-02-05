<?php
/**
 * Standalone script to update order_details quantity columns to support decimals
 * Run this from your project root: php fix_quantity_columns.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating order_details quantity columns...\n";

try {
    // Update ordered_qty column to DECIMAL
    DB::statement('ALTER TABLE `order_details` MODIFY `ordered_qty` DECIMAL(10,2) NOT NULL DEFAULT 0');
    echo "✓ Updated ordered_qty to DECIMAL(10,2)\n";

    // Update received_qty column to DECIMAL
    DB::statement('ALTER TABLE `order_details` MODIFY `received_qty` DECIMAL(10,2) NOT NULL DEFAULT 0');
    echo "✓ Updated received_qty to DECIMAL(10,2)\n";

    echo "\n✅ All quantity columns updated successfully!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
