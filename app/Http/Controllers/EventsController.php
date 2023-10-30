<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Day;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ECMember;

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
    
        if (is_null($request->days)) {
            // Get all days associated with the event
            $days = Day::where('event_id', $event->id)->get();
    
            foreach ($days as $day) {
                foreach (['sign_in_morning', 'sign_out_morning', 'sign_in_afternoon', 'sign_out_afternoon', 'date'] as $field) {
                    $day->$field = null;
                    $day->save();
                    $this->updateAttendanceShift($day, 'm_in', 'sign_in_morning');
                    $this->updateAttendanceShift($day, 'm_out', 'sign_out_morning');
                    $this->updateAttendanceShift($day, 'af_in', 'sign_in_afternoon');
                    $this->updateAttendanceShift($day, 'af_out', 'sign_out_afternoon');
                }
            }
        } else {
            // Update the days associated with the event
            foreach ($request->days as $dayId => $dayData) {
                $day = Day::findOrFail($dayId);
                
                foreach (['date', 'sign_in_morning', 'sign_out_morning', 'sign_in_afternoon', 'sign_out_afternoon'] as $field) {
                    if (isset($dayData[$field])) {
                        if ($field === 'date') {
                            $dateValue = trim($dayData[$field]); // Trim whitespace
                            // Validate the date format using Carbon
                            if (Carbon::createFromFormat('Y-m-d', $dateValue)) {
                                $day->$field = $dateValue;
                            } else {
                                // Handle invalid date format here
                                // For example, set an error message or log the issue
                            }
                        } elseif (!is_null($dayData[$field])) {
                            $timeValue = trim($dayData[$field]); // Trim whitespace
                
                            // Validate the time format using regular expression
                            if (preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $timeValue)) {
                                $value = Carbon::createFromFormat('H:i', $timeValue)->format('H:i:s');
                                $day->$field = $value;
                            } else {
                                // Handle invalid time format here
                                // For example, set an error message or log the issue
                            }
                        } else {
                            $day->$field = null;
                        }
                    }
                }
        
                $day->save();
        
                if ($day) {
                    // Update the attendance shift based on $day and $studentId
                    $this->updateAttendanceShift($day, 'm_in', 'sign_in_morning', $request->student_id);
                    $this->updateAttendanceShift($day, 'm_out', 'sign_out_morning', $request->student_id);
                    $this->updateAttendanceShift($day, 'af_in', 'sign_in_afternoon', $request->student_id);
                    $this->updateAttendanceShift($day, 'af_out', 'sign_out_afternoon', $request->student_id);
                }
            }
        }
    
        // Redirect to the index or show page after successful update
        return redirect()->route('events.index')->with('success', 'Event and days updated successfully');
    }    
    
    protected function updateAttendanceShift($day, $shiftField, $timeField, $studentId)
{
    if ($day->$timeField !== null) {
        $attendances = Attendance::where('day_id', $day->id)->get(); 
        foreach ($attendances as $attendance) {
            if ($attendance->$shiftField->value != 1) {
                // Check if the student is an EC Member
                $isECMember = ECMember::where('id_no', $studentId)->exists();

                if ($isECMember) {
                    $attendance->$shiftField = 6; 
                } else {
                    $attendance->$shiftField = 5;
                }

                $attendance->save();
            }
        }
    } else {
        // Check if the student is an EC Member
        $isECMember = ECMember::where('id_no', $studentId)->exists();

        if ($isECMember) {
            Attendance::where('day_id', $day->id)->update([$shiftField => 6]);
        } else {
            Attendance::where('day_id', $day->id)->update([$shiftField => 0]);
        }
    }
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