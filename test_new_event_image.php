<?php
// Test script to debug new event image creation

// Make this a simple PHP script without Laravel for quick testing

echo "<h2>🔍 Test New Event Image Creation</h2>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Check the latest event
$latestEvent = Event::latest()->first();

if ($latestEvent) {
    echo "<h3>📅 Latest Event Details</h3>";
    echo "<div class='info'>ID: {$latestEvent->id}</div>";
    echo "<div class='info'>Title: {$latestEvent->title}</div>";
    echo "<div class='info'>Created: {$latestEvent->created_at}</div>";
    echo "<div class='info'>Image Path in DB: " . ($latestEvent->image_path ?: 'NULL') . "</div>";
    
    if ($latestEvent->image_path) {
        echo "<div class='info'>Generated Image URL: {$latestEvent->image_url}</div>";
        
        // Check if the actual file exists
        $filename = basename($latestEvent->image_path);
        $fullPath = __DIR__ . '/storage/app/public/event_images/' . $filename;
        
        echo "<div class='info'>Looking for file: {$fullPath}</div>";
        
        if (file_exists($fullPath)) {
            echo "<div class='success'>✅ Image file EXISTS</div>";
            echo "<div class='info'>File size: " . filesize($fullPath) . " bytes</div>";
            
            // Test the serve_event_image.php directly
            echo "<div class='info'>Testing direct access:</div>";
            echo "<img src='/serve_event_image.php?f={$filename}' style='max-width:200px;border:1px solid #ccc;' onerror='this.style.border=\"2px solid red\";this.alt=\"FAILED TO LOAD\"'>";
            
        } else {
            echo "<div class='error'>❌ Image file NOT FOUND</div>";
            
            // Check if it's in the old game_images directory 
            $oldPath = __DIR__ . '/storage/app/public/game_images/' . $filename;
            if (file_exists($oldPath)) {
                echo "<div class='error'>⚠️ Found in OLD game_images directory: {$oldPath}</div>";
            }
        }
    } else {
        echo "<div class='error'>❌ No image path in database</div>";
    }
    
} else {
    echo "<div class='error'>❌ No events found in database</div>";
}

// Check directory status
echo "<h3>📁 Directory Status</h3>";
$eventImagesDir = __DIR__ . '/storage/app/public/event_images/';
$gameImagesDir = __DIR__ . '/storage/app/public/game_images/';

echo "<div class='info'>Event images directory: {$eventImagesDir}</div>";
if (is_dir($eventImagesDir)) {
    echo "<div class='success'>✅ event_images directory exists</div>";
    $files = glob($eventImagesDir . '*');
    echo "<div class='info'>Files in event_images: " . count($files) . "</div>";
    if (count($files) > 0) {
        echo "<ul>";
        foreach (array_slice($files, -5) as $file) {
            echo "<li>" . basename($file) . " (" . filesize($file) . " bytes)</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<div class='error'>❌ event_images directory missing</div>";
}

echo "<div class='info'>Game images directory: {$gameImagesDir}</div>";
if (is_dir($gameImagesDir)) {
    echo "<div class='success'>✅ game_images directory exists</div>";
    $files = glob($gameImagesDir . '*');
    echo "<div class='info'>Files in game_images: " . count($files) . "</div>";
} else {
    echo "<div class='error'>❌ game_images directory missing</div>";
}

// Check recent events with images
echo "<h3>📋 Recent Events with Images</h3>";
$recentEventsWithImages = Event::whereNotNull('image_path')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($recentEventsWithImages->count() > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>File Exists</th><th>Preview</th></tr>";
    
    foreach ($recentEventsWithImages as $event) {
        $filename = basename($event->image_path);
        $fullPath = __DIR__ . '/storage/app/public/event_images/' . $filename;
        $exists = file_exists($fullPath) ? '✅' : '❌';
        
        echo "<tr>";
        echo "<td>{$event->id}</td>";
        echo "<td>{$event->title}</td>";
        echo "<td>{$event->image_path}</td>";
        echo "<td>{$exists}</td>";
        echo "<td><img src='{$event->image_url}' style='max-width:100px;max-height:60px;' onerror='this.style.border=\"1px solid red\";this.alt=\"FAIL\"'></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='info'>No recent events with images found</div>";
}

echo "<h3>🔧 Recommendations</h3>";
echo "<div class='info'>1. Try creating a new event with an image upload</div>";
echo "<div class='info'>2. Check if the OrganizerEventController is being used correctly</div>";
echo "<div class='info'>3. Verify the form is submitting to the right endpoint</div>";
echo "<div class='info'>4. Check browser developer tools for any JavaScript errors</div>";

?>
