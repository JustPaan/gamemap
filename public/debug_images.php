<?php
echo "<h2>Laravel Storage Test - Digital Ocean</h2>";

// Check Laravel storage directory
$laravelStorageDir = __DIR__ . '/../storage/app/public/game_images';
echo "<p><strong>Laravel Storage Directory:</strong> $laravelStorageDir</p>";
echo "<p><strong>Exists:</strong> " . (is_dir($laravelStorageDir) ? "YES" : "NO") . "</p>";
echo "<p><strong>Writable:</strong> " . (is_writable($laravelStorageDir) ? "YES" : "NO") . "</p>";

// Create directory if it doesn't exist
if (!is_dir($laravelStorageDir)) {
    if (mkdir($laravelStorageDir, 0755, true)) {
        echo "<p><strong>✅ Directory created successfully!</strong></p>";
    } else {
        echo "<p><strong>❌ Failed to create directory</strong></p>";
    }
}

// Create a simple test image in Laravel storage
$testImageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAI9jU77yQAAAABJRU5ErkJggg==');
$testImagePath = $laravelStorageDir . '/test-image.png';

if (file_put_contents($testImagePath, $testImageData)) {
    echo "<p><strong>✅ Test image created in Laravel storage</strong></p>";
} else {
    echo "<p><strong>❌ Failed to create test image in Laravel storage</strong></p>";
}

// List files in Laravel storage
if (is_dir($laravelStorageDir)) {
    $files = scandir($laravelStorageDir);
    echo "<p><strong>Files in Laravel storage:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

// Test Laravel route-based image serving
echo "<h3>Laravel Route Image Test:</h3>";
echo "<p>Testing Laravel image serving route:</p>";
echo "<img src='/storage/game_images/test-image.png' alt='Test via Laravel route' style='width:50px;height:50px;border:2px solid green;background:white;'>";

echo "<hr>";
echo "<p><strong>✅ Ready to test:</strong> Upload a game image!</p>";
echo "<p><a href='/admin/game2' style='padding:10px;background:green;color:white;text-decoration:none;'>Go to Game Admin</a></p>";
?>
