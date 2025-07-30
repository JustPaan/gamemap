<?php
// Debug avatar issue - check what's in the database vs what's being displayed

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "<h2>Avatar Debug Information</h2>";
echo "<style>body { font-family: Arial; padding: 20px; background: #f5f5f5; }</style>";

// Get all users and their avatar data
$users = User::all();

echo "<h3>All Users Avatar Data:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #ddd;'>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Avatar (DB)</th><th>Avatar URL</th><th>File Exists?</th>";
echo "</tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user->id}</td>";
    echo "<td>{$user->name}</td>";
    echo "<td>{$user->email}</td>";
    echo "<td>" . ($user->avatar ?? 'NULL') . "</td>";
    echo "<td>{$user->avatar_url}</td>";
    
    // Check if file exists
    $fileExists = 'N/A';
    if ($user->avatar) {
        $filename = basename($user->avatar);
        $filePath = __DIR__ . '/storage/app/public/avatars/' . $filename;
        $fileExists = file_exists($filePath) ? '✅ YES' : '❌ NO';
    }
    echo "<td>{$fileExists}</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Storage Directory Check:</h3>";
$avatarDir = __DIR__ . '/storage/app/public/avatars/';
echo "<p><strong>Avatar Directory:</strong> {$avatarDir}</p>";
echo "<p><strong>Directory Exists:</strong> " . (is_dir($avatarDir) ? '✅ YES' : '❌ NO') . "</p>";

if (is_dir($avatarDir)) {
    $files = scandir($avatarDir);
    $files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..', '.gitkeep']);
    });
    
    echo "<p><strong>Files in directory:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        echo "<li>{$file}</li>";
    }
    echo "</ul>";
}

echo "<h3>Session Check (if available):</h3>";
if (function_exists('auth') && auth()->check()) {
    $currentUser = auth()->user();
    echo "<p><strong>Currently logged in user:</strong> {$currentUser->name} (ID: {$currentUser->id})</p>";
    echo "<p><strong>Current user avatar:</strong> " . ($currentUser->avatar ?? 'NULL') . "</p>";
    echo "<p><strong>Current user avatar_url:</strong> {$currentUser->avatar_url}</p>";
} else {
    echo "<p>No user currently logged in (this script runs outside of web context)</p>";
}
?>
