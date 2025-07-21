<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Organizer Management</title>
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
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-danger {
      background: var(--danger);
      color: white;
    }

    input, select {
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid var(--gray-medium);
      background: white;
      font-size: 13px;
      width: 90%;
    }

    input[readonly] {
      background-color: var(--gray-light);
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
      .action-buttons {
        flex-direction: column;
        gap: 10px;
      }
      .btn {
        justify-content: center;
      }
      input, select {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="nav">
    <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
    <a href="{{ route('admin.game2') }}"><i class="fas fa-gamepad"></i><span>Game</span></a>
    <a href="{{ route('admin.user2') }}"><i class="fas fa-users"></i><span>User</span></a>
    <a href="{{ route('admin.organizer2') }}" class="active"><i class="fas fa-user-tie"></i><span>Organizer</span></a>
    <a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i><span>Event</span></a>
</div>

<div class="container">
    <h2><i class="fas fa-user-tie"></i> Organizer Management</h2>

    <div class="search-container">
        <input type="text" id="search" placeholder="Search organizers...">
        <button type="button" onclick="searchOrganizers()"><i class="fas fa-search"></i></button>
    </div>
    
    <table>
      <thead>
          <tr>
              <th>Name</th>
              <th>User ID</th>
              <th>Phone</th>
              <th>Events Completed</th>
              <th>Actions</th>
          </tr>
      </thead>
      <tbody id="organizer-table">
          @foreach($organizers as $organizer)
          <tr>
              <td><input type="text" name="name" value="{{ $organizer->name }}"></td>
              <td><input type="text" name="user_id" value="{{  $organizer->id }}" readonly></td>
              <td><input type="tel" name="phone" value="{{ $organizer->phone }}" placeholder="Enter phone"></td>
              <td><input type="number" name="events_completed" value="{{ $organizer->events_completed ?? 1 }}" readonly></td>
              <td>
                  <form action="{{ route('admin.organizers.destroy', $organizer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this organizer?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 13px;">
                          <i class="fas fa-trash-alt"></i> Delete
                      </button>
                  </form>
              </td>
          </tr>
          @endforeach
      </tbody>
    </table>

    <div class="action-buttons">
      <a href="{{ route('admin.organizers.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> ADD ORGANIZER
      </a>
      <button class="btn btn-danger" onclick="cancelChanges()"><i class="fas fa-times"></i> CANCEL CHANGES</button>
    </div>
</div>

<script>
    function searchOrganizers() {
        const searchTerm = document.getElementById('search').value.toLowerCase();
        const rows = document.querySelectorAll('#organizer-table tr');

        rows.forEach(row => {
            const name = row.querySelector('input[name="name"]').value.toLowerCase();
            if (name.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function cancelChanges() {
        window.location.reload();
    }
</script>
</body>
</html>