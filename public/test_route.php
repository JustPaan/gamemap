<?php
// Simple test file to verify image serving
echo "<h2>Image Route Test</h2>";

// Test if our route works
echo "<h3>Testing Image Route:</h3>";
echo "<p>Testing route: <a href='/storage/game_images/test-image.png'>/storage/game_images/test-image.png</a></p>";

// Check if files exist
$testImagePath = __DIR__ . '/../storage/app/public/game_images/test-image.png';
echo "<p>File exists: " . (file_exists($testImagePath) ? "YES" : "NO") . "</p>";
echo "<p>File path: $testImagePath</p>";

if (file_exists($testImagePath)) {
    echo "<img src='/storage/game_images/test-image.png' style='border: 2px solid red; width: 100px; height: 100px;'>";
}

// Check all files in directory
$gameImagesDir = __DIR__ . '/../storage/app/public/game_images';
if (is_dir($gameImagesDir)) {
    $files = scandir($gameImagesDir);
    echo "<h3>Files in storage:</h3><ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file - <a href='/storage/game_images/$file'>View</a></li>";
        }
    }
    echo "</ul>";
}
?>
