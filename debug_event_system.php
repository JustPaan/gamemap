<?php
// Enhanced diagnostic for event image system with upload test
echo "<h2>🔍 Event Image System Diagnostic + Upload Test</h2>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .box{background:#f9f9f9;padding:15px;border:1px solid #ddd;margin:10px 0;border-radius:5px;}</style>";

// Handle file upload test first
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<div class='box'>";
    echo "<h3>🧪 Upload Test Results</h3>";
    
    $file = $_FILES['test_file'];
    echo "<div class='info'><strong>Filename:</strong> " . htmlspecialchars($file['name']) . "</div>";
    echo "<div class='info'><strong>Size:</strong> " . number_format($file['size']) . " bytes</div>";
    echo "<div class='info'><strong>Type:</strong> " . htmlspecialchars($file['type']) . "</div>";
    echo "<div class='info'><strong>Error Code:</strong> " . $file['error'] . "</div>";
    
    $filename = $file['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    echo "<div class='info'><strong>Extension:</strong> " . htmlspecialchars($ext) . "</div>";
    
    if ($ext === 'tjpeg') {
        echo "<div class='error'>❌ PROBLEM FOUND: '.tjpeg' is not a valid image extension!</div>";
        echo "<div class='success'>💡 SOLUTION: Rename your file to end with '.jpg' or '.jpeg'</div>";
    } elseif (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "<div class='success'>✅ File extension looks good</div>";
    } else {
        echo "<div class='error'>❌ Unsupported file type</div>";
    }
    
    if ($file['error'] === 0) {
        echo "<div class='success'>✅ File uploaded successfully to temporary location</div>";
        
        // Test if it's a real image
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        echo "<div class='info'><strong>Real MIME Type:</strong> " . $realMimeType . "</div>";
        
        if (strpos($realMimeType, 'image/') === 0) {
            echo "<div class='success'>✅ File is a valid image</div>";
        } else {
            echo "<div class='error'>❌ File is not a valid image</div>";
        }
    } else {
        echo "<div class='error'>❌ Upload error: " . $file['error'] . "</div>";
    }
    echo "</div>";
}

// Upload test form
echo "<div class='box'>";
echo "<h3>🧪 Test Your File Upload</h3>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file' accept='image/*' style='margin:10px 0;'><br>";
echo "<button type='submit' style='background:#4CAF50;color:white;padding:10px 20px;border:none;border-radius:3px;cursor:pointer;'>Test Upload</button>";
echo "</form>";
echo "<div class='info'><strong>Instructions:</strong> Select your 'coc.tjpeg' file and click Test Upload to see what's wrong</div>";
echo "</div>";

// Rest of the original diagnostic code...

// Check directory structure
echo "<h3>📁 Directory Status</h3>";

$storageDir = __DIR__ . '/storage/app/public/';
$eventImagesDir = $storageDir . 'event_images/';
$gameImagesDir = $storageDir . 'game_images/';

echo "<div class='info'>Storage Directory: {$storageDir}</div>";
echo "<div class='info'>Event Images Directory: {$eventImagesDir}</div>";
echo "<div class='info'>Game Images Directory: {$gameImagesDir}</div>";

if (is_dir($eventImagesDir)) {
    echo "<div class='success'>✅ event_images directory EXISTS</div>";
    $eventFiles = glob($eventImagesDir . '*');
    echo "<div class='info'>Files in event_images: " . count($eventFiles) . "</div>";
    
    if (count($eventFiles) > 0) {
        echo "<h4>📄 Recent files in event_images:</h4>";
        echo "<ul>";
        $recentFiles = array_slice($eventFiles, -10); // Last 10 files
        foreach ($recentFiles as $file) {
            $filename = basename($file);
            $size = filesize($file);
            $modified = date('Y-m-d H:i:s', filemtime($file));
            echo "<li>{$filename} ({$size} bytes, modified: {$modified})</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<div class='error'>❌ event_images directory MISSING</div>";
}

if (is_dir($gameImagesDir)) {
    echo "<div class='success'>✅ game_images directory EXISTS</div>";
    $gameFiles = glob($gameImagesDir . '*');
    echo "<div class='info'>Files in game_images: " . count($gameFiles) . "</div>";
} else {
    echo "<div class='error'>❌ game_images directory MISSING</div>";
}

// Check serve files
echo "<h3>🔧 Serving Scripts Status</h3>";

$serveEventScript = __DIR__ . '/public/serve_event_image.php';
$serveGameScript = __DIR__ . '/public/serve_image.php';

if (file_exists($serveEventScript)) {
    echo "<div class='success'>✅ serve_event_image.php EXISTS</div>";
} else {
    echo "<div class='error'>❌ serve_event_image.php MISSING</div>";
}

if (file_exists($serveGameScript)) {
    echo "<div class='success'>✅ serve_image.php EXISTS</div>";
} else {
    echo "<div class='error'>❌ serve_image.php MISSING</div>";
}

// Check if we can connect to database
echo "<h3>🗄️ Database Connection Test</h3>";

try {
    // Read database config
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        
        // Extract database info
        preg_match('/DB_HOST=(.*)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.*)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.*)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        
        $host = trim($hostMatch[1] ?? '');
        $database = trim($dbMatch[1] ?? '');
        $username = trim($userMatch[1] ?? '');
        $password = trim($passMatch[1] ?? '');
        
        if ($host && $database && $username) {
            echo "<div class='info'>Attempting to connect to: {$host}/{$database}</div>";
            
            $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<div class='success'>✅ Database connection successful</div>";
            
            // Check recent events
            $stmt = $pdo->query("SELECT id, title, image_path, created_at FROM events ORDER BY created_at DESC LIMIT 5");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($events) {
                echo "<h4>📋 Recent Events:</h4>";
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>Created</th><th>File Exists</th></tr>";
                
                foreach ($events as $event) {
                    $imageExists = '❌';
                    if ($event['image_path']) {
                        $filename = basename($event['image_path']);
                        $fullPath = $eventImagesDir . $filename;
                        $imageExists = file_exists($fullPath) ? '✅' : '❌';
                    }
                    
                    echo "<tr>";
                    echo "<td>{$event['id']}</td>";
                    echo "<td>" . htmlspecialchars($event['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['image_path'] ?: 'NULL') . "</td>";
                    echo "<td>{$event['created_at']}</td>";
                    echo "<td>{$imageExists}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='info'>No events found in database</div>";
            }
            
        } else {
            echo "<div class='error'>❌ Database configuration incomplete</div>";
        }
        
    } else {
        echo "<div class='error'>❌ .env file not found</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<h3>💡 Next Steps</h3>";
echo "<div class='info'>1. Go to the organizer event creation page</div>";
echo "<div class='info'>2. Create a new event with an image upload</div>";
echo "<div class='info'>3. Refresh this page to see if the new file appears</div>";
echo "<div class='info'>4. Check if the image displays on the events page</div>";

echo "<hr>";
echo "<div class='info'>Page generated at: " . date('Y-m-d H:i:s') . "</div>";
?>
