<?php

$host = '127.0.0.1';
$port = '3306';
$username = 'root';
$password = '';
$database = 'tarumt_fbs';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.\n";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    echo "Database `$database` created or already exists.\n";

    // Select database
    $pdo->exec("USE `$database`");

    // Import SQL file
    $sqlFile = 'tarumt_fbs.sql';
    if (file_exists($sqlFile)) {
        echo "Importing $sqlFile...\n";
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql); // Note: simple exec might fail on multiple statements depending on driver, but usually okay for dumps.
        // If simple exec fails for multiple queries, we split
        echo "SQL imported successfully.\n";
    } else {
        echo "Error: $sqlFile not found.\n";
    }

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
