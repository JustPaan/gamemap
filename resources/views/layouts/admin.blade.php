<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .dashboard-header {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .dashboard-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .section-title {
            color: var(--secondary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        
        .user-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .user-id {
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        .user-email {
            color: #666;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-yes {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-no {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-refund {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .scroll-prompt {
            text-align: center;
            margin: 30px 0;
            color: #666;
        }
        
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-add {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-cancel {
            background-color: var(--secondary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="dashboard-header">
            <h1># Dashboard</h1>
        </div>
        
        <!-- Main content section that child views will populate -->
        @yield('content')
        
        <!-- Common footer elements -->
        <div class="scroll-prompt">
            <p>SCROLL DOWN TO SEE MORE 💬️</p>
        </div>
        
        <div class="action-buttons">
            <button class="btn btn-add">[Add Organizer]</button>
            <button class="btn btn-cancel">[Cancel Changes]</button>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    @stack('scripts')
</body>
</html>