<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Day;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        $days = null;
        return view('admin.events.index', compact('events', 'days'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation logic and saving the new event to the database
        $validatedData = $request->validate([
            'name' => 'required',
            'days' => 'required',
            // Add more validation rules for your event fields
        ]);

        $event = new Event();
        $event->name = $request->name;
        $event->days = $request->days;
        // Set other event properties as needed
        $event->save();

        
        // Create days based on $event->days
        $days = [];
        for ($i = 1; $i <= $event->days; $i++) {
            $day = new Day();
            $day->event_id = $event->id; // Assuming Day model has an 'event_id' column
            $day->day_number = $i;
            // Set other day properties as needed
            $day->save();

            $days[] = $day;
        }

        // Redirect to the index or show page after successful creation
        return redirect()->route('events.index')->with('success', 'Event created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $days = Day::where('event_id', $event->id)->get();
        return view('admin.events.index', compact('event', 'days'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('admin.events.index', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Validation logic and updating the event in the database
        $validatedData = $request->validate([
            'name' => 'required',
            'days' => 'required',
            // Add more validation rules for your event fields
        ]);

        $event->name = $request->name;
        // Update other event properties as needed
        $event->save();

        // Redirect to the index or show page after successful update
        return redirect()->route('events.index')->with('success', 'Event updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        // Redirect to the index page after successful deletion
        return redirect()->route('events.index')->with('success', 'Event deleted successfully');
    }
}