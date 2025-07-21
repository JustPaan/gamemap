<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMap - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #0f0f1a;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .registration-container {
            max-width: 500px;
            margin: 50px auto;
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
            margin-bottom: 15px;
        }
        .form-control:focus {
            background-color: #2d2d3d;
            color: white;
            border-color: #6c63ff;
            box-shadow: 0 0 0 0.25rem rgba(108, 99, 255, 0.25);
        }
        .btn-primary {
            background-color: #6c63ff;
            border: none;
            padding: 10px;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #5a52d6;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #444;
            border: none;
            transition: all 0.3s;
        }
        .btn-secondary:hover {
            background-color: #333;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 10px;
            color: #6c63ff;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
            color: #6c63ff;
        }
        .password-container {
            position: relative;
        }
        .alert {
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
        .role-description {
            font-size: 0.9em;
            margin-top: 5px;
            color: #aaa;
            display: none;
        }
        .role-description.active {
            display: block;
            animation: fadeIn 0.3s ease-in-out;
        }
        a {
            color: #6c63ff;
            text-decoration: none;
            transition: color 0.3s;
        }
        a:hover {
            color: #8a84ff;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="logo">GameMap</div>
        
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            @if(session('is_organizer'))
                <br><small>Your organizer account is pending admin approval. You'll receive an email once approved.</small>
            @endif
        </div>
        <script>
            setTimeout(function() {
                window.location.href = "{{ route('login') }}";
            }, 3000); // Redirect after 3 seconds
        </script>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <strong>Registration Failed</strong>
            <ul class="mt-2 mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registrationForm" onsubmit="return validateForm()">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your full name" value="{{ old('name') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Nickname</label>
                <input type="text" name="nickname" class="form-control" placeholder="Choose a nickname" value="{{ old('nickname') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" value="{{ old('phone') }}" required>
            </div>
            
            <div class="mb-3 password-container">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Create password (min 8 chars)" required>
                <span class="password-toggle" onclick="togglePassword('password')">
                    <i class="far fa-eye"></i>
                </span>
            </div>
            
            <div class="mb-3 password-container">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm your password" required>
                <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                    <i class="far fa-eye"></i>
                </span>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Birthday</label>
                <input type="date" name="birthday" class="form-control" value="{{ old('birthday') }}" max="{{ date('Y-m-d', strtotime('-13 years')) }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Register As</label>
                <select name="role" id="role" class="form-control" required onchange="showRoleDescription(this.value)">
                    <option value="gamer" {{ old('role') == 'gamer' ? 'selected' : '' }}>Gamer</option>
                    <option value="organizer" {{ old('role') == 'organizer' ? 'selected' : '' }}>Event Organizer</option>
                </select>
                <div id="gamer-description" class="role-description {{ old('role') == 'gamer' ? 'active' : '' }}">
                    Join tournaments and connect with other gamers
                </div>
                <div id="organizer-description" class="role-description {{ old('role') == 'organizer' ? 'active' : '' }}">
                    Create and manage gaming events (requires admin approval)
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            Already have an account? <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i> Log in here
            </a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const icon = passwordField.nextElementSibling.querySelector('i');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        function showRoleDescription(role) {
            document.querySelectorAll('.role-description').forEach(el => {
                el.classList.remove('active');
            });
            document.getElementById(`${role}-description`).classList.add('active');
        }

        // Set max date for birthday (minimum 13 years old)
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());
            document.querySelector('input[name="birthday"]').max = maxDate.toISOString().split('T')[0];
            
            // Show appropriate role description on page load
            const selectedRole = document.getElementById('role').value;
            showRoleDescription(selectedRole);
        });

        // Validate form fields before submission
        function validateForm() {
            const fields = [
                'name',
                'nickname',
                'email',
                'phone',
                'password',
                'password_confirmation',
                'birthday',
                'role'
            ];
            for (let field of fields) {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input.value.trim()) {
                    alert(`Please fill in the ${input.placeholder.toLowerCase()}.`);
                    input.focus();
                    return false; // Prevent form submission
                }
            }
            return true; // All fields are filled
        }
    </script>
</body>
</html>