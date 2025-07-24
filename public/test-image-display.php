<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Game Image Display Test</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .test-box{border:1px solid #ccc;padding:15px;margin:10px 0;border-radius:8px;} .success{border-color:green;background:#f0f8f0;} .error{border-color:red;background:#f8f0f0;}</style>";

// Get games with images
$games = App\Models\Game::whereNotNull('image_path')->take(5)->get();

if ($games->count() > 0) {
    echo "<div class='test-box'>";
    echo "<h3>✅ Found " . $games->count() . " games with images</h3>";
    echo "</div>";
    
    foreach($games as $game) {
        echo "<div class='test-box'>";
        echo "<h3>Game: {$game->name}</h3>";
        echo "<p><strong>Image Path:</strong> {$game->image_path}</p>";
        echo "<p><strong>Asset URL (old method):</strong> " . asset('storage/' . $game->image_path) . "</p>";
        echo "<p><strong>Image URL (new accessor):</strong> {$game->image_url}</p>";
        
        // Test both methods
        echo "<div style='display: flex; gap: 20px; margin: 15px 0;'>";
        
        echo "<div>";
        echo "<h4>Using asset('storage/...)</h4>";
        echo "<img src='" . asset('storage/' . $game->image_path) . "' style='max-width: 150px; max-height: 150px; border: 3px solid blue;' onerror='this.style.borderColor=\"red\"; this.nextSibling.innerHTML=\"❌ FAILED\";' onload='this.nextSibling.innerHTML=\"✅ SUCCESS\";'>";
        echo "<div style='font-weight: bold; color: blue;'>Testing...</div>";
        echo "</div>";
        
        echo "<div>";
        echo "<h4>Using image_url accessor</h4>";
        echo "<img src='{$game->image_url}' style='max-width: 150px; max-height: 150px; border: 3px solid green;' onerror='this.style.borderColor=\"red\"; this.nextSibling.innerHTML=\"❌ FAILED\";' onload='this.nextSibling.innerHTML=\"✅ SUCCESS\";'>";
        echo "<div style='font-weight: bold; color: green;'>Testing...</div>";
        echo "</div>";
        
        echo "</div>";
        
        // Check file existence
        $storagePath = storage_path('app/public/' . $game->image_path);
        $publicPath = public_path('storage/' . $game->image_path);
        
        echo "<p><strong>Storage file exists:</strong> " . (file_exists($storagePath) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p><strong>Public symlink file exists:</strong> " . (file_exists($publicPath) ? '✅ YES' : '❌ NO') . "</p>";
        
        echo "</div>";
    }
} else {
    echo "<div class='test-box error'>";
    echo "<h3>❌ No games with images found!</h3>";
    echo "<p>Please upload some game images through the admin panel first.</p>";
    echo "</div>";
}

// Configuration check
echo "<div class='test-box'>";
echo "<h3>Configuration</h3>";
echo "<p><strong>APP_URL:</strong> " . config('app.url') . "</p>";
echo "<p><strong>APP_ENV:</strong> " . config('app.env') . "</p>";
echo "<p><strong>ASSET_URL:</strong> " . (config('asset.url') ?? 'Not set') . "</p>";
echo "</div>";
?>
