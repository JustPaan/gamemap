<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMap - Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #0f0f1a;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            max-width: 400px;
            padding: 30px;
            border-radius: 10px;
            background-color: #1e1e2e;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-control {
            background-color: #2d2d3d;
            color: white;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        .form-control:focus {
            background-color: #2d2d3d;
            color: white;
            border-color: #7c3aed;
            box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
        }
        .btn-primary {
            background-color: #7c3aed;
            border-color: #7c3aed;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #6d28d9;
            border-color: #6d28d9;
        }
        .form-label {
            color: #e5e7eb;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .title {
            text-align: center;
            color: #7c3aed;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .text-link {
            color: #7c3aed;
            text-decoration: none;
        }
        .text-link:hover {
            color: #8b5cf6;
            text-decoration: underline;
        }
        .alert {
            background-color: #1f2937;
            border: 1px solid #374151;
            color: #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #065f46;
            border-color: #059669;
            color: #d1fae5;
        }
        .text-danger {
            color: #ef4444 !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="title">GameMap</h1>
        
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Log In</button>

            @if(session('error'))
            <p class="text-danger mt-3 text-center">{{ session('error') }}</p>
            @endif
        </form>
        
        <div class="text-center mt-3">
            <span class="text-muted">Don't have an account? </span>
            <a href="{{ route('register') }}" class="text-link">Sign up</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>