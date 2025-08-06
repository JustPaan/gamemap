<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Reset - GameMap Development</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .reset-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .danger-zone {
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            background-color: #fff5f5;
        }
        .warning-icon {
            font-size: 48px;
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="warning-icon">⚠️</div>
            
            <h1 class="text-center text-danger mb-4">Database Reset Tool</h1>
            <p class="text-center text-muted mb-4">Development Environment Only</p>

            @if(session('success'))
                <div class="alert alert-success">
                    <h5>✅ {{ session('success') }}</h5>
                    @if(session('output'))
                        <details class="mt-3">
                            <summary>View Output</summary>
                            <pre class="mt-2 p-2 bg-light rounded">{{ session('output') }}</pre>
                        </details>
                    @endif
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <h5>❌ {{ session('error') }}</h5>
                </div>
            @endif

            <div class="danger-zone">
                <h3 class="text-danger mb-3">🗑️ Danger Zone</h3>
                
                <div class="alert alert-warning">
                    <h5>⚠️ This action will permanently delete:</h5>
                    <ul class="mb-0">
                        <li><strong>All Users</strong> (including admin accounts)</li>
                        <li><strong>All Events</strong> and event registrations</li>
                        <li><strong>All Games</strong> in the database</li>
                        <li><strong>All Organizers</strong> and their data</li>
                        <li><strong>All Participants</strong> and registrations</li>
                        <li><strong>All Payment</strong> records</li>
                        <li><strong>All uploaded files</strong> (avatars, game images, event images)</li>
                    </ul>
                </div>

                <div class="alert alert-info">
                    <h5>✅ What will remain:</h5>
                    <ul class="mb-0">
                        <li>Database structure and tables</li>
                        <li>Laravel framework files</li>
                        <li>Configuration files</li>
                        <li>Application code</li>
                    </ul>
                </div>

                <form method="POST" action="/dev/reset-database" class="mt-4">
                    @csrf
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmReset" required>
                        <label class="form-check-label text-danger" for="confirmReset">
                            <strong>I understand this will permanently delete all data and cannot be undone</strong>
                        </label>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="confirmBackup" required>
                        <label class="form-check-label" for="confirmBackup">
                            I have backed up any important data that I want to keep
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Are you absolutely sure? This cannot be undone!')">
                            🗑️ RESET ALL DATABASE DATA
                        </button>
                        <a href="/login" class="btn btn-secondary">Cancel - Go to Login</a>
                    </div>
                </form>
            </div>

            <div class="mt-4">
                <h4>📝 What to do after reset:</h4>
                <ol>
                    <li>Create a new admin user account via registration</li>
                    <li>Add new games to the database</li>
                    <li>Create organizer accounts</li>
                    <li>Set up new events</li>
                    <li>Test all functionality with fresh data</li>
                </ol>
            </div>

            <div class="mt-4 p-3 bg-light rounded">
                <h5>🔧 Alternative Options:</h5>
                <p class="mb-2"><strong>Command Line:</strong></p>
                <code>php artisan db:reset-data</code>
                <p class="mb-2 mt-3"><strong>Force (no confirmation):</strong></p>
                <code>php artisan db:reset-data --force</code>
            </div>
        </div>
    </div>
</body>
</html>
