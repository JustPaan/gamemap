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
    </style>
</head>
<body>
<div class="container mt-5 mb-5" data-lat="{{ $event->location_lat }}" data-lng="{{ $event->location_lng }}" data-title="{{ $event->location_name }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-4">{{ $event->title }}</h2>
        <a href="{{ url('/events') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Event Details
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $event->description }}</p>
            <p><strong>Date:</strong> {{ $event->start_date->format('d/m/Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('H:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i A') }}</p>
            <p><strong>Location:</strong> {{ $event->location_name }}</p>
            <p><strong>Max Participants:</strong> {{ $event->max_participants }}</p>
            
            @if($event->image_path)
                <img src="{{ $event->image_url }}" alt="Event Image" class="img-fluid mb-3" style="width: 300px; height: auto; object-fit: cover;">
            @endif
        </div>
    </div>

    <!-- Registration Section -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Registration
        </div>
        <div class="card-body">
            @auth
                @php
                    $isRegistered = $event->participants->contains(Auth::id());
                    $isFullyBooked = $event->participants_count >= $event->max_participants;
                @endphp

                @if($isRegistered)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        You're successfully registered for this event!
                        @if($event->total_fee > 0)
                            <div class="mt-2">
                                <small>Payment of RM{{ number_format($event->total_fee, 2) }} was processed</small>
                            </div>
                        @endif
                    </div>
                @elseif($isFullyBooked)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        This event is fully booked!
                    </div>
                @else
                    @if($event->total_fee > 0)
                        <form action="{{ route('events.checkout', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-credit-card me-2"></i> 
                                Pay RM{{ number_format($event->total_fee, 2) }} to Register
                            </button>
                            <div class="text-center mt-2">
                                <small class="text-muted">Secure payment powered by Stripe</small>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('events.register', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="fas fa-user-plus me-2"></i> 
                                Register for Free
                            </button>
                        </form>
                    @endif
                @endif
            @else
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i> Login to Register
                    </a>
                </div>
            @endauth
            
            <div class="mt-3 text-center">
                <small class="text-muted">
                    {{ $event->participants_count }} / {{ $event->max_participants }} participants registered
                </small>
            </div>
        </div>
    </div>

    <h3>Location on Map</h3>
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
        }
    </script>
</body>
</html>