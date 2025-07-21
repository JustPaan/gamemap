<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\OrganizerPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => OrganizerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Role-based access gates
        Gate::define('admin-access', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('organizer-access', function ($user) {
            return $user->role === 'organizer' && $user->is_approved && !$user->is_rejected;
        });

        Gate::define('gamer-access', function ($user) {
            return $user->role === 'gamer';
        });

        // Organizer management gates
        Gate::define('manage-organizers', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('approve-organizer', function ($user, $targetUser) {
            return $user->isAdmin() && 
                   $targetUser->requested_organizer &&
                   is_null($targetUser->is_approved) &&
                   is_null($targetUser->is_rejected);
        });

        Gate::define('reject-organizer', function ($user, $targetUser) {
            return $user->isAdmin() && 
                   $targetUser->requested_organizer &&
                   is_null($targetUser->is_approved) &&
                   is_null($targetUser->is_rejected);
        });

        Gate::define('revoke-organizer', function ($user, $targetUser) {
            return $user->isAdmin() && 
                   $targetUser->role === 'organizer' &&
                   $targetUser->is_approved;
        });

        // Additional organizer permissions
        Gate::define('view-organizer', function ($user, $targetUser) {
            return $user->isAdmin() || $user->id === $targetUser->id;
        });

        Gate::define('edit-organizer', function ($user, $targetUser) {
            return $user->isAdmin() || $user->id === $targetUser->id;
        });
    }
}