<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Game | Admin Panel</title>
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
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8fafc;
      color: var(--text-dark);
      line-height: 1.6;
    }

    /* Enhanced Navigation */
    .admin-navbar {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary));
      padding: 0.8rem 1.5rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 1030;
    }

    .nav-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .nav-brand {
      color: white;
      font-weight: 600;
      font-size: 1.3rem;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .nav-brand i {
      font-size: 1.1em;
    }

    .nav-links {
      display: flex;
      gap: 0.25rem;
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
      font-weight: 500;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.15);
      color: white;
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.25);
      font-weight: 600;
    }

    .nav-link i {
      font-size: 0.9em;
    }

    /* Main Content */
    .main-container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    .form-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .form-header {
      border-bottom: 1px solid var(--gray-medium);
      padding-bottom: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .form-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--primary-dark);
      margin: 0;
    }

    .form-title i {
      margin-right: 0.75rem;
      color: var(--primary);
    }

    /* Form Elements */
    .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--text-dark);
    }

    .required-field::after {
      content: " *";
      color: var(--danger);
    }

    .image-upload-container {
      border: 2px dashed var(--gray-medium);
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      margin-bottom: 1.5rem;
    }

    .image-upload-container:hover {
      border-color: var(--primary);
      background-color: rgba(59, 130, 246, 0.05);
    }

    .image-preview {
      max-width: 100%;
      height: auto;
      max-height: 200px;
      border-radius: 8px;
      margin-bottom: 1rem;
      display: none;
    }

    .upload-icon {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .upload-text {
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    .upload-hint {
      color: var(--gray-dark);
      font-size: 0.9rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .admin-navbar {
        padding: 0.8rem 1rem;
      }
      
      .nav-container {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-links {
        width: 100%;
        overflow-x: auto;
        padding-bottom: 0.5rem;
      }
      
      .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
      }
      
      .main-container {
        padding: 0 1rem;
      }
      
      .form-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

<!-- Enhanced Navigation Bar -->
<nav class="admin-navbar">
  <div class="nav-container">
    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
      <i class="fas fa-shield-alt"></i>
      <span>GameMap Admin</span>
    </a>
    
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

<!-- Main Content -->
<div class="main-container">
  <div class="form-card">
    <div class="form-header">
      <h1 class="form-title">
        <i class="fas fa-plus-circle"></i>
        Add New Game
      </h1>
    </div>

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
        <strong>Error!</strong> Please fix the following issues:
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
          <!-- Game Name -->
          <div class="mb-4">
            <label for="name" class="form-label required-field">Game Name</label>
            <input type="text" class="form-control" id="name" name="name" required 
                   value="{{ old('name') }}" placeholder="Enter game name">
          </div>

          <!-- Device Type -->
          <div class="mb-4">
            <label for="device_type" class="form-label required-field">Device Type</label>
            <select class="form-select" id="device_type" name="device_type" required>
              <option value="" disabled selected>Select device type</option>
              <option value="PC" {{ old('device_type') == 'PC' ? 'selected' : '' }}>PC</option>
              <option value="Mobile" {{ old('device_type') == 'Mobile' ? 'selected' : '' }}>Mobile</option>
              <option value="Console" {{ old('device_type') == 'Console' ? 'selected' : '' }}>Console</option>
            </select>
          </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
          <!-- Game Type -->
          <div class="mb-4">
              <label for="game_type" class="form-label required-field">Game Type</label>
              <select class="form-select" id="game_type" name="game_type" required>
                  <option value="" disabled selected>Select game type</option>
                  <option value="FIGHTING" {{ old('game_type') == 'FIGHTING' ? 'selected' : '' }}>FIGHTING</option>
                  <option value="RPG" {{ old('game_type') == 'RPG' ? 'selected' : '' }}>RPG</option>
                  <option value="FPS" {{ old('game_type') == 'FPS' ? 'selected' : '' }}>FPS</option>
                  <option value="TBS" {{ old('game_type') == 'TBS' ? 'selected' : '' }}>TBS</option>
                  <option value="SPORT" {{ old('game_type') == 'SPORT' ? 'selected' : '' }}>SPORT</option>
                  <option value="ARCADE" {{ old('game_type') == 'ARCADE' ? 'selected' : '' }}>ARCADE</option>
                  <option value="RACING" {{ old('game_type') == 'RACING' ? 'selected' : '' }}>RACING</option>
                  <option value="MMORPG" {{ old('game_type') == 'MMORPG' ? 'selected' : '' }}>MMORPG</option>
                  <option value="TPS" {{ old('game_type') == 'TPS' ? 'selected' : '' }}>TPS</option>
                  <option value="STRATEGY" {{ old('game_type') == 'STRATEGY' ? 'selected' : '' }}>STRATEGY</option>
              </select>
          </div>

          <!-- Active Events Count -->
          <div class="mb-4">
              <label for="active_events_count" class="form-label">Active Events Count</label>
              <input type="number" class="form-control" id="active_events_count" name="active_events_count"
                    value="{{ old('active_events_count', 0) }}" min="0">
          </div>
      </div>
      </div>

      <!-- Image Upload -->
      <div class="mb-4">
        <label class="form-label required-field">Game Image</label>
        <div class="image-upload-container" onclick="document.getElementById('image').click()">
          <img id="imagePreview" class="image-preview" alt="Game preview">
          <div id="uploadArea">
            <i class="fas fa-cloud-upload-alt upload-icon"></i>
            <div class="upload-text">Click to upload game image</div>
            <div class="upload-hint">JPEG, PNG, or JPG (Max 2MB)</div>
          </div>
          <input type="file" id="image" name="image" accept="image/*" class="d-none" required>
        </div>
      </div>

      <!-- Description -->
      <div class="mb-4">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4"
                  placeholder="Enter game description">{{ old('description') }}</textarea>
      </div>

      <!-- Form Actions -->
      <div class="d-flex justify-content-between border-top pt-4">
        <a href="{{ route('admin.game2') }}" class="btn btn-outline-secondary px-4">
          <i class="fas fa-arrow-left me-2"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary px-4">
          <i class="fas fa-save me-2"></i> Save Game
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Image preview functionality
  document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
      const preview = document.getElementById('imagePreview');
      const uploadArea = document.getElementById('uploadArea');
      const reader = new FileReader();
      
      reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        uploadArea.style.display = 'none';
      }
      reader.readAsDataURL(file);
    }
  });

  // Auto-dismiss alerts after 5 seconds
  document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      setTimeout(function() {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      }, 5000);
    });
  });
</script>
</body>
</html>