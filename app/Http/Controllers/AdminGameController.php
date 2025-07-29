<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AdminGameController extends Controller
{

    protected $gameRules = [
        'name' => 'required|string|max:255',
        'device_type' => 'required|in:PC,Mobile,Console',
        'game_type' => 'required|in:FIGHTING,RPG,FPS,TBS,SPORT,ARCADE,RACING,MMORPG,TPS,STRATEGY',
        'description' => 'nullable|string',
        'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'is_deleted' => 'boolean',
        'active_events_count' => 'integer|min:0'
    ];

    /**
     * Display game management interface (game2.blade.php)
     */
    public function game2()
    {
        $games = Game::withTrashed()->latest()->paginate(10);
        return view('admin.game2', compact('games'));
    }

    /**
     * Display a listing of all games
     */
    public function index()
    {
        $games = Game::latest()->paginate(10);
        return view('admin.games.index', compact('games'));
    }

    /**
     * Show the form for creating a new game
     */
    public function create()
    {
        return view('admin.games.create');
    }

    /**
     * Store a newly created game
     */
    public function store(Request $request)
    {
        $rules = $this->gameRules;
        $rules['name'] .= '|unique:games';
        $rules['image'] = 'required|' . $rules['image'];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $imagePath = $this->storeImage($request->file('image'));

        Game::create([
            'name' => $request->name,
            'device_type' => $request->device_type,
            'game_type' => $request->game_type, 
            'description' => $request->description,
            'image_path' => $imagePath,
            'is_deleted' => $request->is_deleted ?? false,
            'active_events_count' => $request->active_events_count ?? 0
        ]);

        return redirect()->route('admin.game2')
            ->with('success', 'Game created successfully!');
    }

    /**
     * Show the form for editing a game
     */
    public function edit(Game $game)
    {
        return view('admin.games.edit', compact('game'));
    }

    /**
     * Update the specified game
     */
    public function update(Request $request, Game $game)
    {
        $rules = $this->gameRules;
        $rules['name'] .= '|unique:games,name,'.$game->id;

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'device_type' => $request->device_type,
            'game_type' => $request->game_type,
            'description' => $request->description,
            'is_deleted' => $request->is_deleted ?? false,
            'active_events_count' => $request->active_events_count ?? 0
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($game->image_path);
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        $game->update($data);

        return redirect()->route('admin.game2')
            ->with('success', 'Game updated successfully!');
    }

    /**
     * Remove the specified game (using soft delete)
     */
    public function destroy(Game $game)
    {
        try {
            // Check if there are any events associated with this game
            $eventCount = \App\Models\Event::where('game_id', $game->id)->count();
            
            if ($eventCount > 0) {
                return redirect()->route('admin.game2')
                    ->with('error', "Cannot delete game '{$game->name}' because it has {$eventCount} associated event(s). Please delete or reassign those events first.");
            }
            
            // Delete the associated image if exists
            $this->deleteImage($game->image_path);
            
            // Perform the soft delete
            $game->delete();
            
            return redirect()->route('admin.game2')
                ->with('success', 'Game moved to trash successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.game2')
                ->with('error', 'Error deleting game: ' . $e->getMessage());
        }
    }

    /**
     * Toggle delete status (alternative to soft delete)
     */
    public function toggleDeleteStatus($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->update(['is_deleted' => !$game->is_deleted]);
        
        return back()->with('success', 'Delete status updated!');
    }

    /**
     * Restore a soft deleted game
     */
    public function restore($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->restore();
        
        return back()->with('success', 'Game restored successfully!');
    }

    /**
     * Store game image and return path
     */
    protected function storeImage($image)
    {
        // Generate unique filename
        $filename = time() . '_' . $image->getClientOriginalName();
        
        // Ensure the storage directory exists
        $storageDir = storage_path('app/public/game_images');
        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        
        // Store to storage/app/public/game_images/ (Laravel standard)
        $storagePath = $image->storeAs('game_images', $filename, 'public');
        
        return $storagePath;
    }

    /**
     * Delete game image if exists
     */
    protected function deleteImage($path)
    {
        if ($path) {
            // Delete from public storage
            $publicPath = public_path('storage/' . $path);
            if (file_exists($publicPath)) {
                unlink($publicPath);
            }
            
            // Also delete from Laravel storage if exists
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Permanently delete a game
     */
    public function forceDelete($id)
    {
        try {
            $game = Game::withTrashed()->findOrFail($id);
            
            // Check if there are any events associated with this game
            $eventCount = \App\Models\Event::where('game_id', $game->id)->count();
            
            if ($eventCount > 0) {
                return redirect()->route('admin.game2')
                    ->with('error', "Cannot delete game '{$game->name}' because it has {$eventCount} associated event(s). Please delete or reassign those events first.");
            }
            
            // Delete the associated image if exists
            $this->deleteImage($game->image_path);
            
            // Perform permanent delete
            $game->forceDelete();
            
            return redirect()->route('admin.game2')
                ->with('success', 'Game permanently deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.game2')
                ->with('error', 'Error deleting game: ' . $e->getMessage());
        }
    }
}