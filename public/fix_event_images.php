<?php
// Event Images Fix Script
// This script will fix the event image storage and database path issues

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h1>Event Images Fix Script</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .step { border: 1px solid #ccc; margin: 10px 0; padding: 15px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
</style>";

$storageBase = __DIR__ . '/../storage/app/public/';
$gameImagesDir = $storageBase . 'game_images/';
$eventImagesDir = $storageBase . 'event_images/';

// Step 1: Ensure directories exist
echo "<div class='step'>";
echo "<h2>Step 1: Creating Required Directories</h2>";

if (!is_dir($gameImagesDir)) {
    if (mkdir($gameImagesDir, 0755, true)) {
        echo "<span class='success'>✅ Created game_images directory</span><br>";
    } else {
        echo "<span class='error'>❌ Failed to create game_images directory</span><br>";
    }
} else {
    echo "<span class='info'>ℹ️ game_images directory already exists</span><br>";
}

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

// Step 2: Check current events and their files
echo "<div class='step'>";
echo "<h2>Step 2: Analyzing Current Events</h2>";

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
        
        // Check if file exists in event_images (wrong location)
        $eventImageFile = $eventImagesDir . $filename;
        $gameImageFile = $gameImagesDir . $filename;
        
        $fileFound = false;
        $needsMove = false;
        
        if (file_exists($eventImageFile)) {
            echo "Found file in event_images directory<br>";
            $fileFound = true;
            $needsMove = true;
        } elseif (file_exists($gameImageFile)) {
            echo "Found file in game_images directory (correct location)<br>";
            $fileFound = true;
        } else {
            echo "<span class='error'>❌ File not found in either directory</span><br>";
            $errorCount++;
        }
        
        if ($fileFound) {
            // Move file if it's in the wrong place
            if ($needsMove) {
                if (copy($eventImageFile, $gameImageFile)) {
                    unlink($eventImageFile);
                    echo "<span class='success'>✅ Moved file to game_images directory</span><br>";
                    $movedCount++;
                } else {
                    echo "<span class='error'>❌ Failed to move file</span><br>";
                    $errorCount++;
                }
            }
            
            // Update database path to use basename only (for consistency)
            $correctPath = 'game_images/' . $filename;
            if ($event->image_path !== $correctPath) {
                DB::table('events')
                    ->where('id', $event->id)
                    ->update(['image_path' => $correctPath]);
                echo "<span class='success'>✅ Updated database path to: {$correctPath}</span><br>";
                $fixedCount++;
            } else {
                echo "<span class='info'>ℹ️ Database path already correct</span><br>";
            }
        }
        
        echo "<br>";
    }
    
    echo "</div>";
    
    // Step 3: Summary
    echo "<div class='step'>";
    echo "<h2>Step 3: Summary</h2>";
    echo "• Files moved: <strong>{$movedCount}</strong><br>";
    echo "• Database paths fixed: <strong>{$fixedCount}</strong><br>";
    echo "• Errors encountered: <strong>{$errorCount}</strong><br>";
    
    if ($errorCount === 0) {
        echo "<br><span class='success'>🎉 All event images should now be working!</span><br>";
        echo "<span class='info'>Event images will now be served from: /serve_image.php?f=filename</span><br>";
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
echo "<br><a href='/debug_event_images.php'>🔍 Check Debug Again</a>";
echo "<br><a href='javascript:location.reload()'>🔄 Refresh This Page</a>";
?>
