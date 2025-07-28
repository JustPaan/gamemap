<?php
// Event creation diagnostic
echo "<h2>🔍 Event Creation Diagnostic</h2>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

echo "<h3>📝 Form Submission Test</h3>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div class='info'><strong>Form submitted!</strong></div>";
    
    if (isset($_FILES['test_image'])) {
        $file = $_FILES['test_image'];
        
        echo "<h4>📁 File Upload Details:</h4>";
        echo "<div class='info'>Original Name: " . htmlspecialchars($file['name']) . "</div>";
        echo "<div class='info'>MIME Type: " . htmlspecialchars($file['type']) . "</div>";
        echo "<div class='info'>Size: " . $file['size'] . " bytes</div>";
        echo "<div class='info'>Temp Name: " . htmlspecialchars($file['tmp_name']) . "</div>";
        echo "<div class='info'>Error Code: " . $file['error'] . "</div>";
        
        // Check filename issues
        $filename = $file['name'];
        echo "<h4>🔍 Filename Analysis:</h4>";
        echo "<div class='info'>Length: " . strlen($filename) . " characters</div>";
        echo "<div class='info'>Extension: " . pathinfo($filename, PATHINFO_EXTENSION) . "</div>";
        echo "<div class='info'>Basename: " . pathinfo($filename, PATHINFO_FILENAME) . "</div>";
        
        // Check for special characters
        if (preg_match('/[^a-zA-Z0-9_\-\s\.]/', $filename)) {
            echo "<div class='error'>⚠️ Filename contains special characters</div>";
        } else {
            echo "<div class='success'>✅ Filename looks clean</div>";
        }
        
        // Check file content
        if ($file['error'] === 0 && is_uploaded_file($file['tmp_name'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $realMimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            echo "<div class='info'>Real MIME Type: " . $realMimeType . "</div>";
            
            if (strpos($realMimeType, 'image/') === 0) {
                echo "<div class='success'>✅ File is a valid image</div>";
            } else {
                echo "<div class='error'>❌ File is not a valid image</div>";
            }
        }
        
    } else {
        echo "<div class='error'>❌ No file uploaded</div>";
    }
} else {
    echo "<div class='info'>Use the form below to test file upload</div>";
}

echo "<h3>🧪 Test Upload Form</h3>";
?>

<form method="POST" enctype="multipart/form-data" style="border: 1px solid #ddd; padding: 20px; border-radius: 5px;">
    <div style="margin-bottom: 15px;">
        <label for="test_image">Select Image File:</label><br>
        <input type="file" id="test_image" name="test_image" accept="image/*" style="margin-top: 5px;">
    </div>
    <div style="margin-bottom: 15px;">
        <button type="submit" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;">Test Upload</button>
    </div>
</form>

<h3>💡 Common Issues & Solutions</h3>
<div class="info">
<strong>1. Corrupted filename extension:</strong><br>
- "coc.tjpeg" suggests the file extension is corrupted<br>
- Rename the file to have a proper extension like .jpg, .jpeg, .png<br><br>

<strong>2. Special characters in filename:</strong><br>
- Avoid spaces, special characters, non-English letters<br>
- Use only: letters, numbers, hyphens, underscores<br><br>

<strong>3. File size issues:</strong><br>
- Maximum file size: 2MB<br>
- Recommended: 1200x630 pixels<br><br>

<strong>4. Unsupported format:</strong><br>
- Only JPG, JPEG, PNG, GIF are supported<br>
- WebP and other formats may not work<br>
</div>

<h3>📋 Quick Fix Steps</h3>
<div class="info">
1. Rename your image file to something simple like "event-image.jpg"<br>
2. Make sure the file extension is correct (.jpg, .png, etc.)<br>
3. Check the file size is under 2MB<br>
4. Try uploading again in the event creation form<br>
</div>

<?php
echo "<hr>";
echo "<div class='info'>Test page generated at: " . date('Y-m-d H:i:s') . "</div>";
?>
