<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Display games grouped by type for normal users
     */
public function publicIndex()
{
    $games = Game::where('is_deleted', false)
        ->whereNull('deleted_at')
        ->paginate(12); // 12 items per page

    return view('games', compact('games'));
}

    /**
     * Display details for a single game
     */
    public function show(Game $game)
    {
        // Ensure the game is not deleted
        if ($game->is_deleted || $game->deleted_at) {
            abort(404);
        }

        return view('games.show', [
            'game' => $game->load(['events' => function($query) {
                $query->where('start_date', '>=', now()->toDateString())
                    ->orderBy('start_date')
                    ->withCount('participants');
            }])
        ]);
    }

    /**
     * Get featured games (most active events)
     */
    protected function getFeaturedGames()
    {
        return Cache::remember('featured_games', 3600, function () {
            return Game::where('is_deleted', false)
                ->whereNull('deleted_at')
                ->withCount('events')
                ->orderByDesc('active_events_count')
                ->limit(5)
                ->get();
        });
    }

    /**
     * Search games by name or type
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'device_type' => 'nullable|in:PC,Mobile,Console'
        ]);

        $games = Game::where('is_deleted', false)
            ->whereNull('deleted_at')
            ->where(function($query) use ($validated) {
                $query->where('name', 'like', '%'.$validated['query'].'%')
                    ->orWhere('game_type', 'like', '%'.$validated['query'].'%');
            });

        if (!empty($validated['device_type'])) {
            $games->where('device_type', $validated['device_type']);
        }

        return view('games.search', [
            'games' => $games->paginate(12),
            'searchQuery' => $validated['query'],
            'deviceType' => $validated['device_type'] ?? null
        ]);
    }
}