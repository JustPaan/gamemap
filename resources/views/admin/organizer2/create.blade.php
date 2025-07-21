<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Organizer | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --danger: #ef4444;
            --success: #10b981;
            --text-dark: #1f2937;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
        }

        .admin-navbar {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            padding: 0.8rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .required-field::after {
            content: " *";
            color: var(--danger);
        }

        .alert {
            margin-bottom: 1rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Enhanced Navigation Bar -->
<nav class="admin-navbar">
    <div class="nav-container">
        <a href="{{ route('admin.dashboard') }}" class="nav-brand text-white">
            <i class="fas fa-shield-alt"></i> GameMap Admin
        </a>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white">Dashboard</a>
            <a href="{{ route('admin.game2') }}" class="nav-link text-white active">Games</a>
            <a href="{{ route('admin.user2') }}" class="nav-link text-white">Users</a>
            <a href="{{ route('admin.organizer2') }}" class="nav-link text-white">Organizers</a>
            <a href="{{ route('admin.event2') }}" class="nav-link text-white">Events</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-container">
    <div class="form-card">
        <h1 class="form-title">
            <i class="fas fa-plus-circle"></i>
            Add New Organizer
        </h1>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.organizers.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="form-label required-field">Name</label>
                <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}" placeholder="Enter organizer's name">
            </div>

            <div class="mb-4">
                <label for="email" class="form-label required-field">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="{{ old('email') }}" placeholder="Enter organizer's email">
            </div>

            <div class="mb-4">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter organizer's phone number">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label required-field">Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label required-field">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm password">
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.organizer2') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Add Organizer
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>