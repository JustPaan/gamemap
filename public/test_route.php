<?php
// Test direct image server for Digital Ocean
echo "<h2>Direct Image Server Test</h2>";

// Check storage directory
$storageDir = __DIR__ . '/../storage/app/public/game_images';
echo "<p><strong>Storage directory:</strong> $storageDir</p>";
echo "<p><strong>Directory exists:</strong> " . (is_dir($storageDir) ? "YES" : "NO") . "</p>";

// Create test image if missing
$testImagePath = $storageDir . '/test-image.png';
if (!file_exists($testImagePath)) {
    echo "<p>Creating test image...</p>";
    $testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jU77yQAAAABJRU5ErkJggg==');
    if (file_put_contents($testImagePath, $testImageData)) {
        echo "<p>✅ Test image created!</p>";
    }
}

// Test direct image server
echo "<h3>Testing Direct Image Server:</h3>";
echo "<p>Direct server URL: <a href='/serve_image.php?f=test-image.png' target='_blank'>/serve_image.php?f=test-image.png</a></p>";

echo "<p>Image should display below via direct server:</p>";
echo "<img src='/serve_image.php?f=test-image.png' style='border: 2px solid green; width: 100px; height: 100px; background: white;'>";

// List all files with direct server links
if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    echo "<h3>All files (via direct server):</h3><ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && $file != '.gitkeep') {
            echo "<li>$file - <a href='/serve_image.php?f=$file' target='_blank'>View Direct</a></li>";
        }
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><strong>If image displays above, the system is working!</strong></p>";
echo "<p><a href='/admin/game2'>Go back to Admin</a></p>";
?>
