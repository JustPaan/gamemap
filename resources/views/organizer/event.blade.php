<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
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
            background-color: var(--gray-light);
            font-family: 'Segoe UI', sans-serif;
        }

        .header {
            background: var(--primary);
            padding: 15px;
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

        .event-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card-header {
            background-color: var(--primary);
            color: white;
            padding: 15px;
        }

        .event-card-body {
            padding: 20px;
        }
        
        .event-card-body img {
            width: 150px; /* Set a fixed width */
            height: 150px; /* Set a fixed height */
            object-fit: cover; /* Cover the area without distortion */
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .bg-upcoming {
            background-color: #f59e0b; /* Yellow for upcoming */
            color: white;
        }

        .bg-completed {
            background-color: #10b981; /* Green for completed */
            color: white;
        }

        .bg-ongoing {
            background-color: #3b82f6; /* Blue or any other color for ongoing */
            color: white;
        }

        @media (max-width: 768px) {
            .nav-menu {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body class="min-vh-100">

    <!-- Header -->
    <div class="header d-flex justify-content-between align-items-center">
        <h1 class="h5"><i class="fas fa-calendar-alt"></i> Event Management</h1>
        <div class="d-flex">
            <span class="badge bg-light text-dark">
                <i class="fas fa-user"></i> {{ Auth::user()->name }}
            </span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-menu">
        <a class="nav-link" href="{{ url('organizer/dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a class="nav-link" href="{{ url('organizer/analytics') }}">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a class="nav-link" href="{{ url('organizer/profile') }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a class="nav-link active" href="{{ url('organizer/event') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4"><i class="fas fa-calendar-alt"></i> Your Events</h2>
            <a href="{{ url('organizer/event/form') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Event
            </a>
        </div>

        <!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card success">
            <h5><i class="fas fa-users"></i> Total Events</h5>
            <h3>{{ $totalEvents }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <h5><i class="fas fa-calendar-check"></i> Upcoming</h5>
            <h3>{{ $upcomingEvents }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card primary">
            <h5><i class="fas fa-running"></i> Ongoing</h5>
            <h3>{{ $ongoingEvents }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card danger">
            <h5><i class="fas fa-check-circle"></i> Completed</h5>
            <h3>{{ $completedEvents }}</h3>
        </div>
    </div>
</div>

        <!-- Events List -->
        <div class="row">
            @foreach($events as $event)
                <div class="col-md-6 col-lg-4">
                    <div class="event-card">
                        <div class="event-card-header d-flex justify-content-between align-items-center">
                            <h3 class="h6 mb-0">{{ $event->title }}</h3>
                            @php $status = getEventStatus($event); @endphp
                            <span class="badge-status bg-{{ $status == 'upcoming' ? 'warning' : ($status == 'completed' ? 'success' : ($status == 'ongoing' ? 'primary' : 'secondary')) }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                        <div class="event-card-body">
                            <p><i class="fas fa-info-circle"></i> {{ Str::limit($event->description, 100) }}</p>
                            <p><i class="fas fa-calendar-day"></i> {{ $event->start_date->format('d/m/Y') }}</p>
                            <p><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('H:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i A') }}</p>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $event->location_name }}</p>

                            <!-- Image Display -->
                            @if($event->image_path)
                                <img src="{{ $event->image_url }}" alt="Event Image" class="img-fluid mb-3" style="max-height: 150px;">
                            @else
                                <p>No image available</p>
                            @endif

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('organizer.event.detail', $event->id) }}" class="btn btn-sm btn-outline-primary">
                                    View Event Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                @if($events->onFirstPage())
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $events->previousPageUrl() }}">Previous</a>
                    </li>
                @endif

                @foreach(range(1, $events->lastPage()) as $page)
                    <li class="page-item {{ $events->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $events->url($page) }}">{{ $page }}</a>
                    </li>
                @endforeach

                @if($events->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $events->nextPageUrl() }}">Next</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Next</a>
                    </li>
                @endif
            </ul>
        </nav>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@php
function getEventStatus($event) {
    $now = \Carbon\Carbon::now();
    if ($event->end_date && $now->gt($event->end_date)) {
        return 'completed'; // Past event
    } elseif ($event->start_date && $now->lt($event->start_date) && $now->lt($event->end_date)) {
        return 'upcoming'; // Upcoming event
    } else {
        return 'ongoing'; // Active event
    }
}
@endphp
</html>