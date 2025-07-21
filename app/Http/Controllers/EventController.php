<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    // Public event listing
    public function publicIndex()
    {
        $events = Event::where('start_date', '>', now()) // Only events in the future
                    ->orderBy('start_date', 'asc')
                    ->get();
        return view('events', compact('events'));
    }

public function publicShow($id)
{
    $event = Event::with(['organizer', 'participants'])->findOrFail($id);
    return view('eventdetails', compact('event')); // Ensure this matches your view filename
}


    // Organizer's event listing
    public function organizerIndex()
    {
        $events = Event::withCount('participants')
                    ->where('user_id', Auth::id())
                    ->orderBy('start_time')
                    ->paginate(10);

        return view('events.organizer.index', compact('events'));
    }

    // Show event creation form
    public function create()
    {
        return view('events.organizer.create');
    }

    // Store new event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'max_participants' => 'nullable|integer|min:1',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $event = new Event($validated);
        $event->user_id = Auth::id();
        $event->save();

        return redirect()->route('organizer.events.show', $event)
                       ->with('success', 'Event created successfully!');
    }

    // Show single event
    public function show(Event $event)
    {
        $this->authorize('view', $event);
        
        $event->load(['organizer', 'participants']);
        return view('events.organizer.show', compact('event'));
    }

    // Show event edit form
    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        return view('events.organizer.edit', compact('event'));
    }

    // Update event
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'max_participants' => 'nullable|integer|min:1',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $event->update($validated);

        return redirect()->route('organizer.events.show', $event)
                       ->with('success', 'Event updated successfully!');
    }

    // Delete event
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        if ($event->participants()->exists()) {
            return back()->with('error', 'Cannot delete event with registered participants');
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
                       ->with('success', 'Event deleted successfully!');
    }

    // Gamer event registration
public function register(Request $request, $id)
{
    // Find the event
    $event = Event::with('participants')->findOrFail($id);
    
    // Get the authenticated user
    $user = Auth::user();

    // Check if the user is already registered
    if ($event->participants()->where('user_id', $user->id)->exists()) {
        return redirect()->route('events.public.register')->with('error', 'You are already registered for this event.');
    }

    // Register the user
    $event->participants()->attach($user->id, [
        'registered_at' => now(),
        'attended' => false,
    ]);

    // Redirect to the event registration confirmation page
    return redirect()->route('events.public.register')->with('success', 'You have successfully registered for "'.$event->title.'" event.');
}

    // Gamer event unregistration
    public function unregister(Request $request, Event $event)
    {
        $this->authorize('cancelRegistration', $event);
        
        $user = Auth::user();
        $event->participants()->detach($user->id);

        return back()->with('success', 'Successfully unregistered from the event');
    }

    public function publicRegister(Request $request)
{
    $query = Event::query()->orderBy('start_date', 'asc');
    
    if ($request->has('search')) {
        $query->where('title', 'like', '%'.$request->search.'%');
    }
    
    if ($request->has('date')) {
        $query->whereDate('start_date', $request->date);
    }
    
    if ($request->has('location')) {
        $query->where('location_name', 'like', '%'.$request->location.'%');
    }
    
    $events = $query->paginate(9);
    
    return view('eventregister', compact('events'));
}
 public function checkout(Request $request, $eventId)
    {
        // Debug: Check if Stripe key is loaded
        logger('Stripe Secret: ' . config('services.stripe.secret'));
        
        $event = Event::findOrFail($eventId);
        Stripe::setApiKey(config('services.stripe.secret'));

        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'myr', // For Malaysian Ringgit
                    'product_data' => [
                        'name' => $event->title,
                        'description' => substr($event->description, 0, 100),
                    ],
                    'unit_amount' => $event->total_fee * 100, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('events.payment.success', $eventId),
            'cancel_url' => route('events.public.show', $eventId),
            'metadata' => [
                'event_id' => $eventId,
                'user_id' => $user->id
            ]
        ]);

        return redirect()->away($session->url);
    }

public function paymentSuccess(Request $request, $eventId)
{
    $event = Event::findOrFail($eventId);
    $user = Auth::user();

    try {
        // Use syncWithoutDetaching to prevent duplicates
        $event->participants()->syncWithoutDetaching([
            $user->id => [
                'registered_at' => now(),
                'attended' => false
            ]
        ]);

        // Refresh the event to get updated participants count
        $event->refresh();

        // Create or update payment record
        Payment::updateOrCreate(
            [
                'user_id' => $user->id,
                'event_id' => $event->id
            ],
            [
                'stripe_payment_id' => 'temp_' . uniqid(),
                'amount' => $event->total_fee,
                'status' => 'completed'
            ]
        );

        // Explicitly increment participants count
        $event->increment('participants_count');

    } catch (QueryException $e) {
        Log::error('Payment success error: ' . $e->getMessage());
    }

    return view('events.payment-success', compact('event'));
}

public function showMap()
{
    // Get events within 50km radius (we'll implement this logic in the view)
    $events = Event::where('is_approved', true)
                  ->where('start_date', '>=', now())
                  ->with('game')
                  ->get();

    return view('events.map', compact('events'));
}

    // Handle Stripe webhook for payment confirmation
public function handleWebhook(Request $request)
{
    $payload = $request->getContent();
    $sigHeader = $request->header('Stripe-Signature');
    $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $endpointSecret
        );
    } catch (\Exception $e) {
        Log::error('Stripe webhook verification failed: ' . $e->getMessage());
        return response()->json(['error' => 'Invalid signature'], 403);
    }

    if ($event->type == 'checkout.session.completed') {
        $session = $event->data->object;
        
        // Verify the payment was successful
        if ($session->payment_status !== 'paid') {
            Log::warning('Received unpaid session: ' . $session->id);
            return response()->json(['status' => 'ignored_unpaid']);
        }

        try {
            $event = Event::withCount('participants')->find($session->metadata->event_id);
            $user = User::find($session->metadata->user_id);

            if (!$event || !$user) {
                Log::error('Webhook references missing data', [
                    'event_id' => $session->metadata->event_id,
                    'user_id' => $session->metadata->user_id
                ]);
                return response()->json(['error' => 'Invalid references'], 400);
            }

            // Use database transaction for atomic operations
            DB::transaction(function () use ($event, $user, $session) {
                // Register participant if not already registered
                $wasNewRegistration = $event->participants()
                    ->syncWithoutDetaching([
                        $user->id => [
                            'registered_at' => now(),
                            'attended' => false
                        ]
                    ]);

                // Only update counts if this was a new registration
                if (!empty($wasNewRegistration['attached'])) {
                    $event->increment('participants_count');
                }

                // Create or update payment record
                Payment::updateOrCreate(
                    ['stripe_payment_id' => $session->payment_intent],
                    [
                        'user_id' => $user->id,
                        'event_id' => $event->id,
                        'amount' => $session->amount_total / 100,
                        'status' => 'completed',
                        'payment_method' => $session->payment_method_types[0] ?? 'card'
                    ]
                );

                Log::info('Processed payment', [
                    'event' => $event->id,
                    'user' => $user->id,
                    'amount' => $session->amount_total / 100
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage(), [
                'session_id' => $session->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }       
    }


    return response()->json(['status' => 'success']);
}
}