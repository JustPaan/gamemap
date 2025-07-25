<?php
// Direct event image server for Digital Ocean compatibility
// Serves images from storage/app/public/event_images/

$filename = $_GET['f'] ?? '';

if (empty($filename)) {
    http_response_code(400);
    exit('No file specified');
}

// Security: Only allow safe filenames
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
    http_response_code(400);
    exit('Invalid filename');
}

// Construct file path
$filePath = __DIR__ . '/../storage/app/public/event_images/' . $filename;

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

// Security: Only allow image files
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    exit('File type not allowed');
}

// Get mime type
$mimeType = mime_content_type($filePath);
if ($mimeType === false) {
    $mimeType = 'application/octet-stream';
}

// Set headers for caching and content type
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

// Output the file
readfile($filePath);
exit;
?>
