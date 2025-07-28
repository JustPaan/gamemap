<?php
// Quick file upload test
echo "<h2>🔍 Quick File Upload Test</h2>";
echo "<style>body{font-family:Arial;margin:20px;background:#f5f5f5;} .success{color:green;} .error{color:red;} .info{color:blue;} .box{background:white;padding:15px;border-radius:5px;margin:10px 0;border:1px solid #ddd;}</style>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    
    echo "<div class='box'>";
    echo "<h3>📁 Upload Results</h3>";
    echo "<div class='info'><strong>Filename:</strong> " . htmlspecialchars($file['name']) . "</div>";
    echo "<div class='info'><strong>Size:</strong> " . number_format($file['size']) . " bytes</div>";
    echo "<div class='info'><strong>Type:</strong> " . htmlspecialchars($file['type']) . "</div>";
    echo "<div class='info'><strong>Error Code:</strong> " . $file['error'] . "</div>";
    
    // Analyze filename
    $filename = $file['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    echo "<div class='info'><strong>Extension:</strong> " . htmlspecialchars($ext) . "</div>";
    
    if ($ext === 'tjpeg') {
        echo "<div class='error'>❌ PROBLEM FOUND: '.tjpeg' is not a valid image extension!</div>";
        echo "<div class='info'>💡 SOLUTION: Rename your file to end with '.jpg' or '.jpeg'</div>";
    } elseif (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "<div class='success'>✅ File extension looks good</div>";
    } else {
        echo "<div class='error'>❌ Unsupported file type</div>";
    }
    
    if ($file['error'] === 0) {
        echo "<div class='success'>✅ File uploaded successfully to temporary location</div>";
    } else {
        echo "<div class='error'>❌ Upload error: " . $file['error'] . "</div>";
    }
    echo "</div>";
}

echo "<div class='box'>";
echo "<h3>🧪 Test Your File</h3>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file' accept='image/*' style='margin:10px 0;'><br>";
echo "<button type='submit' style='background:#4CAF50;color:white;padding:10px 20px;border:none;border-radius:3px;cursor:pointer;'>Test Upload</button>";
echo "</form>";
echo "</div>";

echo "<div class='box'>";
echo "<h3>🔧 Common Issues & Quick Fixes</h3>";
echo "<div class='error'><strong>Issue: 'coc.tjpeg' filename</strong></div>";
echo "<div class='info'>This means your file has a corrupted extension.</div>";
echo "<div class='success'><strong>Quick Fix:</strong></div>";
echo "<ol>";
echo "<li>Right-click your image file</li>";
echo "<li>Select 'Rename'</li>";
echo "<li>Change 'coc.tjpeg' to 'coc.jpg' or 'coc-event.jpg'</li>";
echo "<li>Try uploading again</li>";
echo "</ol>";
echo "</div>";

?>
