<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAEMprsBfBuikdHEQgss2K0bpau0dthecY&libraries=places&callback=initMap" async defer></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }
        .pac-container {
            z-index: 1051 !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Create New Event</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('organizer.event.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Event Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label> 
                        <input type="text" class="form-control" id="title" name="title" required value="{{ old('title') }}"> 
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required value="{{ old('start_date') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required value="{{ old('end_date') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required value="{{ old('start_time') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required value="{{ old('end_time') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="max_participants" class="form-label">Max Participants <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" required min="1" value="{{ old('max_participants') }}">
                    </div>

                    <div class="mb-3">
                        <label for="total_fee" class="form-label">Participation Fee (RM)</label>
                        <input type="number" class="form-control" id="total_fee" name="total_fee" min="0" step="0.01" value="{{ old('total_fee', 0) }}">
                    </div>

                    <div class="mb-3">
                        <label for="event_image" class="form-label">Event Image</label>
                        <input type="file" class="form-control" id="event_image" name="event_image" accept="image/*">
                        <small class="text-muted">Recommended size: 1200x630 pixels</small>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Game Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="device_type" class="form-label">Device Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="device_type" name="device_type" required>
                            <option value="">Select device type</option>
                            <option value="PC" {{ old('device_type') == 'PC' ? 'selected' : '' }}>PC</option>
                            <option value="Mobile" {{ old('device_type') == 'Mobile' ? 'selected' : '' }}>Mobile</option>
                            <option value="Console" {{ old('device_type') == 'Console' ? 'selected' : '' }}>Console</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="game_id" class="form-label">Select a Game <span class="text-danger">*</span></label>
                        <select class="form-select" id="game_id" name="game_id" required onchange="updateGameName()">
                            <option value="">Select a game</option>
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}" data-name="{{ $game->name }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                                    {{ $game->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="game_name" class="mt-2 text-muted"></div>
                    </div>

                    <div class="mb-3">
                        <label for="game_type" class="form-label">Game Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="game_type" name="game_type" required>
                            <option value="">Select a game type</option>
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
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Event Location
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="location_name" class="form-label">Location Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location_name" name="location_name" required value="{{ old('location_name') }}">
                        <small class="text-muted">e.g., Cyberjaya Mall, Game Hub Center</small>
                    </div>

                    <div class="mb-3">
                        <label for="location_search" class="form-label">Search Location</label>
                        <input type="text" class="form-control" id="location_search" placeholder="Search for a location...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pin Location on Map <span class="text-danger">*</span></label>
                        <div id="map"></div>
                        <small class="text-muted">Click on the map or drag the marker to set the exact location</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location_lat" class="form-label">Latitude <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location_lat" name="location_lat" required readonly value="{{ old('location_lat') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location_lng" class="form-label">Longitude <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location_lng" name="location_lng" required readonly value="{{ old('location_lng') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary btn-lg">Create Event</button>
                <a href="{{ route('organizer.event') }}" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to update the game name display
        function updateGameName() {
            const gameSelect = document.getElementById('game_id');
            const selectedOption = gameSelect.options[gameSelect.selectedIndex];
            const gameName = selectedOption ? selectedOption.textContent : '';
            document.getElementById('game_name').textContent = gameName ? `Selected Game: ${gameName}` : '';
        }

        // Initialize the game name display on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateGameName();
            
            // Set up date validation
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            
            // Set minimum dates to today
            const today = new Date().toISOString().split('T')[0];
            startDate.min = today;
            endDate.min = today;
            
            startDate.addEventListener('change', function() {
                endDate.min = this.value;
                if (endDate.value && endDate.value < this.value) {
                    endDate.value = this.value;
                }
            });
            
            endDate.addEventListener('change', function() {
                if (this.value < startDate.value) {
                    alert('End date must be on or after start date');
                    this.value = startDate.value;
                }
            });
            
            // Set up time validation
            const startTime = document.getElementById('start_time');
            const endTime = document.getElementById('end_time');
            
            startTime.addEventListener('change', function() {
                if (startDate.value === endDate.value && endTime.value && endTime.value < this.value) {
                    alert('End time must be after start time when dates are the same');
                    endTime.value = '';
                }
            });
        });
    </script>
    
<script>
    let map;
    let marker;
    let geocoder;
    let autocomplete;

    function initMap() {
        // Default to Kuala Lumpur coordinates if no previous value
        const defaultLat = parseFloat("{{ old('location_lat', 3.1390) }}");
        const defaultLng = parseFloat("{{ old('location_lng', 101.6869) }}");
        const defaultLocation = { lat: defaultLat, lng: defaultLng };
        
        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultLocation,
            zoom: 15,
            mapTypeControl: true,
            streetViewControl: true
        });

        geocoder = new google.maps.Geocoder();
        
        // Initialize the marker
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true,
            title: "Drag me to set the exact location"
        });

        // Initialize the autocomplete for location search
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('location_search'),
            {
                types: ['establishment', 'geocode'],
                fields: ['geometry', 'name']
            }
        );

        // When a place is selected from autocomplete, update the map
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                alert("No details available for input: '" + place.name + "'");
                return;
            }
            
            // Update map view
            map.setCenter(place.geometry.location);
            map.setZoom(17);
            
            // Update marker position
            marker.setPosition(place.geometry.location);
            
            // Update form fields
            updateLocationFields(place.geometry.location, place.name);
        });

        // Add click listener on the map
        map.addListener('click', (event) => {
            marker.setPosition(event.latLng);
            updateLocationFields(event.latLng);
            geocodeLatLng(event.latLng);
        });

        // Add dragend listener on the marker
        marker.addListener('dragend', (event) => {
            updateLocationFields(event.latLng);
            geocodeLatLng(event.latLng);
        });

        // If there are old values, ensure the marker is at the correct position
        if ("{{ old('location_lat') }}" && "{{ old('location_lng') }}") {
            const oldLocation = { 
                lat: parseFloat("{{ old('location_lat') }}"), 
                lng: parseFloat("{{ old('location_lng') }}") 
            };
            marker.setPosition(oldLocation);
            map.setCenter(oldLocation);
            map.setZoom(17);
        }

        // Initial update of fields if there are old values
        if ("{{ old('location_lat') }}" && "{{ old('location_lng') }}") {
            document.getElementById('location_lat').value = "{{ old('location_lat') }}";
            document.getElementById('location_lng').value = "{{ old('location_lng') }}";
        }
    }

    function updateLocationFields(location, name = null) {
        console.log('Updating location to:', location.lat(), location.lng());
        document.getElementById('location_lat').value = location.lat();
        document.getElementById('location_lng').value = location.lng();
        
        if (name) {
            document.getElementById('location_name').value = name;
        }
    }

    function geocodeLatLng(latLng) {
        geocoder.geocode({ location: latLng }, (results, status) => {
            if (status === "OK") {
                if (results[0]) {
                    document.getElementById('location_name').value = results[0].formatted_address;
                }
            }
        });
    }
</script>
</body>
</html>