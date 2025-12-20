<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking TESTADMIN in database...\n\n";

$user = DB::table('users')->where('tarumt_id', 'TESTADMIN')->first();

if ($user) {
    echo "✅ TESTADMIN EXISTS in database!\n";
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
} else {
    echo "❌ TESTADMIN not found. Creating now...\n";
    
    DB::table('users')->insert([
        'tarumt_id' => 'TESTADMIN',
        'name' => 'Test Admin',
        'email' => 'testadmin@tarumt.edu.my',
        'password' => bcrypt('admin123'),
        'role' => 'admin',
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ TESTADMIN created!\n";
}

echo "\n================================================\n";
echo "To see in phpMyAdmin:\n";
echo "1. Go to: http://localhost/phpmyadmin\n";
echo "2. Click: tarumt_fbs (left sidebar)\n";
echo "3. Click: users table\n";
echo "4. Look for tarumt_id = TESTADMIN\n";
echo "================================================\n";
