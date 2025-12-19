<?php

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    die("❌ .env file not found!\n");
}

$content = file_get_contents($envPath);

// Define standard MySQL config
$newConfig = [
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'tarumt_fbs',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',
];

foreach ($newConfig as $key => $value) {
    if (preg_match("/^$key=.*$/m", $content)) {
        // Replace existing
        $content = preg_replace("/^$key=.*$/m", "$key=$value", $content);
    } else {
        // Append if missing
        $content .= "\n$key=$value";
    }
}

file_put_contents($envPath, $content);
echo "✅ .env updated to use MySQL (tarumt_fbs)\n";
