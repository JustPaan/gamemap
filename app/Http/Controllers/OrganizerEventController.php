<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrganizerEventController extends Controller
{
public function index()
{
    $user = Auth::user();

    if (!$user->is_approved || $user->role !== 'organizer') {
        return redirect()->route('home')
            ->with('error', 'Only approved organizers can access this page');
    }

    $events = Event::where('organizer_id', $user->id)
        ->withCount('participants')
        ->latest()
        ->paginate(6);

    // Total events count
    $totalEvents = Event::where('organizer_id', $user->id)->count();

    // Ongoing Events (started but not ended)
    $ongoingEvents = Event::where('organizer_id', $user->id)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->count();

    // Upcoming Events (not started yet)
    $upcomingEvents = Event::where('organizer_id', $user->id)
        ->where('start_date', '>', now())
        ->count();

    // Completed Events (already ended)
    $completedEvents = Event::where('organizer_id', $user->id)
        ->where('end_date', '<', now())
        ->count();

    // Total participants across all events
    $totalParticipants = Event::where('organizer_id', $user->id)
        ->withCount('participants')
        ->get()
        ->sum('participants_count');

    return view('organizer.event', [
        'events' => $events,
        'totalEvents' => $totalEvents,
        'ongoingEvents' => $ongoingEvents,
        'upcomingEvents' => $upcomingEvents,
        'completedEvents' => $completedEvents,
        'totalParticipants' => $totalParticipants,
    ]);
}

    public function create()
    {
        $user = Auth::user();

        if (!$user || !$user->is_approved || $user->role !== 'organizer') {
            return redirect()->route('home')
                ->with('error', 'Only approved organizers can create events');
        }

        $games = Game::where('is_deleted', false)->get();

        return view('organizer.event.form', compact('games')); 
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location_name' => 'required|string|max:255',
            'location_lng' => 'required|numeric|between:-180,180',
            'location_lat' => 'required|numeric|between:-90,90',
            'max_participants' => 'required|integer|min:1',
            'total_fee' => 'nullable|numeric|min:0',
            'device_type' => 'required|in:PC,Mobile,Console',
            'game_id' => 'required|exists:games,id',
            'game_type' => 'required|in:FIGHTING,RPG,FPS,TBS,SPORT,ARCADE,RACING,MMORPG,TPS,STRATEGY',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Additional validation when dates are same
        if ($validated['start_date'] === $validated['end_date'] && 
            $validated['start_time'] >= $validated['end_time']) {
            return back()->withErrors(['end_time' => 'End time must be after start time when dates are same']);
        }

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create an event.');
        }

        $organizer = Auth::user();

        // Create event with location details
        $event = Event::create([
            'title' => $validated['title'], 
            'description' => $validated['description'],
            'start_date' => Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']),
            'end_date' => Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'game_id' => $validated['game_id'],
            'organizer_id' => $organizer->id,
            'location_name' => $validated['location_name'],
            'location_lng' => $validated['location_lng'],
            'location_lat' => $validated['location_lat'],
            'max_participants' => $validated['max_participants'],
            'device_type' => $validated['device_type'],
            'game_type' => $validated['game_type'],
            'total_fee' => $validated['total_fee'] ?? 0,
            'is_approved' => false,
        ]);

        // Handle file upload
        if ($request->hasFile('event_image')) {
            $path = $request->file('event_image')->store('game_images', 'public');
            $event->update(['image_path' => $path]);
        }

        return redirect()->route('organizer.event')->with('success', 'Event created successfully!');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        
        // Authorization check
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('organizer.event')
                ->with('error', 'You are not authorized to edit this event');
        }

        $games = Game::where('is_deleted', false)->get();

        return view('organizer.event.edit', compact('event', 'games'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        // Authorization check
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('organizer.event')
                ->with('error', 'You are not authorized to update this event');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location_name' => 'required|string|max:255',
            'longitude' => 'required|numeric|between:-180,180',
            'latitude' => 'required|numeric|between:-90,90',
            'max_participants' => 'required|integer|min:1',
            'total_fee' => 'nullable|numeric|min:0',
            'device_type' => 'required|in:PC,Mobile,Console',
            'game_id' => 'required|exists:games,id',
            'game_type' => 'required|in:FIGHTING,RPG,FPS,TBS,SPORT,ARCADE,RACING,MMORPG,TPS,STRATEGY',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Additional validation when dates are same
        if ($validated['start_date'] === $validated['end_date'] && 
            $validated['start_time'] >= $validated['end_time']) {
            return back()->withErrors(['end_time' => 'End time must be after start time when dates are same']);
        }

        // Update event data
        $event->update([
            'title' => $validated['title'], 
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'game_id' => $validated['game_id'],
            'location_name' => $validated['location_name'],
            'location_lng' => $validated['longitude'],
            'location_lat' => $validated['latitude'],
            'max_participants' => $validated['max_participants'],
            'device_type' => $validated['device_type'],
            'game_type' => $validated['game_type'],
            'total_fee' => $validated['total_fee'] ?? 0,
        ]);

        // Handle file upload
        if ($request->hasFile('event_image')) {
            // Delete old image if exists
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            
            $path = $request->file('event_image')->store('game_images', 'public');
            $event->update(['image_path' => $path]);
        }

        return redirect()->route('organizer.event')
                         ->with('success', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        
        // Authorization check
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('organizer.event')
                ->with('error', 'You are not authorized to delete this event');
        }

        // Check if event has participants
        if ($event->participants()->count() > 0) {
            return redirect()->route('organizer.event')
                ->with('error', 'Cannot delete event with active participants');
        }

        // Delete image if exists
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return redirect()->route('organizer.event')
                         ->with('success', 'Event deleted successfully!');
    }

    public function viewDetails($id)
    {
        $event = Event::withCount('participants')->findOrFail($id);
        
        // Authorization check
        if (Auth::id() !== $event->organizer_id) {
            return redirect()->route('organizer.event')
                ->with('error', 'You are not authorized to view this event');
        }

        $totalCollectedFees = $event->participants_count * $event->total_fee;

        return view('organizer.eventdetail', compact('event', 'totalCollectedFees'));
    }
}