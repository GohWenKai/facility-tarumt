<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Fixing Admin Accounts ===\n\n";

// Fix corrupted password hashes
echo "Fixing corrupted password hashes...\n";

// Fix Super Admin (ID 1) - had malformed hash
$superAdmin = User::find(1);
if ($superAdmin) {
    $superAdmin->password = Hash::make('admin123');
    $superAdmin->failed_login_attempts = 0;
    $superAdmin->save();
    echo "✓ Fixed Super Admin (ID: 1, Email: {$superAdmin->email})\n";
}

// Fix Admin (ID 6) - had \r\n at the end of password hash
$admin = User::find(6);
if ($admin) {
    $admin->password = Hash::make('admin123');
    $admin->failed_login_attempts = 0;
    $admin->save();
    echo "✓ Fixed Admin (ID: 6, Email: {$admin->email})\n";
}

// Create new admin only if needed
$newAdminEmail = 'admin.new@tarumt.edu.my';
$existingUser = User::where('email', $newAdminEmail)->first();

if (!$existingUser) {
    $user = new User();
    $user->name = 'New Admin';
    $user->tarumt_id = 'ADMIN003';
    $user->email = $newAdminEmail;
    $user->password = Hash::make('admin123');
    $user->role = 'admin';
    $user->credits = 10;
    $user->save();
    echo "✓ Created new admin account (Email: {$newAdminEmail})\n";
} else {
    echo "• New admin email already exists, skipping creation\n";
}

echo "\n=== Summary ===\n";
echo "All admin accounts now use password: admin123\n";
echo "\nWorking Admin Accounts:\n";
echo "1. superAdmin@tarumt.edu.my (password: admin123)\n";
echo "2. admin@tarumt.edu.my (password: admin123)\n";
if (!$existingUser) {
    echo "3. admin.new@tarumt.edu.my (password: admin123)\n";
}
echo "\n✓ All accounts fixed and ready to use!\n";
echo "\nGo to: http://127.0.0.1:8000/login\n";
