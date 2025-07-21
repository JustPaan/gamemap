<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #1c1c22;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-top: 20px;
        }
        .event-image {
            max-width: 150px;
            width: auto; 
            height: auto; 
            border: 2px solid #ffffff;
            border-radius: 8px;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">{{ $event->title }}</h2>

    @if($event->image_path)
        <img src="{{ asset('storage/' . $event->image_path) }}" alt="Event Image" class="event-image">
    @else
        <p>No image available.</p>
    @endif

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Organizer</th>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $event->organizer->name ?? 'N/A' }}</td>
                <td>{{ $event->description }}</td>
                <td>{{ $event->start_date->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($event->start_time)->format('H:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i A') }}</td>
                <td>{{ $event->location_name }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th>Max Participants</th>
                <th>Registered Participants</th>
                <th>Total Fee (RM)</th>
                <th>Total Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $event->max_participants }}</td>
                <td>{{ $event->participants()->count() }}</td>
                <td>{{ number_format($event->total_fee, 2) }}</td>
                <td>{{ number_format($event->payments()->sum('amount'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Location on Map</h3>
    <div id="map" data-lat="{{ $event->location_lat }}" data-lng="{{ $event->location_lng }}" data-title="{{ $event->location_name }}"></div>
    
    <div class="mt-4">
        <a href="http://127.0.0.1:8000/admin/events" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Events
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAEMprsBfBuikdHEQgss2K0bpau0dthecY&callback=initMap" async defer></script>
<script>
    function initMap() {
        const container = document.getElementById('map');
        const lat = parseFloat(container.dataset.lat);
        const lng = parseFloat(container.dataset.lng);
        const title = container.dataset.title;

        const location = { lat: lat, lng: lng };

        const map = new google.maps.Map(container, {
            zoom: 15,
            center: location,
        });

        new google.maps.Marker({
            position: location,
            map: map,
            title: title,
        });
    }
</script>
</body>
</html>