<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Analytics</title>
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

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table th {
            background-color: var(--primary-light);
            font-weight: 600;
            color: var(--text-dark);
        }

        .data-table tr:hover {
            background-color: #f9fafb;
        }

        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .trend-up {
            color: var(--success);
        }

        .trend-down {
            color: var(--danger);
        }

        .trend-neutral {
            color: var(--warning);
        }

        @media (max-width: 768px) {
            .nav-menu {
                flex-wrap: wrap;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div><i class="fas fa-chart-line"></i> Organizer Analytics</div>
        <div>
            <span class="badge bg-light text-dark">
                <i class="fas fa-user"></i> {{ Auth::user()->name }}
            </span>
        </div>
    </div>

    <div class="nav-menu">
        <a href="{{ route('organizer.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('organizer.analytic') }}" class="active">
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
        <h2 class="mb-4"><i class="fas fa-chart-line"></i> Analytics Overview</h2>
        
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card bg-success text-white">
                    <h5><i class="fas fa-users"></i> Total Events</h5>
                    <h3>{{ $stats['totalEvents'] }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-primary text-white">
                    <h5><i class="fas fa-users"></i> Total Participants</h5>
                    <h3>{{ $stats['totalParticipants'] }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-warning text-white">
                    <h5><i class="fas fa-calendar-check"></i> Upcoming Events</h5>
                    <h3>{{ $stats['upcomingEvents'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Participants Over Time - Simplified Table Version -->
        <div class="stat-card mt-5">
            <h3><i class="fas fa-users"></i> Participants Over Time</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Participants</th>
                        <th>Trend</th> <!-- Kept the trend for quick visual reference -->
                    </tr>
                </thead>
                <tbody>
                    @php
                        $previousCount = null;
                    @endphp
                    
                    @foreach($participantsOverTime as $month => $count)
                        @php
                            $trendClass = 'trend-neutral';
                            $trendIcon = 'minus';
                            $trendLabel = 'Steady';
                            
                            if ($previousCount !== null) {
                                if ($count > $previousCount) {
                                    $trendClass = 'trend-up';
                                    $trendIcon = 'arrow-up';
                                    $trendLabel = 'Increasing';
                                } elseif ($count < $previousCount) {
                                    $trendClass = 'trend-down';
                                    $trendIcon = 'arrow-down';
                                    $trendLabel = 'Decreasing';
                                }
                            }
                            $previousCount = $count;
                        @endphp
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ $count }}</td>
                            <td>
                                <span class="trend-indicator {{ $trendClass }}">
                                    <i class="fas fa-{{ $trendIcon }}"></i>
                                    {{ $trendLabel }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Device Type Distribution -->
        <div class="stat-card mt-5">
            <h3><i class="fas fa-mobile-alt"></i> Device Type Distribution</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Device Type</th>
                        <th>Percentage</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = array_sum($deviceTypes);
                    @endphp
                    
                    @foreach($deviceTypes as $type => $count)
                        @php
                            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                        @endphp
                        <tr>
                            <td>{{ ucfirst($type) }}</td>
                            <td>{{ $percentage }}%</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Game Type Distribution -->
        <div class="stat-card mt-5">
            <h3><i class="fas fa-gamepad"></i> Game Type Distribution</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Game Type</th>
                        <th>Percentage</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalGames = array_sum($gameTypes);
                    @endphp
                    
                    @foreach($gameTypes as $gameType => $count)
                        @php
                            $percentage = $totalGames > 0 ? round(($count / $totalGames) * 100) : 0;
                        @endphp
                        <tr>
                            <td>{{ ucfirst($gameType) }}</td>
                            <td>{{ $percentage }}%</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>