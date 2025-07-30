<?php
// Debug authentication and user consistency

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "<h2>User Authentication Debug</h2>";
echo "<style>body { font-family: Arial; padding: 20px; background: #f5f5f5; }</style>";

// Check for duplicate users by email
echo "<h3>Checking for Duplicate Users:</h3>";

$duplicateEmails = User::select('email')
    ->groupBy('email')
    ->havingRaw('COUNT(*) > 1')
    ->pluck('email');

if ($duplicateEmails->count() > 0) {
    echo "<p style='color: red;'><strong>⚠️ Found duplicate email addresses:</strong></p>";
    
    foreach ($duplicateEmails as $email) {
        $users = User::where('email', $email)->get();
        echo "<div style='border: 1px solid red; padding: 10px; margin: 10px 0;'>";
        echo "<h4>Email: {$email}</h4>";
        
        foreach ($users as $user) {
            echo "<p>";
            echo "<strong>ID:</strong> {$user->id} | ";
            echo "<strong>Name:</strong> {$user->name} | ";
            echo "<strong>Created:</strong> {$user->created_at} | ";
            echo "<strong>Avatar:</strong> " . ($user->avatar ?? 'NULL') . " | ";
            echo "<strong>Role:</strong> {$user->role}";
            echo "</p>";
        }
        echo "</div>";
    }
} else {
    echo "<p style='color: green;'>✅ No duplicate email addresses found</p>";
}

// Show recent logins/user activity
echo "<h3>Recent User Activity:</h3>";
$recentUsers = User::whereNotNull('last_active_at')
    ->orderBy('last_active_at', 'desc')
    ->take(10)
    ->get();

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #ddd;'>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Last Active</th><th>Avatar</th><th>Created</th>";
echo "</tr>";

foreach ($recentUsers as $user) {
    echo "<tr>";
    echo "<td>{$user->id}</td>";
    echo "<td>{$user->name}</td>";
    echo "<td>{$user->email}</td>";
    echo "<td>" . ($user->last_active_at ? $user->last_active_at->format('Y-m-d H:i:s') : 'Never') . "</td>";
    echo "<td>" . ($user->avatar ? '✅ Has Avatar' : '❌ No Avatar') . "</td>";
    echo "<td>{$user->created_at->format('Y-m-d H:i:s')}</td>";
    echo "</tr>";
}

echo "</table>";

// Check specific users mentioned in the screenshots
echo "<h3>Users with specific names (gak, lin):</h3>";
$specificUsers = User::whereIn('name', ['gak', 'lin'])->get();

foreach ($specificUsers as $user) {
    echo "<div style='border: 1px solid blue; padding: 15px; margin: 10px 0;'>";
    echo "<h4>{$user->name} (ID: {$user->id})</h4>";
    echo "<p><strong>Email:</strong> {$user->email}</p>";
    echo "<p><strong>Avatar:</strong> " . ($user->avatar ?? 'NULL') . "</p>";
    echo "<p><strong>Avatar URL:</strong> {$user->avatar_url}</p>";
    echo "<p><strong>Created:</strong> {$user->created_at}</p>";
    echo "<p><strong>Updated:</strong> {$user->updated_at}</p>";
    echo "<p><strong>Role:</strong> {$user->role}</p>";
    
    // Check if avatar file exists
    if ($user->avatar) {
        $filename = basename($user->avatar);
        $filePath = __DIR__ . '/storage/app/public/avatars/' . $filename;
        echo "<p><strong>Avatar File Exists:</strong> " . (file_exists($filePath) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p><strong>File Path:</strong> {$filePath}</p>";
    }
    echo "</div>";
}

echo "<h3>Authentication Configuration:</h3>";
echo "<p><strong>Auth Guard:</strong> " . config('auth.defaults.guard') . "</p>";
echo "<p><strong>Auth Provider:</strong> " . config('auth.defaults.provider') . "</p>";
echo "<p><strong>User Model:</strong> " . config('auth.providers.users.model') . "</p>";

?>
