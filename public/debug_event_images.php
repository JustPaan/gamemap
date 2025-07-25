<?php
// Debug script for event images
// Access: https://your-domain.com/debug_event_images.php

// Include Laravel's database configuration
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Event Images Debug</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .event { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
    .debug-info { background: #f5f5f5; padding: 10px; margin: 10px 0; }
    .error { color: red; }
    .success { color: green; }
    .warning { color: orange; }
    img { max-width: 200px; max-height: 150px; border: 1px solid #ddd; }
</style>";

try {
    // Get all events with images
    $events = DB::table('events')
        ->whereNotNull('image_path')
        ->where('image_path', '!=', '')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    echo "<h2>Found " . count($events) . " events with images</h2>";
    
    foreach ($events as $event) {
        echo "<div class='event'>";
        echo "<h3>Event: {$event->title} (ID: {$event->id})</h3>";
        
        echo "<div class='debug-info'>";
        echo "<strong>Database Info:</strong><br>";
        echo "• Image Path in DB: <code>{$event->image_path}</code><br>";
        echo "• Created: {$event->created_at}<br>";
        echo "• Organizer ID: {$event->organizer_id}<br>";
        echo "</div>";
        
        // Extract filename
        $filename = basename($event->image_path);
        echo "<div class='debug-info'>";
        echo "<strong>File Analysis:</strong><br>";
        echo "• Extracted filename: <code>{$filename}</code><br>";
        
        // Check different possible paths
        $possiblePaths = [
            'game_images' => __DIR__ . '/../storage/app/public/game_images/' . $filename,
            'event_images' => __DIR__ . '/../storage/app/public/event_images/' . $filename,
            'full_path_game' => __DIR__ . '/../storage/app/public/' . $event->image_path,
        ];
        
        $foundPath = null;
        foreach ($possiblePaths as $label => $path) {
            $exists = file_exists($path);
            $color = $exists ? 'success' : 'error';
            echo "• {$label}: <span class='{$color}'>" . ($exists ? 'EXISTS' : 'NOT FOUND') . "</span><br>";
            echo "  Path: <code>{$path}</code><br>";
            
            if ($exists && !$foundPath) {
                $foundPath = $path;
                $foundLabel = $label;
            }
        }
        echo "</div>";
        
        // Test URL generation
        echo "<div class='debug-info'>";
        echo "<strong>URL Generation:</strong><br>";
        $serveUrl = "/serve_image.php?f=" . $filename;
        echo "• serve_image.php URL: <code>{$serveUrl}</code><br>";
        echo "• Full URL: <code>" . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}{$serveUrl}</code><br>";
        echo "</div>";
        
        // Show image if found
        if ($foundPath) {
            echo "<div class='debug-info'>";
            echo "<strong>Image Preview:</strong><br>";
            echo "Found in: <span class='success'>{$foundLabel}</span><br>";
            echo "File size: " . number_format(filesize($foundPath) / 1024, 2) . " KB<br>";
            echo "File type: " . mime_content_type($foundPath) . "<br>";
            echo "<br><img src='{$serveUrl}' alt='Event Image' onerror='this.style.border=\"2px solid red\"; this.alt=\"Failed to load\";'><br>";
            echo "</div>";
        } else {
            echo "<div class='debug-info error'>";
            echo "<strong>❌ Image file not found in any expected location!</strong><br>";
            echo "</div>";
        }
        
        echo "</div><hr>";
    }
    
    // Check serve_image.php configuration
    echo "<h2>serve_image.php Configuration</h2>";
    $serveImagePath = __DIR__ . '/serve_image.php';
    if (file_exists($serveImagePath)) {
        echo "<div class='debug-info success'>";
        echo "✅ serve_image.php exists<br>";
        echo "Path: <code>{$serveImagePath}</code><br>";
        
        $content = file_get_contents($serveImagePath);
        if (preg_match('/storage\/app\/public\/([^\/]+)\//', $content, $matches)) {
            echo "Serves from directory: <code>{$matches[1]}</code><br>";
        }
        echo "</div>";
    } else {
        echo "<div class='debug-info error'>";
        echo "❌ serve_image.php not found!<br>";
        echo "</div>";
    }
    
    // Check storage directories
    echo "<h2>Storage Directory Check</h2>";
    $storageDirs = [
        'game_images' => __DIR__ . '/../storage/app/public/game_images/',
        'event_images' => __DIR__ . '/../storage/app/public/event_images/',
        'avatars' => __DIR__ . '/../storage/app/public/avatars/',
    ];
    
    foreach ($storageDirs as $name => $dir) {
        $exists = is_dir($dir);
        $color = $exists ? 'success' : 'error';
        echo "<div class='debug-info'>";
        echo "<strong>{$name}:</strong> <span class='{$color}'>" . ($exists ? 'EXISTS' : 'NOT FOUND') . "</span><br>";
        echo "Path: <code>{$dir}</code><br>";
        
        if ($exists) {
            $files = glob($dir . '*');
            echo "Files count: " . count($files) . "<br>";
            if (count($files) > 0) {
                echo "Sample files: ";
                for ($i = 0; $i < min(3, count($files)); $i++) {
                    echo "<code>" . basename($files[$i]) . "</code> ";
                }
                if (count($files) > 3) echo "...";
                echo "<br>";
            }
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Error occurred:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "<br><br><strong>Debug completed at:</strong> " . date('Y-m-d H:i:s');
echo "<br><a href='javascript:location.reload()'>🔄 Refresh</a>";
?>
