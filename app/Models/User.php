<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Event;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'nickname', 'phone', 'birthday',
        'bio', 'location', 'games', 'platforms', 'avatar', 'background',
        'role', 'is_approved', 'is_banned', 'demoted_at', 'last_active_at',
        'requested_organizer', 'is_rejected', 'approved_at', 'rejected_at',
        'approved_by'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthday' => 'date',
        'is_approved' => 'boolean',
        'is_banned' => 'boolean',
        'is_rejected' => 'boolean',
        'requested_organizer' => 'boolean',
        'games' => 'array',
        'platforms' => 'array',
        'last_active_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    protected $appends = [
        'avatar_url', 'background_url', 'role_name', 'event_stats', 'is_pending_organizer'
    ];

    // =====================
    // ADMIN-SPECIFIC METHODS
    // =====================

    public function managedOrganizers()
    {
        return $this->hasMany(User::class, 'approved_by')->where('role', 'organizer');
    }

    public function getAdminStatsAttribute()
    {
        if (!$this->isAdmin()) return null;
        
        return [
            'total_users' => User::count(),
            'active_today' => User::whereDate('last_active_at', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'pending_approval' => User::where('requested_organizer', true)
                                    ->whereNull('is_approved')
                                    ->whereNull('is_rejected')
                                    ->count(),
            'total_organizers' => User::where('role', 'organizer')->count()
        ];
    }

    // =====================
    // ROLE METHODS
    // =====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function organizer()
    {
        return $this->hasOne(Organizer::class, 'user_id');
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function isApprovedOrganizer(): bool
    {
        return $this->isOrganizer() && $this->is_approved && !$this->is_rejected;
    }

    public function isGamer(): bool
    {
        return $this->role === 'gamer';
    }

    public function isDemoted(): bool
    {
        return $this->demoted_at !== null;
    }

    public function isPendingOrganizer(): bool
    {
        return $this->requested_organizer && is_null($this->is_approved) && is_null($this->is_rejected);
    }

    public function isRejectedOrganizer(): bool
    {
        return $this->is_rejected === true;
    }

    // =====================
    // RELATIONSHIPS
    // =====================

public function organizerProfile()
{
    return $this->hasOne(Organizer::class);
}
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }
    
    public function activeEvents(): HasMany
    {
        return $this->organizedEvents()
                   ->where('start_date', '<=', now())
                   ->where('end_date', '>=', now());
    }

    public function pastEvents(): HasMany
    {
        return $this->organizedEvents()
                   ->where('end_date', '<', now());
    }

    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'participants')
                    ->withTimestamps();
    }

    public function managedGames(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'organizer_game')
                   ->withTimestamps();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar 
            ? asset('storage/avatars/'.$this->avatar)
            : asset('images/default-avatar.png');
    }

    public function getBackgroundUrlAttribute(): string
    {
        return $this->background
            ? asset('storage/backgrounds/'.$this->background)
            : asset('images/default-background.jpg');
    }

    public function getRoleNameAttribute(): string
    {
        return match(true) {
            $this->isAdmin() => 'Administrator',
            $this->isApprovedOrganizer() => 'Approved Organizer',
            $this->isPendingOrganizer() => 'Pending Organizer',
            $this->isRejectedOrganizer() => 'Rejected Organizer',
            $this->isGamer() => 'Gamer',
            default => 'User'
        };
    }

    public function getEventStatsAttribute(): array
    {
        return [
            'total_organized' => $this->organizedEvents()->count(),
            'active_events' => $this->activeEvents()->count(),
            'past_events' => $this->pastEvents()->count(),
            'total_participants' => $this->registeredEvents()->count(),
            'total_revenue' => $this->payments()->whereIn('event_id', $this->organizedEvents()->pluck('id'))->sum('amount') ?? 0,
        ];
    }
    
    public function getEventStats()
    {
        return [
            'totalEvents' => $this->organizedEvents()->count(),
            'activeEvents' => $this->organizedEvents()
                ->where('start_time', '>', now())
                ->orWhere(function ($query) {
                    $query->where('start_time', '<=', now())
                        ->where('end_time', '>=', now());
                })->count(),
            'pastEvents' => $this->organizedEvents()
                ->where('end_time', '<', now())
                ->count(),
            'totalParticipants' => $this->registeredEvents()->withCount('participants')->sum('participants_count'),
        ];
    }

    public function getIsPendingOrganizerAttribute(): bool
    {
        return $this->isPendingOrganizer();
    }

    // =====================
    // SCOPES
    // =====================

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeOrganizers($query)
    {
        return $query->where('role', 'organizer');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('requested_organizer', true)
                    ->whereNull('is_approved')
                    ->whereNull('is_rejected');
    }

    public function scopeApprovedOrganizers($query)
    {
        return $query->where('role', 'organizer')
                    ->where('is_approved', true)
                    ->whereNull('demoted_at');
    }

    public function scopePendingOrganizers($query)
    {
        return $query->where('role', 'organizer')
                    ->where('is_approved', false);
    }

    public function scopeDemotedOrganizers($query)
    {
        return $query->where('role', 'organizer')
                    ->whereNotNull('demoted_at');
    }

    public function scopeGamers($query)
    {
        return $query->where('role', 'gamer');
    }

    public function scopeActive($query)
    {
        return $query->where('is_banned', false)
                    ->where(function($q) {
                        $q->whereNull('demoted_at')
                          ->orWhere('role', '!=', 'organizer');
                    });
    }

    // =====================
    // BUSINESS LOGIC METHODS
    // =====================

    public function canBeDemoted(): bool
    {
        return $this->isApprovedOrganizer() && 
               !$this->activeEvents()->exists() &&
               $this->demoted_at === null;
    }

    public function approveOrganizer(User $approver): void
    {
        if (!$approver->isAdmin()) {
            throw new \Exception('Only admins can approve organizers');
        }

        $this->update([
            'role' => 'organizer',
            'is_approved' => true,
            'is_rejected' => false,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'requested_organizer' => false,
            'demoted_at' => null
        ]);

        // Create organizer profile if doesn't exist
        $this->organizerProfile()->firstOrCreate([
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone
        ]);
    }

    public function rejectOrganizer(): void
    {
        $this->update([
            'is_approved' => false,
            'is_rejected' => true,
            'rejected_at' => now(),
            'requested_organizer' => false
        ]);
    }

    public function demoteOrganizer(): void
    {
        if (!$this->canBeDemoted()) {
            throw new \Exception('Organizer cannot be demoted at this time');
        }

        $this->update([
            'demoted_at' => now(),
            'is_approved' => false
        ]);
    }

    public function requestOrganizerStatus(): void
    {
        if ($this->role !== 'gamer') {
            throw new \Exception('Only gamers can request organizer status');
        }

        $this->update([
            'requested_organizer' => true,
            'is_approved' => null,
            'is_rejected' => null
        ]);
    }

    public function saveIfDirty()
    {
        return $this->isDirty() ? $this->save() : false;
    }

    public function latestEvent()
    {
        return $this->hasOne(Event::class, 'organizer_id') // Ensure this is correct
                    ->orderBy('start_time', 'desc');
    }

    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->isDirty('role')) {
                if ($user->role === 'organizer') {
                    // Create organizer profile if doesn't exist
                    if (!$user->organizerProfile) {
                        Organizer::create([
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone
                        ]);
                    }
                } else {
                    // Remove organizer profile if role changed from organizer
                    $user->organizerProfile()->delete();
                }
            }
        });
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}