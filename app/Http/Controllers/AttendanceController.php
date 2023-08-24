<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Day;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::query();
    
        // Apply filters if provided
        if ($request->filled('event_id')) {
            $query->whereHas('day.event', function ($query) use ($request) {
                $query->where('id', $request->input('event_id'));
            });
        }
    
        if ($request->filled('day_number')) {
            $query->whereHas('day', function ($query) use ($request) {
                $query->where('day_number', $request->input('day_number'));
            });
        }

        if ($request->filled('year_level')) {
            $query->whereHas('attendaces.student', function ($query) use ($request) {
                $query->where('year_level', $request->input('year_level'));
            });
        }
    
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('student_id', 'LIKE', "%$searchTerm%")
                    ->orWhereHas('student', function ($query) use ($searchTerm) {
                        $query->where('year_level', 'LIKE', "%$searchTerm%")
                              ->orWhere('full_name', 'LIKE', "%$searchTerm%");
                    });
            });
        }
    
        $attendances = $query->with(['day.event', 'student'])
                            ->join('students', 'attendances.student_id', '=', 'students.id')
                            ->join('days', 'attendances.day_id', '=', 'days.id')
                            ->orderBy('students.year_level', 'asc')
                            ->orderBy('days.day_number', 'asc')
                            ->get();

        $events = Event::all();
    
        if ($request->ajax()) {
            return view('attendances.table', compact('attendances', 'events'));
        }
    
        return view('attendances.index', compact('attendances', 'events'));
    }

    public function export(Request $request)
    {
        $export = new AttendanceExport($request);
        $fileName = 'attendance_export.xlsx'; // You can set the desired file name

        return Excel::download($export, $fileName);
    }

    public function exportData(Request $request)
    {
        $export = new AttendanceExport($request);
    
        return Excel::download($export, 'attendance_export.xlsx');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|exists:students,id',
            'attendance_type' => 'required|in:m_in,m_out,af_in,af_out'
        ]);

        $studentId = $request->input('student_id');
        $attendanceType = $request->input('attendance_type');

        // Update the attendance based on studentId and attendanceType
        $attendance = Attendance::where('student_id', $studentId)->first();
        if ($attendance) {
            $attendance->$attendanceType = 1; // Set the attendance type to 1 (Present)
            $attendance->save();

            return response()->json(['message' => 'Attendance updated successfully']);
        } else {
            return response()->json(['message' => 'Attendance not found'], 404);
        }
    }

    public function updateAttendance(Request $request)
    {
        $requestData = $request->all();

        $event = Event::where('name', $requestData['event_name'])->first();
        $student = Student::where('id_no', $requestData['student_id'])->first();
        $dayNumber = intval($requestData['day_number']);
        $day = Day::where('event_id', $event->id)
                    ->where('day_number', $dayNumber)
                    ->first();
       
        $attendance = Attendance::where('day_id', $day->id)->where('student_id', $student->id)->first();

        $signInMorning = new DateTime($day->sign_in_morning);
        $endsignInMorning = (clone $signInMorning)->modify('+1 hour');
        $signOutMorning = new DateTime($day->sign_out_morning);
        $endsignOutMorning = (clone $signOutMorning)->modify('+1 hour');
        $signInAfternoon = new DateTime($day->sign_in_afternoon);
        $endsignInAfternoon = (clone $signInAfternoon)->modify('+1 hour');
        $signOutAfternoon = new DateTime($day->sign_out_afternoon);
        $endsignOutAfternoon = (clone $signOutAfternoon)->modify('+1 hour');
        $signTime = new DateTime($requestData['sign_time']);
        
        $message = "Already Signed In: " . $student->id_no;

        if ($signTime > $signInMorning && $signTime < $endsignInMorning) {
            if($attendance->m_in->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->m_in = 1;
            }
        } else if ($signTime > $signOutMorning && $signTime < $endsignOutMorning) {
            if($attendance->m_out->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->m_out = 1;
            }
        } else if ($signTime > $signInAfternoon && $signTime < $endsignInAfternoon) {
            if($attendance->af_in->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->af_in = 1;
            }
        } else if ($signTime > $signOutAfternoon && $signTime < $endsignOutAfternoon) {
            if($attendance->af_out->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->af_out = 1;
            }
        } else {
            $message = "No Sign In/Out Scheduled";
            return redirect()->route('student.search')->with('error', $message);  
        }

        $attendance->save();

        $message = "ID No: " . $student->id_no;

        return redirect()->route('student.search')->with('success', $message);        
    }

    public function dashboard(Request $request)
    {
        // Get the selected event and day IDs from the request
        $selectedEventId = $request->input('event');
        $selectedDayId = $request->input('day');
    
        // Retrieve data for the charts with filters
        $yearLevelData = $this->applyFiltersToYearLevelData($selectedEventId, $selectedDayId);
        $idNoData = $this->applyFiltersToIdNoData($selectedEventId, $selectedDayId);
        $mInData = $this->applyFiltersToMInData($selectedEventId, $selectedDayId);
        $afOutData = $this->applyFiltersToAfOutData($selectedEventId, $selectedDayId);
    
        // Extract data for year_level chart
        $yearLevelLabels = [];
        $dataByYearLevel = [];
    
        foreach ($yearLevelData as $attendance) {
            $yearLevel = $attendance->year_level;
    
            $yearLevelLabels[] = $yearLevel;
            $dataByYearLevel[$yearLevel] = $attendance->count;
        }
    
        // Extract data for id_no scatter plot
        $scatterData = [];
    
        foreach ($idNoData as $attendance) {
            $idNoYear = $attendance->id_no_year;
            $count = $attendance->count;
    
            // Format data for scatter plot
            $scatterData[] = ['x' => $idNoYear, 'y' => $count];
        }
    
        // Data for m_in bar chart
        $mInLabels = ['m_in = 1', 'm_in != 1'];
        $dataForMIn = [$mInData->m_in_count, $mInData->not_m_in_count];
    
        // Data for af_out bar chart
        $afOutLabels = ['af_out = 1', 'af_out != 1'];
        $dataForAfOut = [$afOutData->af_out_count, $afOutData->not_af_out_count];
    
        // Get the list of events and days for the filters
        $events = Event::all();
        $days = Day::all();
    
        // Pass the variables to the view
        return view('dashboard', compact('yearLevelLabels', 'dataByYearLevel', 'scatterData', 'mInLabels', 'dataForMIn', 'afOutLabels', 'dataForAfOut', 'selectedEventId', 'selectedDayId', 'events', 'days'));
    }
    

    protected function applyFiltersToYearLevelData($eventId, $dayId)
    {
        return Day::join('attendances', 'days.id', '=', 'attendances.day_id')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('days.event_id', $eventId);
            })
            ->when($dayId, function ($query) use ($dayId) {
                $query->where('days.id', $dayId);
            })
            ->select('students.year_level', 
                DB::raw('COUNT(CASE WHEN attendances.m_in = 1 OR attendances.af_out = 1 THEN 1 ELSE NULL END) as count'))
            ->groupBy('students.year_level')
            ->get();
    }

    protected function applyFiltersToIdNoData($eventId, $dayId)
    {
        return Day::join('attendances', 'days.id', '=', 'attendances.day_id')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('days.event_id', $eventId);
            })
            ->when($dayId, function ($query) use ($dayId) {
                $query->where('days.id', $dayId);
            })
            ->select(DB::raw("SUBSTRING(students.id_no, 1, 4) as id_no_year"), 
                DB::raw('COUNT(CASE WHEN attendances.m_in = 1 OR attendances.af_out = 1 THEN 1 ELSE NULL END) as count'))
            ->groupBy('id_no_year')
            ->get();
    }

    protected function applyFiltersToMInData($eventId, $dayId)
    {
        return Day::join('attendances', 'days.id', '=', 'attendances.day_id')
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('days.event_id', $eventId);
            })
            ->when($dayId, function ($query) use ($dayId) {
                $query->where('days.id', $dayId);
            })
            ->select(DB::raw('COUNT(CASE WHEN attendances.m_in = 1 THEN 1 ELSE NULL END) as m_in_count'),
                     DB::raw('COUNT(CASE WHEN attendances.m_in = 0 THEN 1 ELSE NULL END) as not_m_in_count'))
            ->first();
    }

    protected function applyFiltersToAfOutData($eventId, $dayId)
    {
        return Day::join('attendances', 'days.id', '=', 'attendances.day_id')
            ->when($eventId, function ($query) use ($eventId) {
                $query->where('days.event_id', $eventId);
            })
            ->when($dayId, function ($query) use ($dayId) {
                $query->where('days.id', $dayId);
            })
            ->select(DB::raw('COUNT(CASE WHEN attendances.af_out = 1 THEN 1 ELSE NULL END) as af_out_count'),
                     DB::raw('COUNT(CASE WHEN attendances.af_out = 0 THEN 1 ELSE NULL END) as not_af_out_count'))
            ->first();
    }
    
    
    
}
