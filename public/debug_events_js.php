<?php
// Debug events data for JavaScript
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;

echo "<h1>Events Data Debug</h1>";
echo "<style>body{font-family:Arial;margin:20px;} pre{background:#f5f5f5;padding:15px;overflow:auto;}</style>";

// Get the same events as in HomeController
$events = Event::with('game')
    ->where(function($query) {
        $query->where('start_date', '>', now()) // Upcoming events
              ->orWhere(function($subQuery) {
                  $subQuery->where('start_date', '<=', now())
                            ->where('end_date', '>=', now()); // Ongoing events
              });
    })
    ->get();

echo "<h2>Events Count: " . count($events) . "</h2>";

if (count($events) > 0) {
    echo "<h3>Sample Event Data (JSON format like in JavaScript):</h3>";
    $firstEvent = $events->first();
    echo "<pre>" . json_encode($firstEvent, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h3>Image URL Check:</h3>";
    echo "<strong>Event ID:</strong> {$firstEvent->id}<br>";
    echo "<strong>Title:</strong> {$firstEvent->title}<br>";
    echo "<strong>Image Path in DB:</strong> " . ($firstEvent->image_path ?: 'NULL') . "<br>";
    echo "<strong>Image URL Accessor:</strong> {$firstEvent->image_url}<br>";
    
    echo "<h3>All Events - Image URLs:</h3>";
    foreach ($events as $event) {
        echo "<div style='border:1px solid #ccc;margin:5px 0;padding:10px;'>";
        echo "<strong>{$event->title} (ID: {$event->id})</strong><br>";
        echo "DB Path: <code>" . ($event->image_path ?: 'NULL') . "</code><br>";
        echo "Image URL: <code>{$event->image_url}</code><br>";
        echo "Game: " . ($event->game ? $event->game->name : 'No game') . "<br>";
        echo "</div>";
    }
    
    echo "<h3>JavaScript Test:</h3>";
    echo "<script>";
    echo "const events = " . json_encode($events) . ";";
    echo "console.log('Events data:', events);";
    echo "if (events.length > 0) {";
    echo "  console.log('First event image_url:', events[0].image_url);";
    echo "  console.log('First event full data:', events[0]);";
    echo "}";
    echo "</script>";
    echo "<p>Check browser console (F12) for JavaScript data.</p>";
    
} else {
    echo "<p>No upcoming or ongoing events found.</p>";
}

echo "<br><a href='/debug_event_images.php'>🔍 Debug Event Images</a>";
echo "<br><a href='javascript:history.back()'>← Go Back</a>";
?>
