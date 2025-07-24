<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Recent Games Debug</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .test-box{border:1px solid #ccc;padding:15px;margin:10px 0;border-radius:8px;}</style>";

// Get the latest games
$games = App\Models\Game::latest()->take(5)->get();

foreach($games as $game) {
    echo "<div class='test-box'>";
    echo "<h3>Game: {$game->name}</h3>";
    echo "<p><strong>ID:</strong> {$game->id}</p>";
    echo "<p><strong>Image Path:</strong> " . ($game->image_path ?: 'NULL') . "</p>";
    echo "<p><strong>Created:</strong> {$game->created_at}</p>";
    
    if ($game->image_path) {
        echo "<p><strong>Image URL:</strong> {$game->image_url}</p>";
        echo "<p><strong>File exists in storage:</strong> " . (file_exists(storage_path('app/public/' . $game->image_path)) ? 'YES' : 'NO') . "</p>";
        echo "<p><strong>File exists in public:</strong> " . (file_exists(public_path('storage/' . $game->image_path)) ? 'YES' : 'NO') . "</p>";
        echo "<img src='{$game->image_url}' style='max-width: 100px; border: 2px solid green;' onerror='this.style.borderColor=\"red\";'>";
    } else {
        echo "<p style='color: red;'><strong>No image uploaded!</strong></p>";
    }
    echo "</div>";
}
?>
