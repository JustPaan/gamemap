<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-light: #dbeafe;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-light: #f3f4f6;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            background: white;
            padding: 10px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><i class="fas fa-tachometer-alt"></i> Organizer Dashboard</div>
        <div>
            <span class="badge bg-light text-dark">
                <i class="fas fa-user"></i> {{ Auth::user()->name }}
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
        <a href="{{ route('organizer.dashboard') }}" class="active">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('organizer.analytic') }}">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="{{ route('organizer.profile') }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a href="{{ route('organizer.event') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </div>

    <div class="dashboard-container">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card success">
                    <h5><i class="fas fa-users"></i> Total Participants This Month</h5>
                    <h3>{{ $stats['totalParticipants'] }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card warning">
                    <h5><i class="fas fa-calendar-check"></i> Total Events Organized</h5>
                    <h3>{{ $stats['totalEvents'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Section -->
        <div class="mt-5">
            <h3><i class="fas fa-calendar-plus"></i> Upcoming Events</h3>
            @if($upcomingEvents->isEmpty())
                <div class="alert alert-info">No upcoming events.</div>
            @else
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingEvents as $event)
                        <tr>
                            <td>EVT{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->start_date->format('M d, Y') }}</td>
                            <td>{{ $event->start_time->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Recent Participants Section -->
        <div class="mt-5">
            <h3><i class="fas fa-users"></i> Recent Participants</h3>
            @if($recentParticipants->isEmpty())
                <div class="alert alert-info">No recent participants.</div>
            @else
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentParticipants as $participant)
                        <tr>
                            <td>{{ $participant->user_id }}</td>
                            <td>{{ $participant->user->name ?? 'N/A' }}</td>
                            <td>{{ $participant->user->email ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>