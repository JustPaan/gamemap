<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --gray-light: #1c1c22;
            --text-light: #ffffff;
            --pc-color: #4f46e5;  /* Purple for PC */
            --console-color: #10b981;  /* Emerald for Console */
            --mobile-color: #f59e0b;  /* Amber for Mobile */
        }

        body {
            background-color: var(--gray-light);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-light);
            min-height: 100vh;
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

        .game-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 20px;
            background: #3a3a3f;
            height: 100%;
        }

        .game-card:hover {
            transform: translateY(-5px);
        }

        .game-card-header {
            background-color: var(--primary);
            color: var(--text-light);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .game-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: calc(100% - 53px);
        }

        .game-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .game-description {
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .game-description-label {
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 5px;
        }

        /* Platform Badge Styles */
        .platform-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .platform-pc {
            background-color: rgba(79, 70, 229, 0.2);
            color: var(--pc-color);
            border: 1px solid var(--pc-color);
        }

        .platform-console {
            background-color: rgba(16, 185, 129, 0.2);
            color: var(--console-color);
            border: 1px solid var(--console-color);
        }

        .platform-mobile {
            background-color: rgba(245, 158, 11, 0.2);
            color: var(--mobile-color);
            border: 1px solid var(--mobile-color);
        }

        .platform-icon {
            font-size: 0.8rem;
        }

        .game-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .events-count {
            font-size: 0.85rem;
            color: #b3b3b3;
        }

        .pagination .page-link {
            background-color: #3a3a3f;
            border-color: #4a4a4f;
            color: var(--primary);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        @media (max-width: 768px) {
            .nav-menu {
                flex-wrap: wrap;
            }
            
            .game-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header d-flex justify-content-between align-items-center">
        <h1 class="h5"><i class="fas fa-gamepad"></i> Games</h1>
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
        <a class="nav-link" href="{{ url('/profile/info') }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a class="nav-link" href="{{ url('events') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
        <a class="nav-link active" href="{{ url('games') }}">
            <i class="fas fa-gamepad"></i> Games
        </a>
        <a class="nav-link" href="{{ url('home') }}">
            <i class="fas fa-home"></i> Home
        </a>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0"><i class="fas fa-gamepad"></i> Available Games</h2>
            <div class="d-flex">
                <form action="{{ route('games.search') }}" method="GET" class="d-flex">
                    <input type="text" name="query" class="form-control form-control-sm me-2" placeholder="Search games..." required>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Platform Filter Legend -->
        <div class="mb-4 d-flex gap-3">
            <div class="d-flex align-items-center">
                <span class="platform-badge platform-pc me-2">
                    <i class="fas fa-desktop platform-icon"></i> PC
                </span>
                <small>Computer Games</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="platform-badge platform-console me-2">
                    <i class="fas fa-gamepad platform-icon"></i> Console
                </span>
                <small>Console Games</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="platform-badge platform-mobile me-2">
                    <i class="fas fa-mobile-alt platform-icon"></i> Mobile
                </span>
                <small>Mobile Games</small>
            </div>
        </div>

        <!-- Games List -->
        <div class="row">
            @forelse($games as $game)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="game-card h-100">
                        <div class="game-card-header">
                            <h3 class="h6 mb-0">{{ $game->name }}</h3>
                            @if($game->device_type === 'PC')
                                <span class="platform-badge platform-pc">
                                    <i class="fas fa-desktop platform-icon"></i> PC
                                </span>
                            @elseif($game->device_type === 'Console')
                                <span class="platform-badge platform-console">
                                    <i class="fas fa-gamepad platform-icon"></i> Console
                                </span>
                            @elseif($game->device_type === 'Mobile')
                                <span class="platform-badge platform-mobile">
                                    <i class="fas fa-mobile-alt platform-icon"></i> Mobile
                                </span>
                            @endif
                        </div>
                        <div class="game-card-body">
                            @if($game->image_path)
                                <img src="{{ asset('storage/' . $game->image_path) }}" alt="Game Image" class="game-image">
                            @else
                                <div class="game-image bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-gamepad fa-3x text-light"></i>
                                </div>
                            @endif
                            
                            <div class="game-description">
                                <div class="game-description-label">Description:</div>
                                <p>{{ $game->description }}</p>
                            </div>
                            
                            <div class="game-meta">
                                <span class="badge bg-secondary">
                                    {{ $game->game_type }}
                                </span>
                                <span class="events-count">
                                    <i class="fas fa-users me-1"></i> {{ $game->events_count ?? 0 }} events
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i> No games available at the moment.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($games->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $games->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>