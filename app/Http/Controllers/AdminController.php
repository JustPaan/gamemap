<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organizer;
use App\Notifications\OrganizerApprovedNotification;
use App\Notifications\OrganizerRejectedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-organizers');
    }

    /**
     * Display admin dashboard with statistics
     */
public function dashboard()
{
    // Get regular users (paginated)
    $users = User::with(['organizerProfile'])->latest()->paginate(10);

    // Get stats
    $stats = [
        'totalUsers' => User::count(),
        'totalOrganizers' => User::where('role', 'organizer')->count(),
        'pendingApprovals' => User::where('role', 'organizer')
                                  ->whereNull('is_approved')
                                  ->where('requested_organizer', true) // Ensure this condition is included
                                  ->count(),
    ];

    // Get pending organizers (paginated)
    $pendingOrganizers = User::with(['organizerProfile'])
                             ->where('role', 'organizer')
                             ->whereNull('is_approved')
                             ->where('requested_organizer', true)
                             ->latest()
                             ->paginate(5);

    return view('admin.dashboard', compact('users', 'stats', 'pendingOrganizers'));
}

    /**
     * Display pending organizer requests
     */
    public function pendingOrganizers()
    {
        $organizers = User::with(['organizerProfile']) // Matches User.php
                 ->where('role', 'organizer')
                 ->whereNull('is_approved')
                 ->latest()
                 ->paginate(10);

        return view('admin.organizers.pending', compact('organizers'));
    }

    /**
     * Approve an organizer request
     */
public function approveOrganizer(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    // Validation
    if ($user->role !== 'organizer' || $user->is_approved !== null) {
        return redirect()->back()->with('error', 'Invalid approval request.');
    }

    DB::transaction(function () use ($user) {
        // Update user status
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'is_rejected' => false,
            'rejected_at' => null,
        ]);

        // Create minimal organizer profile
        Organizer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone, // Copies from user's phone
            'events_completed' => 0
        ]);

        $user->notify(new OrganizerApprovedNotification());
    });

    return redirect()->back()->with('success', 'Organizer approved successfully.');
}

/**
 * Reject an organizer request
 */
public function rejectOrganizer(Request $request, $id)
{
    $request->validate(['reason' => 'nullable|string|max:255']);
    
    $user = User::findOrFail($id);
    
    if ($user->role !== 'organizer' || $user->is_approved !== null) {
        return redirect()->back()->with('error', 'Invalid rejection request.');
    }

    $user->update([
        'is_approved' => false,
        'is_rejected' => true,
        'rejected_at' => now(),
        'rejection_reason' => $request->reason,
    ]);

    $user->notify(new OrganizerRejectedNotification($request->reason));

    return redirect()->back()->with('success', 'Organizer request rejected.');
}
    /**
     * Display approved organizers
     */
    public function approvedOrganizers()
    {
        $organizers = User::with(['organizerProfile'])  // Changed from 'organiser'
                        ->where('role', 'organizer')
                        ->where('is_approved', true)
                        ->latest()
                        ->paginate(10);

        return view('admin.organizers.approved', compact('organizers'));
    }
    /**
     * Display rejected organizer requests
     */
    public function rejectedOrganizers()
    {
        $organizers = User::with(['organizerProfile'])  // Changed from 'organiser'
                        ->where('role', 'organizer')
                        ->where('is_approved', false)
                        ->latest()
                        ->paginate(10);

        return view('admin.organizers.rejected', compact('organizers'));
    }
    /**
     * Revoke organizer status (demote to gamer)
     */
    public function revokeOrganizer(Request $request, User $user)
    {
        Gate::authorize('revoke-organizer', $user);

        if ($user->role !== 'organizer' || !$user->is_approved) {
            return redirect()->back()
                           ->with('error', 'Invalid organizer revocation request');
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'role' => 'gamer',
                'is_approved' => null,
                'approved_at' => null,
                'rejected_at' => null,
                'rejection_reason' => null
            ]);

            // Soft delete organizer profile
            $user->organiser()->delete();
        });

        return redirect()->back()
                       ->with('success', 'Organizer status revoked successfully.');
    }

    /**
     * Restore rejected organizer request
     */
    public function restoreOrganizer(Request $request, User $user)
    {
        Gate::authorize('restore-organizer', $user);

        if ($user->role !== 'organizer' || $user->is_approved !== false) {
            return redirect()->back()
                           ->with('error', 'Invalid organizer restoration request');
        }

        $user->update([
            'is_approved' => null,
            'rejected_at' => null,
            'rejection_reason' => null
        ]);

        return redirect()->back()
                       ->with('success', 'Organizer request restored to pending status.');
    }

    /**
     * Show organizer details
     */
    public function showOrganizer(User $user)
    {
        if ($user->role !== 'organizer') {
            abort(404);
        }

        $organizer = $user->load(['organiser', 'organizedEvents']);

        return view('admin.organizers.show', compact('organizer'));
    }

    
}