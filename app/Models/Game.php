<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    use SoftDeletes;

    protected $table = 'games'; // Ensure the table name is set

    protected $fillable = [
        'name',
        'device_type',
        'game_type',
        'is_deleted',
        'active_events_count',
        'description',
        'image_path'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * Get the full URL for the game image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            // Use Laravel asset helper with storage path
            return asset('storage/' . $this->image_path);
        }
        
        // Return default game image if no image is set
        return asset('images/default-game.png');
    }

    /**
     * Get the storage path for the image
     */
    public function getImageStoragePathAttribute()
    {
        if ($this->image_path) {
            return storage_path('app/public/' . $this->image_path);
        }
        return null;
    }

    // Relationship to events
    public function events()
    {
        return $this->hasMany(Event::class, 'game_id'); // Adjust if there's a game_id in events
    }
}