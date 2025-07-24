<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Storage Investigation</h1>";
echo "<style>body{font-family:Arial; background:#222; color:#fff; padding:20px;}</style>";

// Check all possible storage locations
$possiblePaths = [
    'storage/app/public/game_images/',
    'storage/app/game_images/',
    'storage/game_images/',
    'public/storage/game_images/',
    'public/game_images/',
    '../storage/app/public/game_images/',
];

echo "<h2>Checking All Possible Storage Locations</h2>";
foreach ($possiblePaths as $path) {
    $fullPath = __DIR__ . '/' . $path;
    $exists = is_dir($fullPath);
    echo "<strong>$path</strong><br>";
    echo "Full path: $fullPath<br>";
    echo "Exists: " . ($exists ? 'YES' : 'NO') . "<br>";
    
    if ($exists) {
        $files = scandir($fullPath);
        $imageFiles = array_filter($files, function($file) {
            return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
        });
        echo "Image files: " . count($imageFiles) . "<br>";
        if (count($imageFiles) > 0) {
            echo "Files found: " . implode(', ', array_slice($imageFiles, 0, 5)) . "<br>";
        }
    }
    echo "<br>";
}

// Check if we can create test files
echo "<h2>Test File Creation</h2>";
$testDir = __DIR__ . '/../storage/app/public/game_images/';
if (!is_dir($testDir)) {
    mkdir($testDir, 0755, true);
    echo "Created directory: $testDir<br>";
}

// Try to create a test image
$testFile = $testDir . 'test_image.txt';
if (file_put_contents($testFile, 'test content')) {
    echo "✅ Can create files in storage directory<br>";
    unlink($testFile);
} else {
    echo "❌ Cannot create files in storage directory<br>";
}

// Check database for actual image paths
echo "<h2>Check Database Connection</h2>";
try {
    // Simple database check (if we can access it)
    $dbPath = __DIR__ . '/../.env';
    if (file_exists($dbPath)) {
        echo "✅ .env file exists<br>";
        $envContent = file_get_contents($dbPath);
        if (strpos($envContent, 'DB_CONNECTION') !== false) {
            echo "✅ Database configuration found<br>";
        }
    }
} catch (Exception $e) {
    echo "Database check failed: " . $e->getMessage() . "<br>";
}

// Show current working directory and permissions
echo "<h2>System Info</h2>";
echo "Current working directory: " . getcwd() . "<br>";
echo "Script directory: " . __DIR__ . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

$storageDir = __DIR__ . '/../storage/app/public/game_images/';
if (is_dir($storageDir)) {
    echo "Storage permissions: " . substr(sprintf('%o', fileperms($storageDir)), -4) . "<br>";
    echo "Storage owner: " . (function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($storageDir))['name'] : 'unknown') . "<br>";
}
?>
