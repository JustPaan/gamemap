<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Notifications\OrganizerApprovedNotification;
use App\Notifications\OrganizerRejectedNotification;

class AdminOrganizerController extends Controller
{
public function index()
{
    Log::channel('organizers')->info('Admin accessing organizers list');

    // Get all organizers with their user data and event counts
    $organizers = User::where('role', 'organizer')
        ->with(['registeredEvents' => function ($query) {
            $query->select('user_id', 'organizer_id', 'title')
                  ->withCount('participants'); // Count the registered participants
        }])
        ->orderBy('name')
        ->paginate(10);

    Log::info('Fetched organizers:', $organizers->toArray());

    return view('admin.organizer2', [
        'organizers' => $organizers,
        'stats' => [
            'total' => User::where('role', 'organizer')->count(),
            'active' => User::where('role', 'organizer')->where('is_approved', true)->count(),
        ]
    ]);
}

    public function create()
    {
        return view('admin.organizeradd', [
            'games' => \App\Models\Game::all() // For organizer-game associations
        ]);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'phone' => 'nullable|string|max:20',
        'organization' => 'nullable|string|max:255',
        'password' => 'required|string|min:8|confirmed',
    ], [
        'email.unique' => 'This email is already registered',
        'password.min' => 'Password must be at least 8 characters'
    ]);

    try {
        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'organizer',
                'is_approved' => true, // Auto-approve admin-created organizers
                'organization' => $validated['organization'] ?? null,
            ]);

            Organizer::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'organization' => $validated['organization'] ?? null,
                'events_completed' => 0
            ]);

            Log::channel('organizers')->info('New organizer created', [
                'id' => $user->id,
                'email' => $user->email
            ]);
        });

        return redirect()->route('admin.organizer2')
               ->with('success', 'Organizer created successfully');

    } catch (\Exception $e) {
        Log::channel('organizers')->error('Organizer creation failed', [
            'error' => $e->getMessage(),
            'input' => $request->except('password', 'password_confirmation')
        ]);

        return back()->withInput()
               ->with('error', 'Failed to create organizer. Please try again.');
    }
}

    public function edit($id)
    {
        $organizer = User::with('managedGames')->findOrFail($id);
        
        abort_unless($organizer->role === 'organizer', 404);

        return view('admin.organizers.edit', [
            'organizer' => $organizer,
            'games' => \App\Models\Game::all(),
            'events' => Event::where('organizer_id', $id)
                           ->latest()
                           ->limit(5)
                           ->get(['event_id', 'title', 'start_time', 'end_time']) // Use event_id instead of id
        ]);
    }

    public function update(Request $request, $id)
    {
        $organizer = User::findOrFail($id);
        
        abort_unless($organizer->role === 'organizer', 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($organizer->id)
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($organizer->id)
            ],
            'is_approved' => 'sometimes|boolean',
            'games' => 'nullable|array',
            'games.*' => 'exists:games,id'
        ]);

        try {
            DB::transaction(function () use ($organizer, $validated) {
                $organizer->update($validated);

                if (isset($validated['games'])) {
                    $organizer->managedGames()->sync($validated['games']);
                }

                Log::channel('organizers')->info('Organizer updated', [
                    'id' => $organizer->id,
                    'changes' => $organizer->getChanges()
                ]);
            });

            return redirect()->route('admin.organizer')
                   ->with('success', 'Organizer updated successfully');

        } catch (\Exception $e) {
            Log::channel('organizers')->error('Organizer update failed', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                   ->with('error', 'Failed to update organizer. Please try again.');
        }
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'organizers' => 'required|array',
            'organizers.*.id' => 'required|exists:users,id',
            'organizers.*.name' => 'required|string|max:255',
            'organizers.*.email' => 'required|email',
            'organizers.*.phone' => 'nullable|string|max:20',
            'organizers.*.is_approved' => 'sometimes|boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->organizers as $data) {
                    $organizer = User::findOrFail($data['id']);
                    
                    abort_unless($organizer->role === 'organizer', 403);
                    
                    $organizer->update([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'is_approved' => $data['is_approved'] ?? $organizer->is_approved,
                    ]);
                }
            });

            Log::channel('organizers')->info('Bulk update completed', [
                'count' => count($request->organizers)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Organizers updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::channel('organizers')->error('Bulk update failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update organizers'
            ], 500);
        }
    }

    public function approveOrganizer(User $user)
    {
        abort_unless($user->requested_organizer, 403, 'User has not requested organizer status');
        
        try {
            DB::transaction(function () use ($user) {
                $user->update([
                    'role' => 'organizer',
                    'is_approved' => true,
                    'is_rejected' => false,
                    'approved_at' => now(),
                    'requested_organizer' => false
                ]);

                // Send approval notification
                $user->notify(new OrganizerApprovedNotification());

                Log::channel('organizers')->info('Organizer request approved', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            });

            return back()->with('success', 'Organizer request approved successfully');

        } catch (\Exception $e) {
            Log::channel('organizers')->error('Organizer approval failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to approve organizer request');
        }
    }

    public function rejectOrganizer(User $user)
    {
        abort_unless($user->requested_organizer, 403, 'User has not requested organizer status');
        
        try {
            DB::transaction(function () use ($user) {
                $user->update([
                    'is_approved' => false,
                    'is_rejected' => true,
                    'rejected_at' => now(),
                    'requested_organizer' => false
                ]);

                // Send rejection notification
                $user->notify(new OrganizerRejectedNotification());

                Log::channel('organizers')->info('Organizer request rejected', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            });

            return back()->with('success', 'Organizer request rejected');

        } catch (\Exception $e) {
            Log::channel('organizers')->error('Organizer rejection failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to reject organizer request');
        }
    }

    public function destroy($id)
    {
        $organizer = User::where('role', 'organizer')->findOrFail($id);
        $organizer->delete();

        return redirect()->route('admin.organizer2')->with('success', 'Organizer deleted successfully.');
    }


    public function cancel()
    {
        return redirect()->route('admin.organizer')
               ->with('info', 'Operation canceled. No changes were made.');
    }

    public function pendingRequests()
    {
        $requests = User::where('requested_organizer', true)
                      ->whereNull('is_approved')
                      ->whereNull('is_rejected')
                      ->latest()
                      ->get(['id', 'name', 'email', 'created_at']);

        return response()->json($requests);
    }

    public function showEventDetails($eventId)
    {
        // Fetch event with participant count
        $event = Event::withCount('participants')->findOrFail($eventId);

        // Access the counts
        $registeredCount = $event->participants_count; // Count of registered participants
        $maxParticipants = $event->max_participants;

        // Return view with event details
        return view('admin.event.details', compact('event', 'registeredCount', 'maxParticipants'));
    }
}