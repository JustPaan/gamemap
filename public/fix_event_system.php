<?php
// Fix Event Images System
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Fix Event Images System</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .step { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
</style>";

$storageBase = __DIR__ . '/../storage/app/public/';
$eventImagesDir = $storageBase . 'event_images/';
$gameImagesDir = $storageBase . 'game_images/';

// Step 1: Create event_images directory
echo "<div class='step'>";
echo "<h2>Step 1: Creating Event Images Directory</h2>";

if (!is_dir($eventImagesDir)) {
    if (mkdir($eventImagesDir, 0755, true)) {
        echo "<span class='success'>✅ Created event_images directory</span><br>";
    } else {
        echo "<span class='error'>❌ Failed to create event_images directory</span><br>";
    }
} else {
    echo "<span class='info'>ℹ️ event_images directory already exists</span><br>";
}
echo "</div>";

// Step 2: Fix database paths and move files
echo "<div class='step'>";
echo "<h2>Step 2: Fixing Event Image Paths</h2>";

try {
    $events = DB::table('events')
        ->whereNotNull('image_path')
        ->where('image_path', '!=', '')
        ->get();
    
    echo "<span class='info'>Found " . count($events) . " events with images</span><br><br>";
    
    $fixedCount = 0;
    $movedCount = 0;
    $errorCount = 0;
    
    foreach ($events as $event) {
        echo "<strong>Event: {$event->title} (ID: {$event->id})</strong><br>";
        echo "Current DB path: <code>{$event->image_path}</code><br>";
        
        $filename = basename($event->image_path);
        
        // Check if file is stored in wrong directory (game_images)
        $wrongFile = $gameImagesDir . $filename;
        $correctFile = $eventImagesDir . $filename;
        
        $needsMove = false;
        $needsPathUpdate = false;
        
        // Check if file exists in wrong location
        if (file_exists($wrongFile)) {
            echo "Found file in game_images directory (wrong location)<br>";
            $needsMove = true;
        } elseif (file_exists($correctFile)) {
            echo "File exists in correct event_images directory<br>";
        } else {
            echo "<span class='warning'>⚠️ File not found in either directory</span><br>";
        }
        
        // Move file if needed
        if ($needsMove) {
            if (copy($wrongFile, $correctFile)) {
                unlink($wrongFile);
                echo "<span class='success'>✅ Moved file from game_images to event_images</span><br>";
                $movedCount++;
            } else {
                echo "<span class='error'>❌ Failed to move file</span><br>";
                $errorCount++;
            }
        }
        
        // Update database path if needed
        $correctPath = 'event_images/' . $filename;
        if ($event->image_path !== $correctPath) {
            DB::table('events')
                ->where('id', $event->id)
                ->update(['image_path' => $correctPath]);
            echo "<span class='success'>✅ Updated database path to: {$correctPath}</span><br>";
            $fixedCount++;
        } else {
            echo "<span class='info'>ℹ️ Database path already correct</span><br>";
        }
        
        echo "<br>";
    }
    
    echo "</div>";
    
    // Step 3: Summary
    echo "<div class='step'>";
    echo "<h2>Step 3: Summary</h2>";
    echo "• Files moved from game_images to event_images: <strong>{$movedCount}</strong><br>";
    echo "• Database paths fixed: <strong>{$fixedCount}</strong><br>";
    echo "• Errors encountered: <strong>{$errorCount}</strong><br>";
    
    if ($errorCount === 0) {
        echo "<br><span class='success'>🎉 Event images system is now properly configured!</span><br>";
        echo "<span class='info'>• Events store images in: event_images/ directory</span><br>";
        echo "<span class='info'>• Games store images in: game_images/ directory</span><br>";
        echo "<span class='info'>• Event images served via: /serve_event_image.php?f=filename</span><br>";
        echo "<span class='info'>• Game images served via: /serve_image.php?f=filename</span><br>";
    } else {
        echo "<br><span class='warning'>⚠️ Some issues remain. Check the details above.</span><br>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Error occurred:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}

echo "<br><br><strong>Fix completed at:</strong> " . date('Y-m-d H:i:s');
echo "<br><a href='/debug_events_js.php'>🔍 Check Events Data</a>";
echo "<br><a href='javascript:location.reload()'>🔄 Refresh This Page</a>";
?>
