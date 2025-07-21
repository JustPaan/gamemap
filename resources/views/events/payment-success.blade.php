<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .success-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container text-center">
            <h1 class="text-success mb-4"><i class="fas fa-check-circle"></i> Payment Successful</h1>
            <p>You have successfully registered for:</p>
            <h3 class="mb-3">{{ $event->title }}</h3>
            
            <div class="card mb-4">
                <div class="card-body text-start">
                    <p><strong>Amount Paid:</strong> RM{{ number_format($event->total_fee, 2) }}</p>
                    <p><strong>Date:</strong> {{ $event->start_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}</p>
                    <p><strong>Location:</strong> {{ $event->location_name }}</p>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('events.public.show', $event->id) }}" class="btn btn-primary">
                    View Event Details
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    Return to Home
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>