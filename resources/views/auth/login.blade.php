<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>GameMap - Log In</title>
    <meta name="description" content="Log in to your GameMap account">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            color: #333;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .title {
            text-align: center;
            color: #1c1e21;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
            line-height: 1.2;
        }
        .form-label {
            color: #606770;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 14px;
            display: block;
        }
        .form-control {
            background-color: #ffffff;
            color: #1c1e21;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 17px;
            line-height: 20px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            background-color: #ffffff;
            color: #1c1e21;
            border-color: #1877f2;
            box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.2);
            outline: none;
        }
        .form-control::placeholder {
            color: #8a8d91;
        }
        .btn-primary {
            background-color: #1877f2;
            border: none;
            padding: 14px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 17px;
            width: 100%;
            color: #ffffff;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 6px;
        }
        .btn-primary:hover {
            background-color: #166fe5;
        }
        .btn-primary:active {
            background-color: #1464cc;
        }
        .signup-text {
            text-align: center;
            margin-top: 28px;
            color: #606770;
            font-size: 14px;
        }
        .text-link {
            color: #1877f2;
            text-decoration: none;
            font-weight: 500;
        }
        .text-link:hover {
            text-decoration: underline;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background-color: #d1edff;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .text-danger {
            color: #e41e3f !important;
            text-align: center;
            margin-top: 16px;
            font-size: 14px;
        }
        .mb-3 {
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="title">Log in to GameMap</h1>
        
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ secure_url(route('login', [], false)) }}">
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
            <p class="text-danger">{{ session('error') }}</p>
            @endif
        </form>
        
        <div class="signup-text">
            <span>Don't have an account? </span>
            <a href="{{ route('register') }}" class="text-link">Sign up</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>