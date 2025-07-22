<?php
echo "<h2>Image Debug Test</h2>";

// Check if storage directory exists
$storageDir = __DIR__ . '/storage/game_images';
echo "<p><strong>Storage Directory:</strong> $storageDir</p>";
echo "<p><strong>Exists:</strong> " . (is_dir($storageDir) ? "YES" : "NO") . "</p>";

if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    echo "<p><strong>Files in directory:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>Directory does not exist. Creating it...</p>";
    if (mkdir($storageDir, 0755, true)) {
        echo "<p>Directory created successfully!</p>";
    } else {
        echo "<p>Failed to create directory.</p>";
    }
}

// Test image URL
echo "<p><strong>Test image URL:</strong> /storage/game_images/test.jpg</p>";
echo "<img src='/storage/game_images/test.jpg' alt='Test' style='max-width:200px;border:1px solid red;'>";
?>
