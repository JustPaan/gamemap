<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Game URL Testing</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .test-box{border:1px solid #ccc;padding:15px;margin:10px 0;}</style>";

// Get some games from the database
$games = App\Models\Game::take(5)->get();

echo "<div class='test-box'>";
echo "<h3>Game Image URLs</h3>";

foreach($games as $game) {
    echo "<div style='margin: 15px 0; padding: 10px; border: 1px solid #ddd;'>";
    echo "<h4>Game: {$game->name}</h4>";
    echo "<p><strong>Image Path:</strong> {$game->image_path}</p>";
    echo "<p><strong>Generated URL:</strong> {$game->image_url}</p>";
    echo "<p><strong>Test Image:</strong></p>";
    echo "<img src='{$game->image_url}' style='max-width: 100px; max-height: 100px; border: 2px solid green;' onerror='this.style.borderColor=\"red\"; this.alt=\"❌ Failed to load\";' alt='✅ Success'>";
    echo "</div>";
}

echo "</div>";

// Test APP_URL configuration
echo "<div class='test-box'>";
echo "<h3>Configuration</h3>";
echo "<p><strong>APP_URL:</strong> " . config('app.url') . "</p>";
echo "<p><strong>APP_ENV:</strong> " . config('app.env') . "</p>";
echo "</div>";
?>
