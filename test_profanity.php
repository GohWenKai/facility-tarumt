<?php
/**
 * Test Profanity Filter (PurgoMalum API)
 * 
 * Run: php test_profanity.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Bootstrap Laravel (minimal)
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PurgoMalum Profanity Filter Test ===\n\n";

// Test cases
$testCases = [
    [
        'text' => 'I need to book this room for a study group meeting.',
        'expected' => 'pass',
        'description' => 'Clean text - should pass'
    ],
    [
        'text' => 'I need this room because my professor asked me to.',
        'expected' => 'pass', 
        'description' => 'Clean text with normal words'
    ],
    [
        'text' => 'This is a damn important meeting for our project.',
        'expected' => 'fail',
        'description' => 'Contains mild profanity - should be censored'
    ],
    [
        'text' => 'What the hell, I really need this room!',
        'expected' => 'fail',
        'description' => 'Contains profanity - should be censored'
    ],
];

foreach ($testCases as $index => $test) {
    echo "Test " . ($index + 1) . ": {$test['description']}\n";
    echo "  Input: \"{$test['text']}\"\n";
    
    try {
        $response = Http::timeout(10)->get('https://www.purgomalum.com/service/json', [
            'text' => $test['text']
        ]);
        
        if ($response->successful()) {
            $result = $response->json();
            $filteredText = $result['result'] ?? $test['text'];
            
            $hasProfanity = ($filteredText !== $test['text']);
            
            echo "  Output: \"{$filteredText}\"\n";
            echo "  Profanity Detected: " . ($hasProfanity ? "YES" : "NO") . "\n";
            
            $actualResult = $hasProfanity ? 'fail' : 'pass';
            $status = ($actualResult === $test['expected']) ? '✓ CORRECT' : '✗ UNEXPECTED';
            echo "  Result: {$status}\n";
        } else {
            echo "  Error: API returned status {$response->status()}\n";
        }
    } catch (Exception $e) {
        echo "  Error: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

echo "=== Test Complete ===\n";
