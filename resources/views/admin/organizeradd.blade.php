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
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .required-field::after {
            content: " *";
            color: var(--danger);
        }

        .alert {
            margin-bottom: 1.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .password-strength.weak {
            color: var(--danger);
        }

        .password-strength.medium {
            color: orange;
        }

        .password-strength.strong {
            color: var(--success);
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 1rem;
            }
            
            .form-actions {
                flex-direction: column-reverse;
                gap: 0.75rem;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="admin-navbar navbar navbar-expand-lg">
    <div class="container-fluid">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand text-white">
            <i class="fas fa-shield-alt me-2"></i>GameMap Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon text-white"><i class="fas fa-bars"></i></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link text-white">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.game2') }}" class="nav-link text-white">Games</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.user2') }}" class="nav-link text-white">Users</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.organizer2') }}" class="nav-link text-white active">Organizers</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.events') }}" class="nav-link text-white">Events</a>
                </li>
            </ul>
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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.organizers.store') }}" method="POST" id="organizerForm">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="name" class="form-label required-field">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required 
                           value="{{ old('name') }}" placeholder="Enter organizer's full name">
                </div>

                <div class="col-md-6 mb-4">
                    <label for="email" class="form-label required-field">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           value="{{ old('email') }}" placeholder="Enter valid email address">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="{{ old('phone') }}" placeholder="+60 (123) 456-7890">
                </div>

            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="password" class="form-label required-field">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required 
                           placeholder="Minimum 8 characters" minlength="8">
                    <div id="passwordStrength" class="password-strength"></div>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="password_confirmation" class="form-label required-field">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" 
                           name="password_confirmation" required placeholder="Re-enter password">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.organizer2') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i> Create Organizer
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthText = document.getElementById('passwordStrength');
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        strengthText.textContent = ['Weak', 'Medium', 'Strong', 'Very Strong'][strength - 1] || '';
        strengthText.className = 'password-strength ' + 
            (strength < 2 ? 'weak' : strength < 4 ? 'medium' : 'strong');
    });

    // Form validation
    document.getElementById('organizerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
    });
</script>
</body>
</html>