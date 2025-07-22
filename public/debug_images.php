<?php
echo "<h2>Image Debug Test - Updated</h2>";

// Check if storage directory exists
$storageDir = __DIR__ . '/storage/game_images';
echo "<p><strong>Storage Directory:</strong> $storageDir</p>";
echo "<p><strong>Exists:</strong> " . (is_dir($storageDir) ? "YES" : "NO") . "</p>";
echo "<p><strong>Writable:</strong> " . (is_writable($storageDir) ? "YES" : "NO") . "</p>";

// Create a simple test image (1x1 pixel PNG)
$testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jU77yQAAAABJRU5ErkJggg==');
$testImagePath = $storageDir . '/test-image.png';

if (file_put_contents($testImagePath, $testImageData)) {
    echo "<p><strong>✅ Test image created:</strong> test-image.png</p>";
} else {
    echo "<p><strong>❌ Failed to create test image</strong></p>";
}

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
}

// Test image display
echo "<h3>Image Display Test:</h3>";
echo "<p>If you see a small dot below, images are working:</p>";
echo "<img src='/storage/game_images/test-image.png' alt='Test' style='width:50px;height:50px;border:2px solid red;background:white;'>";

echo "<hr>";
echo "<p><strong>Next step:</strong> Try uploading a game image in the admin panel!</p>";
echo "<p><a href='/admin/game2' style='padding:10px;background:blue;color:white;text-decoration:none;'>Go to Game Admin</a></p>";
?>
