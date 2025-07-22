<?php
// Simple test file to verify image serving
echo "<h2>Image Route Test - Enhanced</h2>";

// Check storage directory first
$storageDir = __DIR__ . '/../storage/app/public/game_images';
echo "<p><strong>Storage directory:</strong> $storageDir</p>";
echo "<p><strong>Directory exists:</strong> " . (is_dir($storageDir) ? "YES" : "NO") . "</p>";

// Create directory if missing
if (!is_dir($storageDir)) {
    if (mkdir($storageDir, 0755, true)) {
        echo "<p>✅ Directory created!</p>";
    } else {
        echo "<p>❌ Failed to create directory</p>";
    }
}

// Check if test image exists, create if not
$testImagePath = $storageDir . '/test-image.png';
echo "<p><strong>Test image exists:</strong> " . (file_exists($testImagePath) ? "YES" : "NO") . "</p>";

if (!file_exists($testImagePath)) {
    echo "<p>Creating test image...</p>";
    $testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jU77yQAAAABJRU5ErkJggg==');
    if (file_put_contents($testImagePath, $testImageData)) {
        echo "<p>✅ Test image created!</p>";
    } else {
        echo "<p>❌ Failed to create test image</p>";
    }
}

// Test the route
echo "<h3>Testing Image Route:</h3>";
echo "<p>Route URL: <a href='/storage/game_images/test-image.png'>/storage/game_images/test-image.png</a></p>";

if (file_exists($testImagePath)) {
    echo "<p>Image should display below:</p>";
    echo "<img src='/storage/game_images/test-image.png' style='border: 2px solid red; width: 100px; height: 100px; background: white;'>";
} else {
    echo "<p>❌ Cannot test route - image file missing</p>";
}

// List all files in directory
if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    echo "<h3>All files in storage:</h3><ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file - <a href='/storage/game_images/$file' target='_blank'>View</a></li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>❌ Storage directory doesn't exist</p>";
}

echo "<hr>";
echo "<p><a href='/admin/game2'>Go back to Admin</a></p>";
?>
