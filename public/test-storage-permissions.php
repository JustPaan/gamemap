<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Storage Permission Test</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .test-box{border:1px solid #ccc;padding:15px;margin:10px 0;border-radius:8px;} .success{border-color:green;background:#f0f8f0;} .error{border-color:red;background:#f8f0f0;}</style>";

// Test storage directories
$directories = [
    'storage/app/public' => storage_path('app/public'),
    'storage/app/public/game_images' => storage_path('app/public/game_images'),
    'public/storage' => public_path('storage'),
    'public/storage/game_images' => public_path('storage/game_images'),
];

echo "<div class='test-box'>";
echo "<h3>Directory Permissions</h3>";

foreach ($directories as $name => $path) {
    $exists = is_dir($path);
    $writable = is_writable($path);
    
    echo "<p><strong>{$name}:</strong> ";
    if ($exists) {
        echo "✅ EXISTS ";
        if ($writable) {
            echo "✅ WRITABLE";
        } else {
            echo "❌ NOT WRITABLE";
        }
    } else {
        echo "❌ DOES NOT EXIST";
    }
    echo "</p>";
}
echo "</div>";

// Test file upload simulation
echo "<div class='test-box'>";
echo "<h3>File Upload Test</h3>";

try {
    // Create a small test image
    $testImagePath = storage_path('app/public/game_images/test_upload.jpg');
    
    // Simple 1x1 pixel JPEG
    $jpegData = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00H\x00H\x00\x00\xFF\xDB\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t\x08\n\x0C\x14\r\x0C\x0B\x0B\x0C\x19\x12\x13\x0F\x14\x1D\x1A\x1F\x1E\x1D\x1A\x1C\x1C $.' \",#\x1C\x1C(7),01444\x1F'9=82<.342\xFF\xC0\x00\x11\x08\x00\x01\x00\x01\x01\x01\x11\x00\x02\x11\x01\x03\x11\x01\xFF\xC4\x00\x1F\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0B\xFF\xC4\x00\xB5\x10\x00\x02\x01\x03\x03\x02\x04\x03\x05\x05\x04\x04\x00\x00\x01}\x01\x02\x03\x00\x04\x11\x05\x12!1A\x06\x13Qa\x07\"q\x142\x81\x91\xA1\x08#B\xB1\xC1\x15R\xD1\xF0$3br\x82\t\n\x16\x17\x18\x19\x1A%&'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz\x83\x84\x85\x86\x87\x88\x89\x8A\x92\x93\x94\x95\x96\x97\x98\x99\x9A\xA2\xA3\xA4\xA5\xA6\xA7\xA8\xA9\xAA\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA\xC2\xC3\xC4\xC5\xC6\xC7\xC8\xC9\xCA\xD2\xD3\xD4\xD5\xD6\xD7\xD8\xD9\xDA\xE1\xE2\xE3\xE4\xE5\xE6\xE7\xE8\xE9\xEA\xF1\xF2\xF3\xF4\xF5\xF6\xF7\xF8\xF9\xFA\xFF\xDA\x00\x08\x01\x01\x00\x00?\x00\xFE\xAF\xFF\xD9";
    
    $success = file_put_contents($testImagePath, $jpegData);
    
    if ($success) {
        echo "<p>✅ Test image created successfully</p>";
        echo "<p><strong>Path:</strong> {$testImagePath}</p>";
        echo "<p><strong>Size:</strong> " . filesize($testImagePath) . " bytes</p>";
        
        // Test if it can be accessed via web
        $publicTestPath = public_path('storage/game_images/test_upload.jpg');
        if (file_exists($publicTestPath)) {
            echo "<p>✅ File accessible via public symlink</p>";
        } else {
            echo "<p>❌ File NOT accessible via public symlink</p>";
            // Try to copy manually
            if (copy($testImagePath, $publicTestPath)) {
                echo "<p>✅ Manual copy to public successful</p>";
            } else {
                echo "<p>❌ Manual copy to public failed</p>";
            }
        }
        
        // Clean up
        @unlink($testImagePath);
        @unlink($publicTestPath);
        
    } else {
        echo "<p>❌ Failed to create test image</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Check recent upload errors
echo "<div class='test-box'>";
echo "<h3>Recent Error Logs</h3>";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $lines = explode("\n", $logs);
    $recentLines = array_slice($lines, -20); // Last 20 lines
    
    $hasUploadErrors = false;
    foreach ($recentLines as $line) {
        if (stripos($line, 'upload') !== false || stripos($line, 'image') !== false || stripos($line, 'storage') !== false) {
            echo "<p style='font-size: 12px; color: #666;'>" . htmlspecialchars($line) . "</p>";
            $hasUploadErrors = true;
        }
    }
    
    if (!$hasUploadErrors) {
        echo "<p>No recent upload-related errors found</p>";
    }
} else {
    echo "<p>No log file found</p>";
}
echo "</div>";
?>
