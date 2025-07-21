<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Participant;
use Illuminate\Support\Facades\Log;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'max_participants',
        'total_fee',
        'location_name',
        'location_lat',
        'location_lng',
        'is_approved',
        'organizer_id',
        'game_id',
        'participants_count',
        'image_path',
        'device_type',
        'game_type',
    ];

    protected $casts = [
        'start_date' => 'date', 
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_approved' => 'boolean',
        'total_fee' => 'decimal:2',
    ];

    protected $appends = [
        'status',
        'is_full',
        'is_past',
        'is_ongoing',
        'participant_count',
        'formatted_total_fee',
        'location' // Add this to append the location array
    ];

    protected $primaryKey = 'id';

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    // Add this accessor to get location as an array
    public function getLocationAttribute(): array
    {
        return [
            'lat' => $this->location_lat,
            'lng' => $this->location_lng,
            'name' => $this->location_name
        ];
    }

    // Relationship with the organizer (User)
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

// In app/Models/Event.php
    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants',)
                    ->withPivot('registered_at', 'attended')
                    ->withTimestamps();
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    // Accessors
    public function getStatusAttribute(): string
    {
        if ($this->is_ongoing) {
            return 'ongoing';
        } elseif ($this->is_past) {
            return 'finished';
        }
        return 'upcoming';
    }
    
    public function getIsUpcomingAttribute(): bool
{
    return !$this->is_ongoing && !$this->is_past;
}

    public function getIsFullAttribute(): bool
    {
        return $this->max_participants && $this->participants()->count() >= $this->max_participants;
    }

    public function getIsPastAttribute(): bool
    {
        // Trim values to avoid trailing spaces or unexpected characters
        $endDate = trim($this->end_date);
        $endTime = trim($this->end_time);

        // Combine date and time
        $endDateTimeString = $endDate . ' ' . $endTime;

        // Create Carbon instance and log for debugging
        try {
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $endDateTimeString);
        } catch (\Exception $e) {
            Log::error('Error parsing end date: ' . $e->getMessage());
            return false; // Handle error appropriately
        }

        return Carbon::now()->greaterThan($endDateTime);
    }

public function getIsOngoingAttribute(): bool
{
    // Trim values to avoid trailing spaces or unexpected characters
    $startDate = trim($this->start_date);
    $startTime = trim($this->start_time);
    $endDate = trim($this->end_date);
    $endTime = trim($this->end_time);

    // Combine date and time
    $startDateTimeString = $startDate . ' ' . $startTime;
    $endDateTimeString = $endDate . ' ' . $endTime;

    // Create Carbon instances and log for debugging
    try {
        if (empty($startDateTimeString) || empty($endDateTimeString)) {
            Log::error('Start or end date/time is empty.');
            return false; // Handle error appropriately
        }
        
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $startDateTimeString);
        $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $endDateTimeString);

        return Carbon::now()->between($startDateTime, $endDateTime);
        
    } catch (\Exception $e) {
        Log::error('Error parsing date: ' . $e->getMessage());
        return false; // Handle error appropriately
    }
}

    public function getParticipantCountAttribute(): int
    {
        return $this->participants()->count();
    }

    public function getFormattedTotalFeeAttribute(): string
    {
        return 'RM ' . number_format($this->total_fee, 2);
    }

    // Method to get the pin point coordinates
    public function getPinPoint(): array
    {
        return [
            'lat' => $this->location_lat,
            'lng' => $this->location_lng,
            'name' => $this->location_name
        ];
    }

    // Query Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeUpcoming($query)
    {
        return $query->where(function($q) {
            $q->where('start_date', '>', now()->toDateString())
              ->orWhere(function($q2) {
                  $q2->whereDate('start_date', now()->toDateString())
                      ->whereTime('start_time', '>', now()->toTimeString());
              });
        });
    }

    public function scopeOngoing($query)
    {
        return $query->where(function($q) {
            $q->where(function($q2) {
                $q2->where('start_date', '<', now()->toDateString())
                   ->where('end_date', '>', now()->toDateString());
            })->orWhere(function($q3) {
                $q3->whereDate('start_date', now()->toDateString())
                   ->whereTime('start_time', '<=', now()->toTimeString())
                   ->whereTime('end_time', '>=', now()->toTimeString());
            });
        });
    }

    public function scopeFinished($query)
    {
        return $query->where(function($q) {
            $q->where('end_date', '<', now()->toDateString())
              ->orWhere(function($q2) {
                  $q2->whereDate('end_date', now()->toDateString())
                      ->whereTime('end_time', '<', now()->toTimeString());
              });
        });
    }

    public function scopeWithinRadius($query, float $latitude, float $longitude, float $radiusKm)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(
                POINT(location_lng, location_lat),
                POINT(?, ?)
            ) <= ?",
            [$longitude, $latitude, $radiusKm * 1000]
        );
    }

    public function scopeSearch($query, string $searchTerm)
    {
        return $query->where('location_name', 'like', "%{$searchTerm}%")
                     ->orWhere('title', 'like', "%{$searchTerm}%");
    }

    public function scopeActive($query)
    {
        return $query->where('is_approved', true)
                     ->where(function($subQuery) {
                         $subQuery->where('start_time', '>', now())
                                  ->orWhere(function($subQuery2) {
                                      $subQuery2->where('start_time', '<=', now())
                                                 ->where('end_time', '>=', now());
                                  });
                     });
    }

    // Method to register a participant
    public function registerParticipant(int $userId): void
    {
        if ($this->is_full) {
            throw new \Exception('Event is full. Cannot register.');
        }

        $this->participants()->create(['user_id' => $userId]);
        $this->increment('participants_count');
    }

    // Method to remove a participant
    public function removeParticipant(int $userId): void
    {
        $this->participants()->where('user_id', $userId)->delete();
        $this->decrement('participants_count');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}