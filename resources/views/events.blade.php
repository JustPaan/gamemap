<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --gray-light: #1c1c22;
            --text-light: #ffffff;
        }

        body {
            background-color: var(--gray-light);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-light);
        }

        .header {
            background: var(--primary);
            padding: 15px;
            color: var(--text-light);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-menu {
            display: flex;
            justify-content: center;
            background: var(--gray-light);
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

        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            background: #2c2c34;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .event-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
            background: #3a3a3f;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-card-header {
            background-color: var(--primary);
            color: var(--text-light);
            padding: 15px;
        }

        .event-card-body {
            padding: 20px;
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
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
        <h1 class="h5"><i class="fas fa-calendar-alt"></i> Events</h1>
        <div class="d-flex">
            <span class="badge bg-light text-dark">
                <i class="fas fa-user"></i> Guest
            </span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-menu">
        <a class="nav-link" href="{{ url('/profile/info') }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a class="nav-link active" href="{{ url('events') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
        <a class="nav-link" href="{{ url('games') }}">
            <i class="fas fa-gamepad"></i> Games
        </a>
        <a class="nav-link" href="{{ url('home') }}">
            <i class="fas fa-home"></i> Home
        </a>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <h2 class="h4"><i class="fas fa-calendar-alt"></i> Upcoming Events</h2>

        <!-- Events List -->
        <div class="row">
            @foreach($events as $event)
                <div class="col-md-6 col-lg-4">
                    <div class="event-card">
                        <div class="event-card-header d-flex justify-content-between align-items-center">
                            <h3 class="h6 mb-0">{{ $event->title }}</h3>
                            <span class="badge-status bg-{{ $event->status == 'upcoming' ? 'warning' : ($event->status == 'ongoing' ? 'success' : 'secondary') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                        <div class="event-card-body">
                            <p><i class="fas fa-info-circle"></i> {{ Str::limit($event->description, 100) }}</p>
                            <p><i class="fas fa-calendar-day"></i> {{ $event->start_date->format('d/m/Y') }}</p>
                            <p><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('H:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i A') }}</p>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $event->location_name }}</p>
                            <p><i class="fas fa-users"></i> Max Participants: {{ $event->max_participants }}</p>

                            @if($event->image_path)
                                <img src="{{ asset('storage/' . $event->image_path) }}" alt="Event Image" class="img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            @endif

                            <div class="mt-3">
                                <a href="{{ route('events.public.show', $event->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                
                                @if($event->total_fee > 0)
                                    <form action="{{ route('events.checkout', $event->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-credit-card me-1"></i> Register
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('events.register', $event->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-user-plus me-1"></i> Register
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>