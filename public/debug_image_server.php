<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Image Server Debug</h1>";
echo "<style>body{font-family:Arial; background:#222; color:#fff; padding:20px;}</style>";

// Test 1: Check if serve_image.php exists
echo "<h2>1. File Existence Check</h2>";
$serveImagePath = __DIR__ . '/serve_image.php';
echo "Looking for: " . $serveImagePath . "<br>";
echo "Exists: " . (file_exists($serveImagePath) ? 'YES' : 'NO') . "<br><br>";

// Test 2: Check storage directory
echo "<h2>2. Storage Directory Check</h2>";
$storageDir = dirname(__DIR__) . '/storage/app/public/game_images';
echo "Storage dir: " . $storageDir . "<br>";
echo "Exists: " . (is_dir($storageDir) ? 'YES' : 'NO') . "<br>";
if (is_dir($storageDir)) {
    $files = scandir($storageDir);
    echo "Files count: " . (count($files) - 2) . "<br>";
    echo "First 5 files: <br>";
    foreach (array_slice($files, 2, 5) as $file) {
        echo "- " . $file . "<br>";
    }
}
echo "<br>";

// Test 3: Direct serve_image.php test
echo "<h2>3. Direct Server Test</h2>";
echo '<a href="/serve_image.php?file=1753160574_cs.jpeg" target="_blank" style="color:#4CAF50;">Test CS Image</a><br>';
echo '<a href="/serve_image.php?file=1753160386_valo.jpg" target="_blank" style="color:#4CAF50;">Test Valorant Image</a><br>';
echo '<a href="/serve_image.php?file=1753160243_coc.png" target="_blank" style="color:#4CAF50;">Test COC Image</a><br><br>';

// Test 4: Image with full path
echo "<h2>4. Test Images with Full HTML</h2>";
$testImages = [
    '1753160574_cs.jpeg',
    '1753160386_valo.jpg', 
    '1753160243_coc.png'
];

foreach ($testImages as $filename) {
    echo "<div style='margin:10px; padding:10px; border:1px solid #4CAF50;'>";
    echo "<strong>File: " . $filename . "</strong><br>";
    echo "<img src='/serve_image.php?file=" . $filename . "' alt='" . $filename . "' style='max-width:100px; max-height:100px; border:2px solid red;' 
           onerror=\"this.style.border='2px solid red'; this.alt='FAILED: " . $filename . "';\">";
    echo "</div>";
}

// Test 5: PHP version and error log
echo "<h2>5. Server Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' . "<br>";

// Test 6: Check serve_image.php content
echo "<h2>6. serve_image.php Content Check</h2>";
if (file_exists($serveImagePath)) {
    $content = file_get_contents($serveImagePath);
    echo "File size: " . strlen($content) . " bytes<br>";
    echo "First 200 characters:<br>";
    echo "<pre style='background:#111; padding:10px; overflow:auto;'>" . htmlspecialchars(substr($content, 0, 200)) . "...</pre>";
} else {
    echo "serve_image.php NOT FOUND!<br>";
}
?>
