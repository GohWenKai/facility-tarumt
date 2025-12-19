<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Updating SQLite Database ===\n\n";
echo "Database: " . config('database.connections.sqlite.database') . "\n\n";

// Fix all admin accounts
$admins = User::whereIn('role', ['admin'])->get();

echo "Found " . $admins->count() . " admin account(s)\n\n";

foreach ($admins as $admin) {
    echo "Updating: {$admin->email} (ID: {$admin->id}, TARUMT ID: {$admin->tarumt_id})\n";
    $admin->password = Hash::make('admin123');
    $admin->failed_login_attempts = 0;
    $admin->save();
    echo "  ✓ Password updated to: admin123\n";
    echo "  ✓ Failed login attempts reset\n\n";
}

// Create a brand new admin if needed
$newAdminId = 'NEWADMIN2025';
$newAdminEmail = 'newadmin2025@tarumt.edu.my';

$existing = User::where('tarumt_id', $newAdminId)->orWhere('email', $newAdminEmail)->first();

if (!$existing) {
    $newAdmin = new User();
    $newAdmin->name = 'Emergency Admin';
    $newAdmin->tarumt_id = $newAdminId;
    $newAdmin->email = $newAdminEmail;
    $newAdmin->password = Hash::make('admin123');
    $newAdmin->role = 'admin';
    $newAdmin->credits = 100;
    $newAdmin->save();
    echo "✓ Created emergency admin: {$newAdminEmail}\n";
    echo "  Student/Staff ID: {$newAdminId}\n";
    echo "  Password: admin123\n\n";
}

echo "\n=== All Admin Accounts ===\n";
$allAdmins = User::where('role', 'admin')->get();
foreach ($allAdmins as $a) {
    echo "• {$a->email} (ID: {$a->tarumt_id}) - Password: admin123\n";
}

echo "\n✅ All changes saved to SQLite database!\n";
echo "🔗 Login at: http://127.0.0.1:8000/login\n";
