<?php
// Test Event Creation System
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Event Creation System Test</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Check if new events can be created properly
echo "<h2>System Status Check</h2>";

// Check directories
$gameImagesDir = __DIR__ . '/../storage/app/public/game_images/';
$eventImagesDir = __DIR__ . '/../storage/app/public/event_images/';

echo "<div class='info'>";
echo "<strong>Directory Status:</strong><br>";
echo "• game_images: " . (is_dir($gameImagesDir) ? "✅ EXISTS" : "❌ MISSING") . "<br>";
echo "• event_images: " . (is_dir($eventImagesDir) ? "✅ EXISTS" : "❌ MISSING") . "<br>";
echo "</div><br>";

// Check controller
$controllerPath = __DIR__ . '/../app/Http/Controllers/OrganizerEventController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    echo "<div class='info'>";
    echo "<strong>OrganizerEventController Status:</strong><br>";
    if (strpos($content, "->store('game_images', 'public')") !== false) {
        echo "✅ Controller saves to 'game_images' directory<br>";
    } else {
        echo "⚠️ Controller might be saving to wrong directory<br>";
    }
    echo "</div><br>";
}

// Check serve_image.php
$serveImagePath = __DIR__ . '/serve_image.php';
if (file_exists($serveImagePath)) {
    $content = file_get_contents($serveImagePath);
    echo "<div class='info'>";
    echo "<strong>serve_image.php Status:</strong><br>";
    if (strpos($content, 'game_images') !== false) {
        echo "✅ Serves from 'game_images' directory<br>";
    } else {
        echo "⚠️ serve_image.php configuration issue<br>";
    }
    echo "</div><br>";
}

// Check Event model
echo "<div class='info'>";
echo "<strong>Event Model Test:</strong><br>";
try {
    // Test the image_url accessor
    $testEvent = new \App\Models\Event();
    $testEvent->image_path = 'game_images/test.jpg';
    $imageUrl = $testEvent->image_url;
    echo "✅ Event model image_url accessor working<br>";
    echo "• Sample URL: <code>{$imageUrl}</code><br>";
} catch (Exception $e) {
    echo "❌ Event model error: " . $e->getMessage() . "<br>";
}
echo "</div><br>";

echo "<h2>Recommendations</h2>";
echo "<div style='background:#f9f9f9;padding:15px;border-left:4px solid #4CAF50;'>";
echo "<strong>To test event image upload:</strong><br>";
echo "1. Login as an organizer<br>";
echo "2. Create a new event with an image<br>";
echo "3. Check if the image displays properly<br><br>";

echo "<strong>Current configuration:</strong><br>";
echo "• Events should save images to: <code>game_images/</code><br>";
echo "• Images served via: <code>/serve_image.php?f=filename</code><br>";
echo "• Missing images fallback to: <code>/images/default-event.jpg</code><br>";
echo "</div>";

echo "<br><a href='/create_default_event_image.php'>🖼️ Create Default Event Image</a>";
echo "<br><a href='/debug_event_images.php'>🔍 Debug Event Images</a>";
echo "<br><a href='javascript:history.back()'>← Go Back</a>";
?>
