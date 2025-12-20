<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

function getNextSerial($type) {
    echo "Testing Type: $type\n";
    
    $prefixes = [
        'Electronics' => 'ELE',
        'Furniture'   => 'FNT',
        'Equipment'   => 'EQP',
        'Other'       => 'OTH'
    ];

    $prefix = $prefixes[$type] ?? strtoupper(substr($type, 0, 3));
    echo "Prefix: $prefix\n";

    $latestAsset = Asset::where('type', $type)
                        ->where('serial_number', 'like', "$prefix-%")
                        ->orderByRaw('LENGTH(serial_number) DESC') 
                        ->orderBy('serial_number', 'desc')
                        ->first();

    if ($latestAsset) {
        echo "Found Latest: " . $latestAsset->serial_number . "\n";
        $parts = explode('-', $latestAsset->serial_number);
        $nextNum = (int)end($parts) + 1;
    } else {
        echo "No previous asset found.\n";
        $nextNum = 1;
    }

    $newSerial = sprintf('%s-%03d', $prefix, $nextNum);
    echo "Generated: $newSerial\n";
    echo "--------------------------------\n";
}

getNextSerial('Furniture');
getNextSerial('Electronics');
