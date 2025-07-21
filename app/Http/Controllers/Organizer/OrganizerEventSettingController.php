<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\EventSettingRequest;
use App\Models\Event;
use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrganizerEventSettingController extends Controller
{
    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        $games = Game::where('is_deleted', false)->get();
        $deviceTypes = $games->pluck('device_type')->unique();

        return view('organizer.eventsetting', [
            'games' => $games,
            'deviceTypes' => $deviceTypes
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(EventSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Log::info($validated);

        // Create event with location data directly
        $event = Event::create([
            'title' => $validated['event_name'],
            'description' => $validated['description'],
            'start_time' => $validated['event_date'] . ' ' . $validated['start_time'],
            'end_time' => $validated['event_date'] . ' ' . $validated['end_time'],
            'game_id' => $validated['game_id'],
            'organizer_id' => Auth::id(),
            'max_participants' => $validated['max_participants'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            // Add other required fields from your request
        ]);

        // Handle image upload if present
        if ($request->hasFile('event_image')) {
            $event->addMediaFromRequest('event_image')->toMediaCollection('event_images');
        }

        return redirect()->route('organizer.event')->with('success', 'Event created successfully!');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        
        $games = Game::where('is_deleted', false)->get();
        $deviceTypes = $games->pluck('device_type')->unique();

        return view('organizer.eventsetting', [
            'event' => $event,
            'games' => $games,
            'deviceTypes' => $deviceTypes
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(EventSettingRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validated();

        // Update event with location data directly
        $event->update([
            'title' => $validated['event_name'],
            'description' => $validated['description'],
            'start_time' => $validated['event_date'] . ' ' . $validated['start_time'],
            'end_time' => $validated['event_date'] . ' ' . $validated['end_time'],
            'game_id' => $validated['game_id'],
            'max_participants' => $validated['max_participants'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        // Update image if changed
        if ($request->hasFile('event_image')) {
            $event->clearMediaCollection('event_images');
            $event->addMediaFromRequest('event_image')->toMediaCollection('event_images');
        }

        return redirect()->route('organizer.event')->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()->route('organizer.event')->with('success', 'Event deleted successfully!');
    }
}