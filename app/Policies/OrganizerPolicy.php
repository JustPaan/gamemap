<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizerPolicy
{
    use HandlesAuthorization;

    public function approveOrganizer(User $admin, User $user)
    {
        return $admin->can('manage-organizers') && $user->requested_organizer;
    }

    public function rejectOrganizer(User $admin, User $user)
    {
        return $admin->can('manage-organizers') && $user->requested_organizer;
    }

    public function revokeOrganizer(User $admin, User $user)
    {
        return $admin->can('manage-organizers') && $user->role === 'organizer';
    }
}