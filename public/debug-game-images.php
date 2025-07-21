<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Game Image Debug</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .test-box{border:1px solid #ccc;padding:15px;margin:10px 0;} .error{border-color:red;} .success{border-color:green;}</style>";

// Get a few games
$games = App\Models\Game::take(3)->get();

foreach($games as $game) {
    echo "<div class='test-box'>";
    echo "<h3>Game: {$game->name}</h3>";
    echo "<p><strong>ID:</strong> {$game->id}</p>";
    echo "<p><strong>Raw image_path:</strong> " . var_export($game->image_path, true) . "</p>";
    echo "<p><strong>Attributes image_path:</strong> " . var_export($game->attributes['image_path'] ?? 'NULL', true) . "</p>";
    echo "<p><strong>Generated image_url:</strong> {$game->image_url}</p>";
    
    // Test the actual file existence
    $imagePath = $game->image_path;
    if ($imagePath) {
        $fullStoragePath = storage_path('app/public/' . $imagePath);
        $publicStoragePath = public_path('storage/' . $imagePath);
        
        echo "<p><strong>Storage file exists:</strong> " . (file_exists($fullStoragePath) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p><strong>Public storage file exists:</strong> " . (file_exists($publicStoragePath) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p><strong>Storage path:</strong> {$fullStoragePath}</p>";
        echo "<p><strong>Public path:</strong> {$publicStoragePath}</p>";
        
        // Test URL accessibility
        echo "<p><strong>Testing image load:</strong></p>";
        echo "<img src='{$game->image_url}' style='max-width: 150px; max-height: 150px; border: 3px solid green;' onerror='this.style.borderColor=\"red\"; this.nextSibling.style.color=\"red\"; this.nextSibling.innerHTML=\"❌ FAILED TO LOAD\";' onload='this.nextSibling.style.color=\"green\"; this.nextSibling.innerHTML=\"✅ LOADED SUCCESSFULLY\";'>";
        echo "<span style='font-weight: bold; margin-left: 10px;'>Loading...</span>";
    } else {
        echo "<p style='color: red;'><strong>No image_path found!</strong></p>";
    }
    
    echo "</div>";
}

// Test configuration
echo "<div class='test-box'>";
echo "<h3>Configuration</h3>";
echo "<p><strong>APP_URL:</strong> " . config('app.url') . "</p>";
echo "<p><strong>ASSET_URL:</strong> " . (config('asset.url') ?? 'Not set') . "</p>";
echo "</div>";

// Test a direct URL
echo "<div class='test-box'>";
echo "<h3>Direct URL Test</h3>";
$testUrl = "https://gamemapv2-44t9f.ondigitalocean.app/storage/game_images/0rmEii2AKy9BStNZa295GyezbIPdY8dpycnUtGPc.jpg";
echo "<p>Testing direct URL: <a href='{$testUrl}' target='_blank'>{$testUrl}</a></p>";
echo "<img src='{$testUrl}' style='max-width: 150px; max-height: 150px; border: 3px solid green;' onerror='this.style.borderColor=\"red\"; this.nextSibling.innerHTML=\"❌ FAILED\";' onload='this.nextSibling.innerHTML=\"✅ SUCCESS\";'>";
echo "<span style='font-weight: bold; margin-left: 10px;'>Testing...</span>";
echo "</div>";
?>
