<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find user by tarumt_id (Student/Staff ID)
$user = \App\Models\User::where('tarumt_id', 'Admin002')->first();

if ($user) {
    $user->password = bcrypt('admin123');
    $user->save();
    echo "✅ Password reset successfully!\n";
    echo "\nLogin with:\n";
    echo "Student/Staff ID: Admin002\n";
    echo "Password: admin123\n";
} else {
    echo "❌ User with ID Admin002 not found\n";
}
