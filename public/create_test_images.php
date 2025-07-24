<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Create Test Images</h1>";
echo "<style>body{font-family:Arial; background:#222; color:#fff; padding:20px;}</style>";

// Create storage directory if it doesn't exist
$storageDir = __DIR__ . '/../storage/app/public/game_images/';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
    echo "✅ Created storage directory<br>";
}

// Create simple test images (colored squares)
function createTestImage($filename, $color, $text) {
    global $storageDir;
    
    $width = 200;
    $height = 200;
    
    // Create image
    $image = imagecreate($width, $height);
    
    // Define colors
    $colors = [
        'red' => [255, 0, 0],
        'green' => [0, 255, 0], 
        'blue' => [0, 0, 255],
        'yellow' => [255, 255, 0]
    ];
    
    $rgb = $colors[$color] ?? [128, 128, 128];
    $bg_color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    
    // Fill background
    imagefill($image, 0, 0, $bg_color);
    
    // Add text
    imagestring($image, 5, 50, 90, $text, $text_color);
    
    // Save image
    $filepath = $storageDir . $filename;
    $success = imagejpeg($image, $filepath, 90);
    imagedestroy($image);
    
    return $success;
}

// Create test images that match our database filenames
$testImages = [
    ['1753160574_cs.jpeg', 'blue', 'CS'],
    ['1753160386_valo.jpg', 'red', 'VALORANT'], 
    ['1753160243_coc.png', 'yellow', 'COC'],
    ['test_image_1.jpg', 'green', 'TEST 1']
];

echo "<h2>Creating Test Images</h2>";
foreach ($testImages as [$filename, $color, $text]) {
    if (createTestImage($filename, $color, $text)) {
        echo "✅ Created: $filename<br>";
    } else {
        echo "❌ Failed to create: $filename<br>";
    }
}

// Verify files exist
echo "<h2>Verification</h2>";
$files = scandir($storageDir);
$imageFiles = array_filter($files, function($file) {
    return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
});

echo "Total image files created: " . count($imageFiles) . "<br>";
foreach ($imageFiles as $file) {
    $size = filesize($storageDir . $file);
    echo "- $file ($size bytes)<br>";
}

// Test serve_image.php with new files
echo "<h2>Test Links</h2>";
foreach ($imageFiles as $file) {
    echo "<a href='/serve_image.php?file=$file' target='_blank' style='color:#4CAF50;'>Test $file</a><br>";
}

echo "<h2>Test Images Display</h2>";
foreach ($imageFiles as $file) {
    echo "<div style='margin:10px; display:inline-block;'>";
    echo "<img src='/serve_image.php?file=$file' style='max-width:100px; border:2px solid #4CAF50;'><br>";
    echo "<small>$file</small>";
    echo "</div>";
}
?>
