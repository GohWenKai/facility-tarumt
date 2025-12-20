<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 🛠️ Facility Booking System Fixer ===\n\n";

// 1. Fix Missing Tables (personal_access_tokens)
echo "1. Checking database tables...\n";
if (!Schema::hasTable('personal_access_tokens')) {
    echo "   ⚠️ Table 'personal_access_tokens' missing. Creating...\n";
    Schema::create('personal_access_tokens', function (Blueprint $table) {
        $table->id();
        $table->morphs('tokenable');
        $table->string('name');
        $table->string('token', 64)->unique();
        $table->text('abilities')->nullable();
        $table->timestamp('last_used_at')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->timestamps();
    });
    echo "   ✅ Table created successfully!\n";
} else {
    echo "   ✅ Table 'personal_access_tokens' exists.\n";
}

// 2. Fix Admin Logins
echo "\n2. Fixing Admin Logins...\n";

// Fix ID 1 (Super Admin)
$admin1 = User::find(1);
if ($admin1) {
    // Ensure tarumt_id is set (might be missing in old SQL)
    if (empty($admin1->tarumt_id) || $admin1->tarumt_id !== 'ADMIN001') {
        $admin1->tarumt_id = 'ADMIN001';
    }
    $admin1->password = Hash::make('admin123');
    $admin1->email = 'admin@tarumt.edu.my';
    $admin1->role = 'admin';
    $admin1->save();
    echo "   ✅ Fixed ADMIN001 (ID: 1)\n";
    echo "      Password: admin123\n";
} else {
    echo "   ❌ User ID 1 not found.\n";
}

// Fix ID 6 (Admin002)
$admin2 = User::find(6);
if ($admin2) {
    if (empty($admin2->tarumt_id) || $admin2->tarumt_id !== 'Admin002') {
        $admin2->tarumt_id = 'Admin002';
    }
    $admin2->password = Hash::make('admin123');
    $admin2->role = 'admin';
    $admin2->save();
    echo "   ✅ Fixed Admin002 (ID: 6)\n";
    echo "      Password: admin123\n";
} else {
    echo "   ⚠️ User ID 6 not found (Admin002 might be deleted).\n";
}

// 3. Clear Cache
echo "\n3. Clearing application cache...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "   ✅ Cache cleared.\n";
} catch (\Exception $e) {
    echo "   ⚠️ Could not clear cache: " . $e->getMessage() . "\n";
}

echo "\n=== 🎉 Fix Complete! ===\n";
echo "You can now login with:\n";
echo "ID: ADMIN001\n";
echo "Password: admin123\n";
