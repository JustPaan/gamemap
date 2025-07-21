<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GameMap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #dbeafe;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
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

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--primary);
            padding: 15px 25px;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            background: white;
            padding: 10px;
            margin: 20px auto;
            max-width: 1200px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .nav-menu a {
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

        .nav-menu a:hover, .nav-menu a.active {
            background: var(--primary);
            color: white;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-card.primary {
            background-color: var(--primary-light);
            border-left: 4px solid var(--primary);
        }

        .stat-card.success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success);
        }

        .stat-card.warning {
            background-color: rgba(245, 158, 11, 0.1);
            border-left: 4px solid var(--warning);
        }

        .stat-card.danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--danger);
        }

        .approval-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .approved {
            background-color: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .pending {
            background-color: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .rejected {
            background-color: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .action-btn {
            padding: 5px 10px;
            font-size: 0.85rem;
            margin: 2px;
        }

        .pagination .page-item .page-link {
            font-size: 0.75rem; /* Smaller font size */
            padding: 0.2rem 0.5rem; /* Adjust padding */
        }

        .pagination .page-item .page-link i {
            font-size: 0.75rem; /* Smaller icon size */
        }

        .pagination .page-item {
            margin: 0 2px; /* Adjust spacing between pagination items */
        }

        .logout-btn {
            background: transparent;
            border: 1px solid white;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            margin-left: 15px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: white;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .nav-menu {
                flex-wrap: wrap;
            }
            
            .dashboard-container {
                padding: 15px;
                margin: 20px 10px;
            }
            
            .stat-card {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><i class="fas fa-tachometer-alt"></i> Admin Dashboard</div>
        <div>
            <span class="badge bg-light text-dark">
                <i class="fas fa-user-shield"></i> {{ Auth::user()->name }}
            </span>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="nav-menu">
        <a href="{{ route('admin.dashboard') }}" class="active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.game2') }}">
            <i class="fas fa-gamepad"></i> Games
        </a>
        <a href="{{ route('admin.user2') }}">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="{{ route('admin.organizer2') }}">
            <i class="fas fa-user-tie"></i> Organizers
        </a>
        <a href="{{ route('admin.events') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </div>

    <div class="dashboard-container">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <h5><i class="fas fa-users"></i> Total Users</h5>
                    <h3>{{ $stats['totalUsers'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h5><i class="fas fa-user-tie"></i> Organizers</h5>
                    <h3>{{ $stats['totalOrganizers'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h5><i class="fas fa-clock"></i> Pending Approvals</h5>
                    <h3>{{ $stats['pendingApprovals'] }}</h3>
                </div>
            </div>
            <!-- Removed Active Events Card -->
        </div>

        <!-- Pending Approvals Section -->
        <div class="mt-5">
            <h3><i class="fas fa-clock"></i> Pending Organizer Approvals</h3>
            @if($pendingOrganizers->isEmpty())
                <div class="alert alert-info">No pending organizer approvals.</div>
            @else
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Registered</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingOrganizers as $organizer)
                        <tr>
                            <td>ORG{{ str_pad($organizer->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $organizer->name }}</td>
                            <td>{{ $organizer->email }}</td>
                            <td>{{ $organizer->created_at->format('M d, Y') }}</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                                <form method="POST" action="{{ route('admin.organizers.approve', $organizer->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.organizers.reject', $organizer->id) }}" class="d-inline">
                                    @csrf
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Rejection reason" required>
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $pendingOrganizers->links() }} <!-- Pagination links -->
            @endif
        </div>

        <!-- Recent Users Section -->
        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="fas fa-users"></i> Recent Users</h3>
                <a href="{{ route('admin.user2') }}" class="btn btn-sm btn-primary">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>USR{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                @if($user->is_banned)
                                    <span class="badge bg-danger">Banned</span>
                                @elseif($user->role === 'organizer')
                                    @if($user->is_approved === true)
                                        <span class="approval-status approved">Approved</span>
                                    @elseif($user->is_approved === false)
                                        <span class="approval-status rejected">Rejected</span>
                                    @else
                                        <span class="approval-status pending">Pending</span>
                                    @endif
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>