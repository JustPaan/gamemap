<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
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

        .registration-container {
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

        .search-filter {
            background: #3a3a3f;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
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
        <h1 class="h5"><i class="fas fa-calendar-alt"></i> Event Registration</h1>
        <div class="d-flex">
            @auth
                <span class="badge bg-light text-dark">
                    <i class="fas fa-user"></i> {{ Auth::user()->name }}
                </span>
            @else
                <span class="badge bg-light text-dark">
                    <i class="fas fa-user"></i> Guest
                </span>
            @endauth
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="nav-menu">
        <a class="nav-link" href="{{ url('profile') }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a class="nav-link" href="{{ url('events') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
        <a class="nav-link active" href="{{ url('eventregister') }}">
            <i class="fas fa-user-plus"></i> Register
        </a>
        <a class="nav-link" href="{{ url('games') }}">
            <i class="fas fa-gamepad"></i> Games
        </a>
        <a class="nav-link" href="{{ url('home') }}">
            <i class="fas fa-home"></i> Home
        </a>
    </div>

    <!-- Main Content -->
    <div class="registration-container">
        <h2 class="h4 mb-4"><i class="fas fa-calendar-plus"></i> Register for Events</h2>

        <!-- Search and Filter Section -->
        <div class="search-filter mb-4">
            <form action="{{ route('events.public.register') }}" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search Events</label>
                            <input type="text" class="form-control bg-dark text-light" id="search" name="search" placeholder="Search by event name...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control bg-dark text-light" id="date" name="date">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control bg-dark text-light" id="location" name="location" placeholder="Location...">
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
        <a href="{{ url('/events') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                <button type="reset" class="btn btn-outline-secondary"><i class="fas fa-undo"></i> Reset</button>
            </form>
        </div>

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
                    <p><i class="fas fa-users"></i> 
                        @if($event->max_participants)
                            {{ $event->participants->count() }}/{{ $event->max_participants }} registered
                        @else
                            Unlimited participants
                        @endif
                    </p>

                    @if($event->image_path)
                        <img src="{{ $event->image_url }}" alt="Event Image" class="img-fluid mb-3" style="width: 100%; height: 150px; object-fit: cover;">
                    @endif

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('events.public.show', $event->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-info-circle"></i> Details
                        </a>
                        
                        @auth
                            @if($event->participants->contains(Auth::id()))
                                <button class="btn btn-sm btn-success" disabled>
                                    <i class="fas fa-check"></i> Registered
                                </button>
                            @else
                                <form action="{{ route('events.register', $event->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-plus"></i> Register
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-sign-in-alt"></i> Login to Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="mt-4">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight today's date in the date picker
            const dateField = document.getElementById('date');
            if(dateField) {
                dateField.value = new Date().toISOString().split('T')[0];
            }
            
            // Add confirmation for registration
            const registerForms = document.querySelectorAll('form[action*="/register"]');
            registerForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if(!confirm('Are you sure you want to register for this event?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>