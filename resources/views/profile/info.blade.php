<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - GameMap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --gray-light: #1c1c22;
            --text-light: #ffffff;
            --bg-dark: #0f0f1a;
            --bg-profile: #1e1e2e;
            --text-secondary: #b3b3b3;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            background-color: var(--bg-profile);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            margin-bottom: 20px;
        }

        .profile-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .profile-info h3 {
            color: var(--primary);
        }

        .profile-info p {
            margin: 5px 0;
            color: var(--text-secondary);
        }

        .btn-custom {
            background-color: var(--primary);
            color: var(--text-light);
            border: none;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #3a6ae8; /* Darker shade for hover */
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            background: var(--bg-dark);
            padding: 10px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

        .nav-link {
            color: var(--primary);
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--primary);
            color: var(--text-light);
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2 class="text-center">User Profile</h2>
    
    <div class="text-center">
        <img src="{{ Auth::check() && Auth::user()->avatar ? asset('storage/avatar_images/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}" 
        alt="User Avatar" class="avatar">
    </div>

    <div class="profile-info">
        <h3>{{ Auth::user()->name }}</h3>
        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        <p><strong>Phone:</strong> {{ Auth::user()->phone ?? 'N/A' }}</p>
        <p><strong>Bio:</strong> {{ Auth::user()->bio ?? 'No bio available.' }}</p>
        
        <h5>Games:</h5>
        <p>
            @if(Auth::user()->games)
                {{ implode(', ', json_decode(Auth::user()->games)) }}
            @else
                No games listed.
            @endif
        </p>
        
        <h5>Platforms:</h5>
        <p>
            @if(Auth::user()->platforms)
                {{ implode(', ', explode(',', Auth::user()->platforms)) }}
            @else
                No platforms listed.
            @endif
        </p>
    </div>

    <div class="text-center">
        <a href="{{ route('profile.edit') }}" class="btn btn-custom">Edit Profile</a>
        <a href="{{ route('home') }}" class="btn btn-secondary">Back to Home</a>
    </div>
</div>

<!-- Navigation Menu -->
<div class="nav-menu">
    <a class="nav-link" href="{{ url('/profile/info') }}">
        <i class="fas fa-user-circle"></i> Profile
    </a>
    <a class="nav-link" href="{{ url('events') }}">
        <i class="fas fa-calendar-alt"></i> Events
    </a>
    <a class="nav-link" href="{{ url('games') }}">
        <i class="fas fa-gamepad"></i> Games
    </a>
    <a class="nav-link active" href="{{ url('home') }}">
        <i class="fas fa-home"></i> Home
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>