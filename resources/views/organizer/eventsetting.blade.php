<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Settings</title>
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

        .form-container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }

        .venue-preview {
            background-color: var(--gray-light);
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            position: relative;
        }

        .venue-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .venue-preview i {
            font-size: 3rem;
            color: var(--primary);
        }

        .upload-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .upload-btn input {
            display: none;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
            padding: 10px 20px;
            border-radius: 8px;
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
        <div class="d-flex align-items-center">
            <a href="{{ url('organizer/event') }}" class="text-white me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h5 mb-0"><i class="fas fa-cog"></i> Event Settings</h1>
        </div>
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
        <a class="nav-link" href="{{ url('organizer/analytic') }}">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a class="nav-link" href="{{ url('organizer/profile') }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
        <a class="nav-link active" href="{{ url('organizer/event') }}">
            <i class="fas fa-calendar-alt"></i> Events
        </a>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form action="{{ route('organizer.eventsetting.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="row">
                <!-- Left Column - Venue Image and Location -->
                <div class="col-md-6">
                    <div class="venue-preview">
                        <img id="imagePreview" src="#" alt="Event Image Preview">
                        <i class="fas fa-image" id="placeholderIcon"></i>
                        <label class="upload-btn">
                            <i class="fas fa-camera"></i>
                            <input type="file" id="eventImage" name="event_image" accept="image/*">
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="locationName" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Location Name
                        </label>
                        <input type="text" class="form-control" id="locationName" name="location_name" value="{{ old('location_name') }}" placeholder="Enter venue name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">
                                <i class="fas fa-longitude-alt me-2"></i>Longitude
                            </label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="Longitude" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">
                                <i class="fas fa-latitude-alt me-2"></i>Latitude
                            </label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="Latitude" required>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Event Details -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="eventName" class="form-label">
                            <i class="fas fa-calendar-check me-2"></i>Event Name
                        </label>
                        <input type="text" class="form-control" id="eventName" name="event_name" value="{{ old('event_name') }}" placeholder="Enter event name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">
                            <i class="fas fa-calendar-day me-2"></i>Event Date
                        </label>
                        <input type="date" class="form-control" id="eventDate" name="event_date" value="{{ old('event_date') }}" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="startTime" class="form-label">
                                <i class="fas fa-clock me-2"></i>Start Time
                            </label>
                            <input type="time" class="form-control" id="startTime" name="start_time" value="{{ old('start_time') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="endTime" class="form-label">
                                <i class="fas fa-clock me-2"></i>End Time
                            </label>
                            <input type="time" class="form-control" id="endTime" name="end_time" value="{{ old('end_time') }}" required>
                        </div>
                    </div>
                    
                    <!-- Game Selection Dropdown -->
                    <div class="mb-3">
                        <label for="game" class="form-label">
                            <i class="fas fa-gamepad me-2"></i>Game
                        </label>
                        <select class="form-select" id="game" name="game_id" required>
                            <option value="" selected disabled>Select a game</option>
                            @foreach($games as $game)
                                <option value="{{ $game->id }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Game Type Selection Dropdown -->
                    <div class="mb-3">
                        <label for="game_type" class="form-label">
                            <i class="fas fa-gamepad me-2"></i>Game Type
                        </label>
                        <select class="form-select" id="game_type" name="game_type" required>
                            <option value="" selected disabled>Select a game type</option>
                            <option value="FIGHTING">FIGHTING</option>
                            <option value="RPG">RPG</option>
                            <option value="FPS">FPS</option>
                            <option value="TBS">TBS</option>
                            <option value="SPORT">SPORT</option>
                            <option value="ARCADE">ARCADE</option>
                            <option value="RACING">RACING</option>
                            <option value="MMORPG">MMORPG</option>
                            <option value="TPS">TPS</option>
                            <option value="STRATEGY">STRATEGY</option>
                        </select>
                    </div>

                    <!-- Device Type Selection Dropdown -->
                    <div class="mb-3">
                        <label for="deviceType" class="form-label">
                            <i class="fas fa-gamepad me-2"></i>Device Type
                        </label>
                        <select class="form-select" id="deviceType" name="device_type" required>
                            <option value="" selected disabled>Select a device type</option>
                            @foreach($deviceTypes as $deviceType)
                                <option value="{{ $deviceType }}" {{ old('device_type') == $deviceType ? 'selected' : '' }}>
                                    {{ $deviceType }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="maxParticipants" class="form-label">
                            <i class="fas fa-users me-2"></i>Maximum Participants
                        </label>
                        <input type="number" class="form-control" id="maxParticipants" name="max_participants" value="{{ old('max_participants') }}" placeholder="Enter maximum number of participants" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-2"></i>Description
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Event description" required>{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="reset" class="btn btn-danger">
                    <i class="fas fa-times me-2"></i>CANCEL
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>SUBMIT
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('eventImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById('placeholderIcon').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>