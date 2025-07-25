<?php
// Simple file system check
echo "<h2>File System Check</h2>";
echo "<style>body{font-family:Arial;margin:20px;} .dir{background:#f0f0f0;padding:10px;margin:10px 0;} .file{color:blue;} .notfound{color:red;}</style>";

$basePath = __DIR__ . '/../storage/app/public/';
$dirs = ['game_images', 'event_images', 'avatars'];

foreach ($dirs as $dir) {
    $fullPath = $basePath . $dir;
    echo "<div class='dir'>";
    echo "<h3>Directory: {$dir}</h3>";
    echo "Path: <code>{$fullPath}</code><br>";
    
    if (is_dir($fullPath)) {
        echo "<span style='color:green'>✅ EXISTS</span><br>";
        $files = scandir($fullPath);
        $files = array_filter($files, function($f) { return !in_array($f, ['.', '..']); });
        echo "Files: " . count($files) . "<br>";
        
        if (count($files) > 0) {
            echo "<strong>Files found:</strong><br>";
            foreach (array_slice($files, 0, 10) as $file) {
                $filePath = $fullPath . '/' . $file;
                $size = number_format(filesize($filePath) / 1024, 2);
                echo "• <span class='file'>{$file}</span> ({$size} KB)<br>";
            }
            if (count($files) > 10) {
                echo "... and " . (count($files) - 10) . " more files<br>";
            }
        }
    } else {
        echo "<span class='notfound'>❌ NOT FOUND</span><br>";
    }
    echo "</div>";
}

// Try to create missing directories
echo "<h3>Attempting to create missing directories...</h3>";
foreach ($dirs as $dir) {
    $fullPath = $basePath . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created: {$dir}<br>";
        } else {
            echo "❌ Failed to create: {$dir}<br>";
        }
    }
}
?>
