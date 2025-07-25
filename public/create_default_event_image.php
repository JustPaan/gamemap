<?php
// Create a simple default event image if it doesn't exist
$publicDir = __DIR__ . '/images/';
$defaultEventImage = $publicDir . 'default-event.jpg';

if (!is_dir($publicDir)) {
    mkdir($publicDir, 0755, true);
}

if (!file_exists($defaultEventImage)) {
    // Create a simple colored rectangle as default image
    $width = 300;
    $height = 200;
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg_color = imagecolorallocate($image, 28, 28, 34); // Dark background
    $border_color = imagecolorallocate($image, 76, 175, 80); // Green border
    $text_color = imagecolorallocate($image, 255, 255, 255); // White text
    
    // Fill background
    imagefill($image, 0, 0, $bg_color);
    
    // Draw border
    imagerectangle($image, 0, 0, $width-1, $height-1, $border_color);
    imagerectangle($image, 1, 1, $width-2, $height-2, $border_color);
    
    // Add text
    $font_size = 5;
    $text = "EVENT IMAGE";
    $text_width = strlen($text) * imagefontwidth($font_size);
    $text_height = imagefontheight($font_size);
    $text_x = ($width - $text_width) / 2;
    $text_y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $text_x, $text_y, $text, $text_color);
    
    // Save image
    imagejpeg($image, $defaultEventImage, 90);
    imagedestroy($image);
    
    echo "✅ Created default event image: {$defaultEventImage}";
} else {
    echo "ℹ️ Default event image already exists: {$defaultEventImage}";
}

echo "<br><a href='/images/default-event.jpg'>🖼️ View Default Event Image</a>";
echo "<br><a href='javascript:history.back()'>← Go Back</a>";
?>
