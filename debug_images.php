<?php

// Debug script to check game images
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== GameMap Image Debug ===\n";

// Check games
$games = App\Models\Game::whereIn('name', ['candycrush', 'aoe'])->get();

foreach ($games as $game) {
    echo "\nGame: {$game->name}\n";
    echo "Image path: " . ($game->image_path ?: 'NULL') . "\n";
    
    if ($game->image_path) {
        $fullPath = storage_path('app/public/' . $game->image_path);
        echo "Full storage path: {$fullPath}\n";
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        echo "Asset URL: " . asset('storage/' . $game->image_path) . "\n";
        
        $publicPath = public_path('storage/' . $game->image_path);
        echo "Public symlink path: {$publicPath}\n";
        echo "Symlink accessible: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
    }
}

// Check storage symlink
$symlinkPath = public_path('storage');
echo "\nStorage symlink exists: " . (is_link($symlinkPath) || is_dir($symlinkPath) ? 'YES' : 'NO') . "\n";

if (is_dir($symlinkPath)) {
    echo "Storage directory contents: \n";
    $contents = scandir($symlinkPath);
    foreach ($contents as $item) {
        if ($item !== '.' && $item !== '..') {
            echo "  - {$item}\n";
        }
    }
}
