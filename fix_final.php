<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== 🔧 Final Fix for Admin Login ===\n\n";

// Fix ID 1
$user = User::find(1);
if ($user) {
    $user->name = 'Super Admin';
    $user->tarumt_id = 'ADMIN001'; // Ensure this is set!
    $user->email = 'admin@tarumt.edu.my'; // Set to expected email
    $user->password = Hash::make('admin123');
    $user->role = 'admin';
    $user->save();
    
    echo "✅ FIXED Account ID: 1\n";
    echo "   Login ID: ADMIN001\n";
    echo "   Password: admin123\n\n";
} else {
    // ID 1 doesn't exist? Create it.
    echo "⚠️ ID 1 not found. Creating new admin...\n";
    DB::table('users')->insert([
        'id' => 1,
        'name' => 'Super Admin',
        'tarumt_id' => 'ADMIN001',
        'email' => 'admin@tarumt.edu.my',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ CREATED Account ID: 1\n";
    echo "   Login ID: ADMIN001\n";
    echo "   Password: admin123\n\n";
}

echo "=== Login Instructions ===\n";
echo "Go to: http://127.0.0.1:8000/login\n";
echo "Student/Staff ID: ADMIN001\n";
echo "Password: admin123\n";
