<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: 'Segoe UI', sans-serif;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #dee2e6;
        }
        .card {
            background-color: white;
            border: 1px solid rgba(0,0,0,.125);
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0,0,0,.125);
            font-weight: 600;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .progress {
            height: 8px;
        }
        .event-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 15px;
        }
        h2, h3 {
            color: #343a40;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container mt-4 mb-5" data-lat="{{ $event->location_lat }}" data-lng="{{ $event->location_lng }}" data-title="{{ $event->location_name }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $event->title }}</h2>
        <a href="{{ route('organizer.event') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-2"></i>Event Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Description:</strong><br>{{ $event->description }}</p>
                    <p><strong>Date:</strong><br>{{ $event->start_date->format('d/m/Y') }} to {{ $event->end_date->format('d/m/Y') }}</p>
                    <p><strong>Time:</strong><br>{{ \Carbon\Carbon::parse($event->start_time)->format('H:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i A') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Location:</strong><br>{{ $event->location_name }}</p>
                    <p><strong>Max Participants:</strong><br>{{ $event->max_participants }}</p>
                    <p><strong>Participation Fee:</strong><br>RM{{ number_format($event->total_fee, 2) }}</p>
                </div>
            </div>
            
            @if($event->image_path)
                <img src="{{ $event->image_url }}" alt="Event Image" class="event-image img-fluid">
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line me-2"></i>Participation Statistics
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Registered Participants:</strong><br>
                    {{ $event->participants_count }} / {{ $event->max_participants }}</p>
                    
                    <div class="progress mb-3">
                        @php
                            $percentage = $event->max_participants > 0 
                                ? ($event->participants_count / $event->max_participants) * 100 
                                : 0;
                        @endphp
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: <?php echo $percentage; ?>%" 
                                aria-valuenow="<?php echo $event->participants_count; ?>" 
                                aria-valuemin="0" 
                                aria-valuemax="<?php echo $event->max_participants; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Collected Fees:</strong><br>RM{{ number_format($totalCollectedFees, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4 mb-3"><i class="fas fa-map-marked-alt me-2"></i>Event Location</h3>
    <div id="map"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAEMprsBfBuikdHEQgss2K0bpau0dthecY&callback=initMap" async defer></script>
<script>
    function initMap() {
        const container = document.querySelector('.container');
        const lat = parseFloat(container.dataset.lat);
        const lng = parseFloat(container.dataset.lng);
        const title = container.dataset.title;

        const eventLocation = { lat: lat, lng: lng };

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: eventLocation,
        });

        const marker = new google.maps.Marker({
            position: eventLocation,
            map: map,
            title: title,
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div>
                    <h5 style="margin-bottom: 5px;">${title}</h5>
                    <p style="margin: 0;">{{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}</p>
                </div>
            `
        });
        
        marker.addListener("click", () => {
            infoWindow.open(map, marker);
        });
        
        // Open info window by default
        infoWindow.open(map, marker);
    }
</script>
</body>
</html>