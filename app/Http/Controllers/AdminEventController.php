<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminEventController extends Controller
{
    /**
     * Display events with optional search filters
     */
    public function index(Request $request)
    {
        $query = Event::query();
        
        // Search by organizer ID
        if ($request->has('organizer_id')) {
            $query->where('organizer_id', $request->organizer_id);
        }
        
        // Search by venue
        if ($request->has('venue')) {
            $query->where('venue', 'like', '%' . $request->venue . '%');
        }

        // Search by event title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Always get events (even if no filters)
        $events = $query->get();
        
        return view('admin.events', compact('events')); // Pass $events to the view
    }
    public function store(Request $request)
    {
        $request->validate([
            'venue' => 'required|string|max:255',
            'participants' => 'required|integer|min:0',
            'total_fee' => 'required|numeric|min:0',
        ]);

        // Use transaction for data consistency
        DB::transaction(function () use ($request) {
            // Create the event
            $event = Event::create([
                'eventID' => (string) Str::uuid(),
                'venue' => $request->venue,
                'participants' => $request->participants,
                'total_fee' => $request->total_fee,
                'organizer_id' => auth()->id(),
            ]);

            // Increment the organizer's events_completed count
            Organizer::where('user_id', auth()->id())
                ->increment('events_completed');
        });

        return redirect()->route('admin.event2')->with('success', 'Event created successfully.');
    }

    /**
     * Generate event report
     */
    public function generateReport($eventId)
    {
        $event = Event::with(['organizer', 'participants'])->findOrFail($eventId); // Adjust as necessary

        return view('admin.eventsreport', compact('event'));
    }

    public function showReport()
    {
        $events = Event::with(['organizer', 'participants'])->get(); // Assuming you have relationships defined

        return view('admin.eventsreport', compact('events'));
    }
}