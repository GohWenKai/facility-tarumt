<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "--- OTP Email Diagnostic ---\n";

// 1. Check Configuration
$mailer = Config::get('mail.default');
echo "Current Mailer: " . $mailer . "\n";
echo "Host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "Port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "Encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "From: " . Config::get('mail.from.address') . "\n";

// 2. Try Sending
echo "\nAttempting to send test email...\n";

try {
    Mail::raw('This is a test email from the facility booking system diagnostic script.', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Email Diagnostic');
    });
    echo "SUCCESS: Email sent successfully (check inbox or logs if 'log' driver).\n";
} catch (\Exception $e) {
    echo "ERROR: Failed to send email.\n";
    echo "Message: " . $e->getMessage() . "\n";
}

echo "\n--- End Diagnostic ---\n";
