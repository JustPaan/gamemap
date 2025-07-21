<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    // Relationship to events
    public function events()
    {
        return $this->hasMany(Event::class, 'game_id'); // Adjust if there's a game_id in events
    }
}