<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    .nav {
      display: flex;
      justify-content: center;
      background: var(--primary);
      padding: 10px 0;
      gap: 5px;
    }

    .nav a {
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      transition: background 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .nav a:hover, .nav a.active {
      background: var(--primary-dark);
    }

    .container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: var(--text-dark);
    }

    .search-container {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .search-container input {
      padding: 10px 15px;
      border: 1px solid var(--gray-medium);
      border-radius: 8px;
      flex: 1;
      font-size: 14px;
    }

    .search-container button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .search-container button:hover {
      background: var(--primary-dark);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      font-size: 14px;
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid var(--gray-medium);
      text-align: center;
    }

    th {
      background: var(--primary-light);
      font-weight: 600;
    }

    tr:nth-child(even) {
      background-color: rgba(219, 234, 254, 0.3);
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 25px;
    }

    .btn {
      padding: 10px 20px;
      font-size: 14px;
      border-radius: 8px;
      cursor: pointer;
      border: none;
      font-weight: 500;
      transition: all 0.3s;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
    }

    .btn-success {
      background: var(--success);
      color: white;
    }

    .btn-success:hover {
      opacity: 0.9;
    }

    .scroll-hint {
      text-align: center;
      margin: 15px 0;
      color: var(--gray-dark);
      font-size: 13px;
    }

    @media (max-width: 768px) {
      .nav {
        flex-wrap: wrap;
        padding: 10px;
      }
      
      .container {
        margin: 20px 10px;
        padding: 15px;
      }
      
      th, td {
        padding: 8px 10px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>

<div class="nav">
    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
    <a href="{{ route('admin.game2') }}"><i class="fas fa-gamepad"></i><span>Game</span></a>
    <a href="{{ route('admin.user2') }}"><i class="fas fa-users"></i><span>User</span></a>
    <a href="{{ route('admin.organizer2') }}"><i class="fas fa-user-tie"></i><span>Organizer</span></a>
    <a href="{{ route('admin.events') }}" class="active"><i class="fas fa-calendar-alt"></i><span>Event</span></a>
</div>

<div class="container">
    <h2><i class="fas fa-calendar-alt"></i> Event Management</h2>

    <form action="{{ route('admin.events') }}" method="GET" class="search-container">
        <input type="text" name="search" placeholder="Search events by title..." value="{{ request()->query('search') }}">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Venue</th>
                <th>Title</th>
                <th>Organizer ID</th>
                <th>Max Participants</th>
                <th>Registered Participants</th>
                <th>Event ID</th>
                <th>Total Fee (RM)</th>
                <th>Action</th> <!-- Added Action column -->
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->location_name }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->organizer_id }}</td>
                    <td>{{ $event->max_participants }}</td>
                    <td>{{ $event->participants_count }}</td>
                    <td>{{ $event->id }}</td>
                    <td>{{ number_format($event->total_fee, 2) }}</td>
                    <td>
                        <form action="{{ route('admin.event.report', $event->id) }}" method="GET">
                            <button type="submit" class="btn btn-success"><i class="fas fa-file-export"></i> Generate Report</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>