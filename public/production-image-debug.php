<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Production Image Debug</h1>";
echo "<style>body{font-family:Arial;margin:20px;background:#f5f5f5;} .test-box{border:1px solid #ccc;padding:15px;margin:10px 0;border-radius:8px;background:white;} .success{border-color:green;} .error{border-color:red;background:#ffe6e6;}</style>";

// Check the most recent game (likely the 'coc' game)
$recentGame = App\Models\Game::orderBy('created_at', 'desc')->first();

if ($recentGame) {
    echo "<div class='test-box'>";
    echo "<h3>Most Recent Game: {$recentGame->name}</h3>";
    echo "<p><strong>Created:</strong> {$recentGame->created_at}</p>";
    echo "<p><strong>Image Path in DB:</strong> " . ($recentGame->image_path ?: '❌ NULL - NO IMAGE SAVED!') . "</p>";
    
    if ($recentGame->image_path) {
        echo "<p><strong>Generated Image URL:</strong> {$recentGame->image_url}</p>";
        
        // Test direct URL access
        $imageUrl = $recentGame->image_url;
        echo "<h4>Image Display Test:</h4>";
        echo "<img src='{$imageUrl}' style='max-width: 200px; border: 3px solid green;' onerror='this.style.borderColor=\"red\"; this.nextSibling.innerHTML=\"❌ FAILED TO LOAD\";' onload='this.nextSibling.innerHTML=\"✅ LOADED SUCCESSFULLY\";'>";
        echo "<div style='font-weight: bold; margin-top: 10px;'>Loading...</div>";
        
        // Check file existence on server
        $storagePath = storage_path('app/public/' . $recentGame->image_path);
        $publicPath = public_path('storage/' . $recentGame->image_path);
        
        echo "<h4>File System Check:</h4>";
        echo "<p>Storage file exists: " . (file_exists($storagePath) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p>Public symlink exists: " . (file_exists($publicPath) ? '✅ YES' : '❌ NO') . "</p>";
        
        if (file_exists($storagePath)) {
            echo "<p>File size: " . number_format(filesize($storagePath)) . " bytes</p>";
        }
        
    } else {
        echo "<div class='error'>";
        echo "<h4>❌ PROBLEM IDENTIFIED!</h4>";
        echo "<p>The game was created but <strong>NO IMAGE PATH WAS SAVED</strong> to the database!</p>";
        echo "<p>This means the image upload process failed during form submission.</p>";
        echo "</div>";
    }
    echo "</div>";
}

// Check app configuration
echo "<div class='test-box'>";
echo "<h3>Server Configuration</h3>";
echo "<p><strong>Environment:</strong> " . config('app.env') . "</p>";
echo "<p><strong>App URL:</strong> " . config('app.url') . "</p>";
echo "<p><strong>Debug Mode:</strong> " . (config('app.debug') ? 'ON' : 'OFF') . "</p>";
echo "<p><strong>PHP Upload Max:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>PHP Post Max:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "</div>";

// Check storage directories on production
echo "<div class='test-box'>";
echo "<h3>Production Storage Check</h3>";

$directories = [
    'storage/app/public/game_images' => storage_path('app/public/game_images'),
    'public/storage/game_images' => public_path('storage/game_images'),
];

foreach ($directories as $name => $path) {
    $exists = is_dir($path);
    $writable = $exists ? is_writable($path) : false;
    
    echo "<p><strong>{$name}:</strong> ";
    if ($exists) {
        echo "✅ EXISTS ";
        echo $writable ? "✅ WRITABLE" : "❌ NOT WRITABLE";
    } else {
        echo "❌ MISSING";
    }
    echo "</p>";
}
echo "</div>";

// List recent game images
echo "<div class='test-box'>";
echo "<h3>All Games with Images</h3>";
$gamesWithImages = App\Models\Game::whereNotNull('image_path')->orderBy('created_at', 'desc')->get();

if ($gamesWithImages->count() > 0) {
    foreach ($gamesWithImages as $game) {
        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<strong>{$game->name}</strong> - Created: {$game->created_at}<br>";
        echo "Image: {$game->image_path}<br>";
        echo "<img src='{$game->image_url}' style='max-width: 100px; max-height: 100px; border: 2px solid blue;' onerror='this.style.borderColor=\"red\";'>";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ NO GAMES WITH IMAGES FOUND!</p>";
    echo "<p>This confirms that image uploads are not working properly.</p>";
}
echo "</div>";

echo "<div class='test-box error'>";
echo "<h3>🔧 Recommended Actions:</h3>";
echo "<ol>";
echo "<li><strong>If no image_path in database:</strong> The form validation or file upload is failing</li>";
echo "<li><strong>If image_path exists but file missing:</strong> Storage permission issue</li>";
echo "<li><strong>If file exists but not displaying:</strong> URL generation or symlink issue</li>";
echo "</ol>";
echo "</div>";
?>
