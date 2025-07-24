<?php

// Test file to verify image display
require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GameMap Image Storage Test ===\n";

// Test 1: Check if storage directory exists
$storageDir = public_path('storage/game_images');
echo "Storage Directory: " . ($storageDir) . "\n";
echo "Exists: " . (file_exists($storageDir) ? "YES" : "NO") . "\n";
echo "Writable: " . (is_writable($storageDir) ? "YES" : "NO") . "\n";

// Test 2: Check APP_URL configuration
echo "\nAPP_URL: " . config('app.url') . "\n";

// Test 3: Check if any games have images
try {
    $games = \App\Models\Game::whereNotNull('image_path')->take(3)->get();
    echo "\nGames with images:\n";
    foreach ($games as $game) {
        echo "- {$game->name}: {$game->image_path}\n";
        echo "  URL: {$game->image_url}\n";
        $imagePath = public_path('storage/' . $game->image_path);
        echo "  File exists: " . (file_exists($imagePath) ? "YES" : "NO") . "\n";
    }
} catch (Exception $e) {
    echo "Error checking games: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
