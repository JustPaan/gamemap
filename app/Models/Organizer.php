<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $table = 'organizers'; 

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'events_completed',
    ];


    // Relationship to User model
public function user()
{
    return $this->belongsTo(User::class);
}

    // Relationship to Event model
    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id', 'user_id'); // Updated foreign key
    }

    public function activeEvents()
    {
        return $this->events()->where('end_date', '>', now());
    }
}