<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMap Home</title>
    <style>
        body {
            background-color: #1c1c22;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            margin-bottom: 60px;
        }
        .profile-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
            max-width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #4CAF50;
            margin-right: 15px;
        }
        .profile-info {
            text-align: left;
            color: #ffffff;
            flex-grow: 1;
        }
        .profile-info h3 {
            margin: 0;
            color: #4CAF50;
        }
        .profile-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #b3b3b3;
        }
        .logout-button {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .logout-button:hover {
            background-color: #c82333;
        }
        .search-bar {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .search-bar input {
            padding: 12px 20px;
            width: 70%;
            max-width: 500px;
            border-radius: 25px 0 0 25px;
            border: none;
            font-size: 16px;
            outline: none;
        }
        .search-bar button {
            padding: 12px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #45a049;
        }
        .filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        .filters button {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            background-color: #333;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        .filters button:hover, .filters button.active {
            background-color: #4CAF50;
            transform: scale(1.05);
        }
        .game-section {
            margin: 30px 0;
        }
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .game {
            transition: transform 0.3s;
            cursor: pointer;
        }
        .game:hover {
            transform: scale(1.1);
        }
        .game img {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid #4CAF50;
        }
        .game div {
            margin-top: 8px;
            font-size: 14px;
        }
        .nav-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(28, 28, 34, 0.9);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            border-top: 2px solid #4CAF50;
            z-index: 100;
            border-radius: 10px 10px 0 0;
        }
        .nav-bar a {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s;
            padding: 5px;
            border-radius: 5px;
        }
        .nav-bar a:hover, .nav-bar a.active {
            background-color: #4CAF50;
            color: #ffffff;
            transform: scale(1.1);
        }
        h3 {
            font-size: 24px;
            margin: 30px 0 20px;
            color: #4CAF50;
        }
        #map-container {
            margin: 30px 0;
            padding: 0 20px;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 15px;
            border: 2px solid #4CAF50;
        }
        .event-info-window {
            padding: 10px;
            max-width: 250px;
            background-color: #2d2d36;
            color: white;
        }
        .event-info-window img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #4CAF50;
        }
        .event-info-window h5 {
            margin: 0 0 5px 0;
            color: #4CAF50;
        }
        .event-info-window p {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #b3b3b3;
        }
        .event-info-window a {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 8px;
            font-size: 14px;
        }
    </style>
    <script 
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBoWvvejR7S5GT8Bh7mfaygm0HKp5Q_jlU&libraries=places">
</script>
</head>
<body>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- User Profile Section -->
    <div class="profile-card">
        <img src="{{ Auth::check() && Auth::user()->avatar ? Auth::user()->avatar_url . '&t=' . time() : asset('images/default-avatar.png') }}" 
             alt="User Avatar" class="avatar"
             onerror="this.onerror=null; this.src='/images/default-avatar.png';"
             onload="console.log('Avatar loaded successfully:', this.src);">
        <div class="profile-info">
            <h3>{{ Auth::user()->name }}</h3>
            <p>Email: {{ Auth::user()->email }}</p>
            <p>Phone: {{ Auth::user()->phone ?? 'N/A' }}</p>
            <p>Bio: {{ Auth::user()->bio ?? 'No bio available.' }}</p>
        </div>
        <button class="logout-button" onclick="document.getElementById('logout-form').submit()">Logout</button>
    </div>

    <div class="search-bar">
        <input type="text" placeholder="Search games..." id="searchInput">
        <button onclick="searchGames()">🔍 Search</button>
    </div>

    <div class="filters">
        <button class="active" onclick="filterGames('all')">All</button>
        <button onclick="filterGames('console')">Console</button>
        <button onclick="filterGames('pc')">PC</button>
        <button onclick="filterGames('mobile')">Mobile</button>
    </div>

    <!-- New Releases Section -->
    <div class="game-section">
        <h3>New Releases</h3>
        
        <div class="games-grid" id="newReleasesContainer">
            @foreach($newReleases as $game)
                <div class="game" data-category="{{ strtolower($game->device_type) }}">
                    @if($game->image_path)
                        <img src="{{ $game->image_url }}" 
                             alt="{{ $game->name }}">
                    @else
                        <img src="{{ asset('images/default-game.png') }}" 
                             alt="{{ $game->name }}">
                    @endif
                    <div>{{ $game->name }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Events Map Section -->
    <div class="game-section" id="map-container">
        <h3>Upcoming Events</h3>
        <div id="map"></div>
    </div>

    <div class="nav-bar">
        <a href="{{ url('/profile/info') }}">👤 Profile</a>
        <a href="/events">⭐ Events</a>
        <a href="/games">🎮 Games</a>
        <a href="#" class="active">🏠 Home</a>
    </div>

    <script>
        // Filter games by category
        function filterGames(category) {
            const games = document.querySelectorAll('.game');
            const buttons = document.querySelectorAll('.filters button');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            games.forEach(game => {
                if (category === 'all' || game.dataset.category === category) {
                    game.style.display = 'block';
                } else {
                    game.style.display = 'none';
                }
            });
        }

        // Search functionality
        function searchGames() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const games = document.querySelectorAll('.game');
            
            games.forEach(game => {
                const gameName = game.querySelector('div').textContent.toLowerCase();
                if (gameName.includes(searchTerm)) {
                    game.style.display = 'block';
                } else {
                    game.style.display = 'none';
                }
            });
        }

        // Make search work on Enter key
        document.getElementById('searchInput').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                searchGames();
            }
        });

        // Map functionality
        let map;
        let userMarker;
        let eventMarkers = [];
        const events = <?php echo json_encode($events); ?>;

        function initMap() {
            const defaultLocation = { lat: 3.1390, lng: 101.6869 };
            
            map = new google.maps.Map(document.getElementById("map"), {
                center: defaultLocation,
                zoom: 12,
                styles: [
                    {
                        "elementType": "geometry",
                        "stylers": [{"color": "#242424"}]
                    },
                    {
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#746855"}]
                    },
                    {
                        "elementType": "labels.text.stroke",
                        "stylers": [{"color": "#242424"}]
                    },
                    {
                        "featureType": "administrative.locality",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#d59563"}]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#d59563"}]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "geometry",
                        "stylers": [{"color": "#263c3f"}]
                    },
                    {
                        "featureType": "poi.park",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#6b9a76"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [{"color": "#38414e"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry.stroke",
                        "stylers": [{"color": "#212a37"}]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#9ca5b3"}]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry",
                        "stylers": [{"color": "#4CAF50"}]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [{"color": "#1f2835"}]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#f3d19c"}]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "geometry",
                        "stylers": [{"color": "#2f3948"}]
                    },
                    {
                        "featureType": "transit.station",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#d59563"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry",
                        "stylers": [{"color": "#17263c"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.fill",
                        "stylers": [{"color": "#515c6d"}]
                    },
                    {
                        "featureType": "water",
                        "elementType": "labels.text.stroke",
                        "stylers": [{"color": "#17263c"}]
                    }
                ]
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(userLocation);
                        userMarker = new google.maps.Marker({
                            position: userLocation,
                            map: map,
                            title: "Your Location",
                            icon: {
                                url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                            }
                        });
                        loadAllEvents();
                    },
                    error => {
                        console.error("Geolocation error:", error);
                        userLocation = defaultLocation;
                        map.setCenter(userLocation);
                        loadAllEvents();
                    },
                    {
                        enableHighAccuracy: true,
                        maximumAge: 30000,
                        timeout: 10000
                    }
                );
            } else {
                userLocation = defaultLocation;
                loadAllEvents();
            }
        }

        function loadAllEvents() {
            clearEventMarkers();
            events.forEach(event => {
                if (!event.location_lat || !event.location_lng) {
                    console.warn('Event missing coordinates:', event.id, event.title);
                    return;
                }
                const eventPos = { 
                    lat: parseFloat(event.location_lat), 
                    lng: parseFloat(event.location_lng) 
                };
                addEventMarker(event, eventPos);
            });
        }

        function addEventMarker(event, position) {
            if (isNaN(position.lat) || isNaN(position.lng)) {
                console.error('Invalid coordinates for event:', event.id, position);
                return;
            }

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: event.title,
                icon: {
                    url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png",
                    scaledSize: new google.maps.Size(32, 32)
                }
            });

            const infoContent = `
                <div class="event-info-window">
                    <img src="${event.image_url || '/images/default-event.jpg'}" 
                         alt="${event.title}" 
                         onerror="this.src='/images/default-event.jpg'">
                    <h5>${event.title}</h5>
                    <p><strong>Game:</strong> ${event.game?.name || 'No game specified'}</p>
                    <p><strong>When:</strong> ${new Date(event.start_date).toLocaleString()}</p>
                    <p><strong>Where:</strong> ${event.location_name || 'No location specified'}</p>
                    <a href="/events/${event.id}" class="btn btn-primary btn-sm mt-2">View Details</a>
                </div>
            `;
            
            const infowindow = new google.maps.InfoWindow({
                content: infoContent,
                maxWidth: 300
            });

            marker.addListener("click", () => {
                eventMarkers.forEach(m => {
                    if (m.infowindow) m.infowindow.close();
                });
                infowindow.open(map, marker);
                marker.infowindow = infowindow;
            });

            marker.eventData = event;
            eventMarkers.push(marker);
        }

        function clearEventMarkers() {
            eventMarkers.forEach(marker => {
                marker.setMap(null);
                if (marker.infowindow) {
                    marker.infowindow.close();
                }
            });
            eventMarkers = [];
        }

        window.addEventListener('load', initMap);
    </script>
</body>
</html>