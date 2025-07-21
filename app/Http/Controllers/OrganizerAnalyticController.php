<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Event;
use App\Models\Game;
use App\Models\User;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrganizerAnalyticController extends Controller
{
    public function index()
    {
        return $this->analytics(); // Redirect to the analytics method
    }

    public function analytics()
    {
        $stats = [
            'totalEvents' => Event::count(),
            'totalParticipants' => Participant::count(),
            'upcomingEvents' => Event::where(DB::raw('CONCAT(start_date, " ", start_time)'), '>', now())->count(),
        ];

        $participantsOverTime = $this->getParticipantsOverTime();
        $deviceTypes = $this->getGameTypes(); // For device types
        $gameTypes = $this->getGameTypesDistribution(); // Update here for game types

        return view('organizer.analytics', compact('stats', 'participantsOverTime', 'deviceTypes', 'gameTypes'));
    }

    // Method to get participants over time
    private function getParticipantsOverTime()
    {
        return Participant::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }

    // Method to get device type distribution
    private function getGameTypes()
    {
        return Game::select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();
    }

    private function getGameTypesDistribution()
    {
        return Game::select('game_type', DB::raw('COUNT(*) as count'))
            ->groupBy('game_type')
            ->pluck('count', 'game_type')
            ->toArray();
    }

    public function getUsers()
    {
        $users = User::all(); // Fetch all users
        return view('users.index', compact('users')); // Pass to view
    }
    private function structureCalendar($events)
    {
        $calendar = [];

        foreach ($events as $event) {
            $eventDate = Carbon::parse($event->start_date);
            $day = $eventDate->day;

            if (!isset($calendar[$day])) {
                $calendar[$day] = [
                    'day' => $day,
                    'is_event' => true,
                    'event_name' => $event->title,
                ];
            }
        }

        return $calendar;
    }
}