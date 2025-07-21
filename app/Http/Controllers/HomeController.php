<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Game;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display the home page with new game releases.
     *
     * @return \Illuminate\View\View
     */
public function index()
{
    // Get the newest non-deleted games (11 most recent)
    $newReleases = Game::where('is_deleted', false)
                    ->orderBy('created_at', 'desc')
                    ->take(11)
                    ->get();

    // Get all upcoming or ongoing events
    $events = Event::with('game')
        ->where(function($query) {
            $query->where('start_date', '>', now()) // Upcoming events
                  ->orWhere(function($subQuery) {
                      $subQuery->where('start_date', '<=', now())
                                ->where('end_date', '>=', now()); // Ongoing events
                  });
        })
        ->get();

    // Check for missing coordinates
    foreach ($events as $event) {
        if (!$event->location_lat || !$event->location_lng) {
            Log::warning("Event ID {$event->id} has missing coordinates.");
        }
    }

    return view('home', [
        'newReleases' => $newReleases,
        'events' => $events
    ]);
}

    /**
     * Filter games by device type (AJAX endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterGames(Request $request)
    {
        $deviceType = $request->input('device_type');
        
        $games = Game::where('is_deleted', false)
                    ->when($deviceType && $deviceType !== 'all', function($query) use ($deviceType) {
                        return $query->where('device_type', ucfirst($deviceType));
                    })
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get();

        return response()->json([
            'html' => view('partials.game-cards', ['games' => $games])->render()
        ]);
    }

    /**
     * Search games by name (AJAX endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchGames(Request $request)
    {
        $searchTerm = $request->input('search_term');
        
        $games = Game::where('is_deleted', false)
                    ->where('name', 'like', '%'.$searchTerm.'%')
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get();

        return response()->json([
            'html' => view('partials.game-cards', ['games' => $games])->render()
        ]);
    }
}