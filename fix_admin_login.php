<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 🔧 Fixing Admin Passwords ===\n\n";

// List of accounts to fix
$accounts = [
    1 => ['name' => 'Super Admin', 'expected_id' => 'ADMIN001'], // ID 1
    6 => ['name' => 'Admin', 'expected_id' => 'Admin002']        // ID 6
];

foreach ($accounts as $id => $info) {
    $user = User::find($id);
    if ($user) {
        $user->password = Hash::make('admin123');
        $user->failed_login_attempts = 0; // Unlock if locked
        $user->save();
        
        echo "✅ Fixed {$info['name']} (ID: $id)\n";
        echo "   Email: " . $user->email . "\n";
        echo "   Tarumt ID: " . ($user->tarumt_id ?? 'N/A') . "\n";
        echo "   New Password: admin123\n\n";
    } else {
        echo "❌ User ID $id not found.\n";
    }
}

// Also try to find by Email if ID lookup failed or for other admins
$backupAdmins = ['admin@tarumt.edu.my', 'superAdmin@tarumt.edu.my'];
foreach ($backupAdmins as $email) {
    $user = User::where('email', $email)->first();
    if ($user && !isset($accounts[$user->id])) { // Don't process twice
        $user->password = Hash::make('admin123');
        $user->save();
        echo "✅ Fixed Admin by Email ({$email})\n";
        echo "   Tarumt ID: " . $user->tarumt_id . "\n";
        echo "   New Password: admin123\n\n";
    }
}

echo "=== Done! Try logging in now. ===\n";
