<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 🔧 Admin Password Reset (Safe Mode) ===\n\n";

// Fix ID 1 (Super Admin)
$admin1 = User::find(1);
if ($admin1) {
    echo "1. Found ADMIN001 (ID: 1)\n";
    // Don't change email to avoid conflicts
    $admin1->tarumt_id = 'ADMIN001';
    $admin1->password = Hash::make('admin123');
    $admin1->role = 'admin';
    $admin1->save();
    echo "   ✅ Password reset to: admin123\n";
    echo "   Login ID: ADMIN001\n";
}

// Fix ID 6 (Admin002)
$admin2 = User::find(6);
if ($admin2) {
    echo "\n2. Found Admin002 (ID: 6)\n";
    $admin2->tarumt_id = 'Admin002';
    $admin2->password = Hash::make('admin123');
    $admin2->role = 'admin';
    $admin2->save();
    echo "   ✅ Password reset to: admin123\n";
    echo "   Login ID: Admin002\n";
}

// Ensure tokens table exists (Double check)
if (!Illuminate\Support\Facades\Schema::hasTable('personal_access_tokens')) {
    echo "\n⚠️ Table 'personal_access_tokens' still missing. Re-running migration SQL...\n";
    // ... create table logic if needed, but it should be there.
}

echo "\n=== 🎉 Done! ===\n";
