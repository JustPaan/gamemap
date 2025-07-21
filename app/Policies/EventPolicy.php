<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all events
        if ($user->role === 'admin') {
            return true;
        }

        // Organizers can view their own events
        if ($user->role === 'organizer') {
            return true;
        }

        // Gamers can only view approved events (handled in controller)
        return true;
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        // Admins can view any event
        if ($user->role === 'admin') {
            return true;
        }

        // Organizers can view their own events
        if ($user->role === 'organizer' && $user->id === $event->user_id) {
            return true;
        }

        // Public can view approved events
        if ($event->is_approved) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        // Only approved organizers can create events
        return $user->role === 'organizer' && $user->is_approved;
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        // Admins can update any event
        if ($user->role === 'admin') {
            return true;
        }

        // Organizers can only update their own events
        return $user->role === 'organizer' && 
               $user->id === $event->user_id &&
               !$event->is_past;
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        // Admins can delete any event
        if ($user->role === 'admin') {
            return true;
        }

        // Organizers can only delete their own future events
        return $user->role === 'organizer' && 
               $user->id === $event->user_id &&
               !$event->is_past &&
               $event->participant_count === 0;
    }

    /**
     * Determine whether the user can approve events.
     */
    public function approve(User $user): bool
    {
        // Only admins can approve events
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can register for events.
     */
    public function register(User $user, Event $event): bool
    {
        // Only gamers can register
        if ($user->role !== 'gamer') {
            return false;
        }

        // Can't register for past events
        if ($event->is_past) {
            return false;
        }

        // Can't register if event is full
        if ($event->is_full) {
            return false;
        }

        // Can't register if already registered
        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can cancel event registration.
     */
    public function cancelRegistration(User $user, Event $event): bool
    {
        // Only gamers can cancel registration
        if ($user->role !== 'gamer') {
            return false;
        }

        // Can't cancel for past events
        if ($event->is_past) {
            return false;
        }

        // Must be registered to cancel
        return $event->participants()->where('user_id', $user->id)->exists();
    }
}