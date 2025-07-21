<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --primary-light: #dbeafe;
      --danger: #ef4444;
      --success: #10b981;
      --gray-light: #f3f4f6;
      --gray-medium: #e5e7eb;
      --gray-dark: #6b7280;
      --text-dark: #1f2937;
    }
    
    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--gray-light);
      margin: 0;
      padding: 0;
      color: var(--text-dark);
    }

    .nav {
      display: flex;
      justify-content: center;
      background: var(--primary);
      padding: 10px 0;
      gap: 5px;
    }

    .nav a {
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      transition: background 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .nav a:hover, .nav a.active {
      background: var(--primary-dark);
    }

    .nav a i {
      font-size: 0.9em;
    }

    .container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: var(--text-dark);
    }

    .search-container {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .search-container input {
      padding: 10px 15px;
      border: 1px solid var(--gray-medium);
      border-radius: 8px;
      flex: 1;
      font-size: 14px;
    }

    .search-container button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .search-container button:hover {
      background: var(--primary-dark);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      font-size: 14px;
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid var(--gray-medium);
      text-align: center;
    }

    th {
      background: var(--primary-light);
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: rgba(219, 234, 254, 0.3);
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 25px;
    }

    .btn {
      padding: 10px 20px;
      font-size: 14px;
      border-radius: 8px;
      cursor: pointer;
      border: none;
      font-weight: 500;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
    }

    .btn-danger {
      background: var(--danger);
      color: white;
    }

    .btn-danger:hover {
      opacity: 0.9;
    }

    input[type="checkbox"] {
      transform: scale(1.3);
      accent-color: var(--primary);
    }

    .delete-btn {
      background: var(--danger);
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .delete-btn:hover {
      background: #dc2626;
    }

    .scroll-hint {
      text-align: center;
      margin: 15px 0;
      color: var(--gray-dark);
      font-size: 13px;
    }

    @media (max-width: 768px) {
      .nav {
        flex-wrap: wrap;
        padding: 10px;
      }
      
      .container {
        margin: 20px 10px;
        padding: 15px;
      }
      
      th, td {
        padding: 8px 10px;
        font-size: 13px;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 10px;
      }
      
      .btn {
        justify-content: center;
      }
    }

    /* Modal styles */
    .modal-content {
      border-radius: 12px;
      border: none;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .modal-header {
      background: var(--primary);
      color: white;
      border-radius: 12px 12px 0 0;
      padding: 15px 20px;
    }

    .modal-title {
      font-weight: 600;
    }

    .modal-body {
      padding: 25px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid var(--gray-medium);
      border-radius: 8px;
      font-size: 14px;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    .select-role {
      padding: 10px 15px;
      border-radius: 8px;
      border: 1px solid var(--gray-medium);
      width: 100%;
    }
  </style>
</head>
<body>

<div class="nav">
    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
    <a href="{{ route('admin.game2') }}"><i class="fas fa-gamepad"></i><span>Game</span></a>
    <a href="{{ route('admin.user2') }}" class="active"><i class="fas fa-users"></i><span>User</span></a>
    <a href="{{ route('admin.organizer2') }}"><i class="fas fa-user-tie"></i><span>Organizer</span></a>
    <a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i><span>Event</span></a>
</div>

<div class="container">
    <h2><i class="fas fa-users"></i> User Management</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search users..." value="{{ request('search') }}">
        <button type="button" id="searchBtn"><i class="fas fa-search"></i></button>
    </div>

    <form id="bulkForm" action="{{ route('admin.users.updateAll') }}" method="POST">
        @csrf
        @method('PUT')
        <table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr data-user-id="{{ $user->id }}">
            <td>
                <input type="text" name="users[{{ $user->id }}][name]" value="{{ $user->name }}" class="form-control">
            </td>
            <td>
                <input type="email" name="users[{{ $user->id }}][email]" value="{{ $user->email }}" class="form-control">
            </td>
            <td>
                <input type="text" name="users[{{ $user->id }}][phone]" value="{{ $user->phone ?? '' }}" class="form-control">
            </td>
            <td>
                <select name="users[{{ $user->id }}][role]" class="select-role">
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="organizer" {{ $user->role == 'organizer' ? 'selected' : '' }}>Organizer</option>
                    <option value="gamer" {{ $user->role == 'gamer' ? 'selected' : '' }}>Gamer</option>
                </select>
            </td>
            <td>
                <div class="action-buttons">
                    <button type="button" class="delete-btn" data-user-id="{{ $user->id }}">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <input type="hidden" name="users[{{ $user->id }}][id]" value="{{ $user->id }}">
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
        <div class="scroll-hint">
            <i class="fas fa-chevron-down"></i> SCROLL DOWN TO SEE MORE <i class="fas fa-chevron-down"></i>
        </div>
        <div class="action-buttons">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus"></i> ADD USER
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> SAVE CHANGES
            </button>
            <button type="reset" class="btn btn-danger">
                <i class="fas fa-times"></i> CANCEL CHANGES
            </button>
        </div>
    </form>
</div>
<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="select-role" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="organizer">Organizer</option>
                            <option value="gamer" selected>Gamer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="notify" name="notify" checked>
                        <label class="form-check-label" for="notify">Send welcome notification</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Search functionality
    $('#searchBtn').click(function() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        $('tbody tr').each(function() {
            const name = $(this).find('input[name*="[name]"]').val().toLowerCase();
            const email = $(this).find('input[name*="[email]"]').val().toLowerCase();
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Delete button handling
    $('.delete-btn').click(function() {
        const userId = $(this).data('user-id');
        $('#deleteForm').attr('action', `/admin/users/${userId}`);
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').click(function() {
        $('#deleteForm').submit();
    });

    // Reset form handling
    $('button[type="reset"]').click(function() {
        location.reload();
    });

    // Live search on typing
    $('#searchInput').on('input', function() {
        $('#searchBtn').click();
    });

    // Add form validation
    $('#addUserForm').submit(function(e) {
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            e.preventDefault();
        }
    });

    // Save Changes button functionality
    $('#bulkForm').on('submit', function(e) {
        e.preventDefault();
        
        // Show loading indicator
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> SAVING...');
        
        // Get form data
        const formData = $(this).serialize();
        
        // Send AJAX request
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT' // For PUT method through POST
            },
            success: function(response) {
                // Show success message
                if (response.success) {
                    // Create and show Bootstrap alert
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('.container').prepend(alertHtml);
                    
                    // Optionally refresh the page after 1.5 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error saving changes';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ': ' + xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage += ' (' + xhr.statusText + ')';
                }
                
                // Create and show Bootstrap alert
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('.container').prepend(alertHtml);
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Mark rows with changes
    $('#bulkForm').on('change', 'input, select', function() {
        $(this).closest('tr').addClass('table-warning');
    });
});
</script>
</body>
</html>