<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Day;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        $days = null;

        $eventId = 6; // Replace with the specific event ID
        $dayId = 8;   // Replace with the specific day ID

        // Retrieve attendance records for the specific event and day
        $attendances = Attendance::whereHas('day', function ($query) use ($eventId, $dayId) {
            $query->where('event_id', $eventId)->where('id', $dayId);
        })->get();
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
        for ($i = 1; $i <= $event->days; $i++) {
            $day = new Day();
            $day->event_id = $event->id; // Assuming Day model has an 'event_id' column
            $day->day_number = $i;
            // Set other day properties as needed
            $day->save();
        }
    
        // Get all students and days
        $students = Student::all();
        $days = Day::where('event_id', $event->id)->get();
    
        // Create attendance records for each student and day combination
        foreach ($days as $day) {
            foreach ($students as $student) {
                Attendance::create([
                    'day_id' => $day->id,
                    'student_id' => $student->id,
                    'm_in' => false,
                    'm_out' => false,
                    'af_in' => false,
                    'af_out' => false,
                ]);
            }
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
         // Load the days associated with the event
         $days = DB::table('days')
                     ->where('event_id', $event->id)
                     ->orderBy('id')
                     ->get();
     
         return view('admin.events.update', compact('event', 'days'));
     }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Validation logic and updating the event in the database
        $validatedData = $request->validate([
            'name' => 'required',
            // Add more validation rules for your event fields
        ]);

        $event->name = $request->name;
        // Update other event properties as needed
        $event->save();

        // Update the days associated with the event
        foreach ($request->days as $dayId => $dayData) {
            $day = Day::findOrFail($dayId);

            // Convert time input to valid datetime format using Carbon
            if (isset($dayData['sign_in_morning'])) {
                $day->sign_in_morning = Carbon::parse($dayData['sign_in_morning'])->format('Y-m-d H:i:s');
            }
            if (isset($dayData['sign_out_morning'])) {
                $day->sign_out_morning = Carbon::parse($dayData['sign_out_morning'])->format('Y-m-d H:i:s');
            }
            if (isset($dayData['sign_in_afternoon'])) {
                $day->sign_in_afternoon = Carbon::parse($dayData['sign_in_afternoon'])->format('Y-m-d H:i:s');
            }
            if (isset($dayData['sign_out_afternoon'])) {
                $day->sign_out_afternoon = Carbon::parse($dayData['sign_out_afternoon'])->format('Y-m-d H:i:s');
            }

            $day->save();
        }

        // Redirect to the index or show page after successful update
        return redirect()->route('events.index')->with('success', 'Event and days updated successfully');
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