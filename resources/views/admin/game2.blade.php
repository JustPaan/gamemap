<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Game Management</title>
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

    .admin-navbar {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      padding: 0.8rem 1.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: center; /* Changed to center the navigation */
      align-items: center;
    }

    .nav-links {
      display: flex;
      gap: 0.5rem;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.9);
      text-decoration: none;
      padding: 0.6rem 1rem;
      border-radius: 6px;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.95rem;
    }

    .nav-link:hover, .nav-link.active {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    .container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .game-image {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
    }

    .action-buttons a {
      margin-right: 5px;
    }

    .nav-tabs .nav-link.active {
      font-weight: 600;
      border-bottom: 3px solid var(--primary);
    }

    .trashed-row {
      background-color: rgba(239, 68, 68, 0.05);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .nav-container {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-links {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 0.5rem;
      }
      
      .container {
        margin: 20px 10px;
        padding: 15px;
      }
      
      .table-responsive {
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>

<nav class="admin-navbar">
  <div class="nav-container">
    <div class="nav-links">
      <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('admin.game2') }}" class="nav-link active">
        <i class="fas fa-gamepad"></i>
        <span>Games</span>
      </a>
      <a href="{{ route('admin.user2') }}" class="nav-link">
        <i class="fas fa-users"></i>
        <span>Users</span>
      </a>
      <a href="{{ route('admin.organizer2') }}" class="nav-link">
        <i class="fas fa-user-tie"></i>
        <span>Organizers</span>
      </a>
      <a href="{{ route('admin.events') }}" class="nav-link">
        <i class="fas fa-calendar-alt"></i>
        <span>Events</span>
      </a>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4"><i class="fas fa-gamepad"></i> Game Management</h2>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ !request()->has('trashed') ? 'active' : '' }}" 
               href="{{ route('admin.game2') }}">Active Games</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->has('trashed') ? 'active' : '' }}" 
               href="{{ route('admin.game2', ['trashed' => true]) }}">Trashed Games</a>
        </li>
    </ul>

    <div class="d-flex justify-content-between mb-4">
        <div class="search-container w-50">
            <form action="{{ route('admin.game2') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search games..." value="{{ request('search') }}">
                    <input type="hidden" name="trashed" value="{{ request()->has('trashed') ? 'true' : 'false' }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
        @if(!request()->has('trashed'))
        <a href="{{ route('admin.games.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Game
        </a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Game Name</th>
                    <th>Device Type</th>
                    <th>Game Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Active Events</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($games as $game)
                <tr class="{{ $game->trashed() ? 'trashed-row' : '' }}">
                    <td>
                        @if($game->image_path)
                            <img src="{{ $game->image_url }}" alt="{{ $game->name }}" class="game-image">
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </td>
                    <td>{{ $game->name }}</td>
                    <td>{{ $game->device_type }}</td>
                    <td>{{ $game->game_type }}</td>
                    <td>{{ Str::limit($game->description, 50) }}</td>
                    <td>
                        @if($game->trashed())
                            <span class="badge bg-danger">Trashed</span>
                        @else
                            <form action="{{ route('admin.games.toggle-delete-status', $game->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $game->is_deleted ? 'btn-danger' : 'btn-success' }}">
                                    {{ $game->is_deleted ? 'Inactive' : 'Active' }}
                                </button>
                            </form>
                        @endif
                    </td>
                    <td>{{ $game->active_events_count }}</td>
                    <td class="action-buttons">
                        @if($game->trashed())
                            <form action="{{ route('admin.games.restore', $game->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.games.force-delete', $game->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Permanently Delete" 
                                        onclick="return confirm('Permanently delete this game? This cannot be undone!')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.games.edit', $game->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.games.destroy', $game->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Move to Trash" 
                                        onclick="return confirm('Move this game to trash?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">
                        @if(request()->has('trashed'))
                            No trashed games found
                        @else
                            No games found
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $games->appends(['trashed' => request()->has('trashed')])->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
</body>
</html>