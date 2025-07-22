<?php

// Simple test script to verify storage setup
echo "=== GameMap Storage Test ===\n";

// Check directories
$directories = [
    'storage/app/public/game_images' => storage_path('app/public/game_images'),
    'public/storage' => public_path('storage'),
    'public/storage/game_images' => public_path('storage/game_images'),
];

foreach ($directories as $label => $path) {
    $exists = file_exists($path);
    $writable = is_writable($path);
    echo "✓ {$label}: " . ($exists ? "EXISTS" : "MISSING") . " | " . ($writable ? "WRITABLE" : "READ-ONLY") . "\n";
}

// Check if we can create a test file
$testFile = public_path('storage/game_images/test.txt');
if (file_put_contents($testFile, 'test content')) {
    echo "✓ Test file created successfully\n";
    unlink($testFile); // Clean up
} else {
    echo "✗ Failed to create test file\n";
}

// Check storage configuration
echo "\n=== Storage Configuration ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Asset URL: " . asset('storage/game_images/test.jpg') . "\n";

echo "\n=== Complete! ===\n";
