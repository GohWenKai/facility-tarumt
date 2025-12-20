<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Reset ADMIN001 password
DB::table('users')
    ->where('tarumt_id', 'ADMIN001')
    ->update(['password' => bcrypt('admin123')]);

echo "✅ ADMIN001 password reset!\n\n";
echo "Login with:\n";
echo "====================\n";
echo "Student/Staff ID: ADMIN001\n";
echo "Password: admin123\n";
echo "====================\n";
echo "\nGo to: http://127.0.0.1:8000/login\n";
