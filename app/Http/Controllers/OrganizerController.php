<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Participant;
use App\Models\Organizer;
use Illuminate\Support\Facades\Log;

class OrganizerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:organizer-access');
    }

public function dashboard()
{
    $user = auth()->user();

    // Stats (unchanged)
    $totalEvents = Event::where('organizer_id', $user->id)->count();
    $completedEvents = Event::where('organizer_id', $user->id)
        ->where('end_time', '<', now())
        ->count();
    $pastEvents = Event::where('organizer_id', $user->id)
        ->where('end_time', '<', now())
        ->count();
    $totalParticipants = Participant::whereHas('event', function ($query) use ($user) {
        $query->where('organizer_id', $user->id);
    })->count();

    // Get only upcoming events (using the scope from Event model)
    $upcomingEvents = Event::where('organizer_id', $user->id)
        ->upcoming() // Using the upcoming scope
        ->orderBy('start_date')
        ->orderBy('start_time')
        ->take(5)
        ->get();

    // Recent participants (unchanged)
    $recentParticipants = Participant::with('user:id,name,email')
        ->whereHas('event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })
        ->latest()
        ->take(5)
        ->get(['id', 'user_id', 'event_id']);

    $stats = [
        'totalEvents' => $totalEvents,
        'completedEvents' => $completedEvents,
        'pastEvents' => $pastEvents,
        'totalParticipants' => $totalParticipants,
    ];

    return view('organizer.dashboard', compact(
        'stats',
        'upcomingEvents',  // Changed variable name
        'recentParticipants'
    ));
}
    public function create()
    {
        return view('admin.organizers.create'); // Create the view file next
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:organizers',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create the organizer
        Organizer::create([
            'user_id' => auth()->id(), // Assuming the organizer is linked to the logged-in user
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('admin.organizer2')->with('success', 'Organizer added successfully!');
    }

}