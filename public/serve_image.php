<?php
// Direct image server for Digital Ocean compatibility
// This file serves images from storage without going through Laravel routing

$filename = $_GET['f'] ?? '';

if (empty($filename)) {
    http_response_code(404);
    die('File not specified');
}

// Sanitize filename (security)
$filename = basename($filename);
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    http_response_code(403);
    die('File type not allowed');
}

// Build file path
$filePath = __DIR__ . '/../storage/app/public/game_images/' . $filename;

// Check if file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found');
}

// Set appropriate headers
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

// Output the file
readfile($filePath);
exit;
?>
