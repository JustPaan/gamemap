<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GameMap Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="dashboard">
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <button>🔍</button>
        </div>

        <div class="filters">
            <button>Console</button>
            <button>PC</button>
            <button>Mobile</button>
        </div>

        <h3>Popular Games</h3>
        <div class="games">
            <div class="game">
                <img src="{{ asset('images/lol.png') }}" alt="LoL">
                <p>League of Legend</p>
            </div>
            <div class="game">
                <img src="{{ asset('images/cod.png') }}" alt="COD">
                <p>Call of Duty</p>
            </div>
            <div class="game">
                <img src="{{ asset('images/clash.png') }}" alt="Clash">
                <p>Clash of Clan</p>
            </div>
            <div class="game">
                <img src="{{ asset('images/valorant.png') }}" alt="Valorant">
                <p>Valorant</p>
            </div>
        </div>

        <div class="nav">
            <a href="#"><img src="{{ asset('images/home.png') }}"><span>home</span></a>
            <a href="#"><img src="{{ asset('images/gamepad.png') }}"><span>games</span></a>
            <a href="#"><img src="{{ asset('images/event.png') }}"><span>event</span></a>
            <a href="#"><img src="{{ asset('images/user.png') }}"><span>profile</span></a>
        </div>
    </div>
</body>
</html>
