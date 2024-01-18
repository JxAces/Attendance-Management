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
        $attendances = Attendance::with(['day.event', 'student'])
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->join('days', 'attendances.day_id', '=', 'days.id')
            ->orderBy('students.year_level', 'asc')
            ->orderBy('days.day_number', 'asc')
            ->get();
    
        $events = Event::all();
    
        if ($request->ajax()) {
            return response()->json(['attendances' => $attendances]);
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
        $endsignInMorning = (clone $signInMorning)->modify('+1 hour 30 minutes');
        $lateSignInMorning = (clone $signInMorning)->modify('+1 hour');
        $signOutMorning = new DateTime($day->sign_out_morning);
        $endsignOutMorning = (clone $signOutMorning)->modify('+1 hour 30 minutes');
        $lateSignOutMorning = (clone $signOutMorning)->modify('+1 hour');
        $signInAfternoon = new DateTime($day->sign_in_afternoon);
        $endsignInAfternoon = (clone $signInAfternoon)->modify('+1 hour 30 minutes');
        $lateSignInAfternoon = (clone $signInAfternoon)->modify('+1 hour');
        $signOutAfternoon = new DateTime($day->sign_out_afternoon);
        $endsignOutAfternoon = (clone $signOutAfternoon)->modify('+1 hour 30 minutes');
        $lateSignOutAfternon = (clone $signInMorning)->modify('+1 hour');
        $signTime = new DateTime($requestData['sign_time']);
        
        $message = "Already Signed In: " . $student->id_no;
        $late = false;

        if ($signTime > $signInMorning && $signTime < $endsignInMorning) {
            if($attendance->m_in->value === 1 || $attendance->m_in->value === 2){
                return redirect()->route('student.search')->with('warning', $message);  
            }  
            if ($signTime > $lateSignInMorning){
                $attendance->m_in = 2;
                $late = true;
            }
            else {
                $attendance->m_in = 1;
            }
        } else if ($signTime > $signOutMorning && $signTime < $endsignOutMorning) {
            if($attendance->m_out->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } 
            if ($signTime > $lateSignOutMorning){
                $attendance->m_out = 2;
                $late = true;
            }
            else {
                $attendance->m_out = 1;
            }
        } else if ($signTime > $signInAfternoon && $signTime < $endsignInAfternoon) {
            if($attendance->af_in->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } 
            if ($signTime > $lateSignInAfternoon){
                $attendance->af_in = 2;
                $late = true;
            }
            else {
                $attendance->af_in = 1;
            }
        } else if ($signTime > $signOutAfternoon && $signTime < $endsignOutAfternoon) {
            if($attendance->af_out->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } 
            if ($signTime > $lateSignOutAfternon){
                $attendance->af_out = 2;
                $late = true;
            }
            else {
                $attendance->af_out = 1;
            }
        } else {
            $message = "No Sign In/Out Scheduled";
            return redirect()->route('student.search')->with('error', $message);  
        }

        $attendance->save();

        if($late == true) {
            $message = "LATE: " . $student->id_no;
            return redirect()->route('student.search')->with('late', $message);
        }

        $message = "ID No: " . $student->id_no;

        return redirect()->route('student.search')->with('success', $message);        
    }

    public function dashboard(Request $request)
    {
        // Get the selected event and day IDs from the request
        $selectedEventId = $request->input('event');
        $selectedDayId = $request->input('day');

        // Set other variables to null
        $mInLabels = null;
        $dataForMIn = null;
        $afOutLabels = null;
        $dataForAfOut = null;
        $scatterLabels = null;
        $scatterValues = null;
    
        // Retrieve data for the charts with filters
        $yearLevelLabels = ['1st', '2nd', '3rd', '4th'];
        $dataByYearLevel = [];
    
        if ($selectedEventId && $selectedDayId) {
            $yearLevelData = $this->getYearLevelDataCounts($selectedEventId, $selectedDayId);
            $dataByYearLevel = array_values($yearLevelData);
        }
        
        if ($selectedEventId && $selectedDayId) {
            $scatterData = $this->getScatterChartData($selectedEventId, $selectedDayId);
            
            // Extract label and data arrays
            $scatterLabels = array_column($scatterData, 'label');
            $scatterValues = array_column($scatterData, 'count');
        }
    
        // Fetch all events and days
        $events = Event::get();
        $days = Day::get();

        // Prepare days grouped by event for JavaScript
        $daysByEvent = $days->groupBy('event_id')->map(function ($days) {
            return $days->map(function ($day) {
                return [
                    'id' => $day->id,
                    'day_number' => $day->day_number,
                    'event' => [
                        'name' => $day->event->name,
                    ],
                ];
            });
        });

        return view('dashboard', compact('yearLevelLabels', 'dataByYearLevel', 'scatterLabels', 'scatterValues', 'mInLabels', 'dataForMIn', 'afOutLabels', 'dataForAfOut', 'selectedEventId', 'selectedDayId', 'events', 'days', 'daysByEvent'));
    }
    
    
    protected function getYearLevelDataCounts($eventId, $dayId)
    {
        $yearLevels = ['1', '2', '3', '4'];
        $counts = [];
    
        foreach ($yearLevels as $yearLevel) {
            $query = "
            SELECT COUNT(*) AS row_count
            FROM attendances
            INNER JOIN days ON attendances.day_id = days.id
            INNER JOIN events ON days.event_id = events.id
            INNER JOIN students ON attendances.student_id = students.id
            WHERE 
                events.id = ? AND 
                days.id = ? AND 
                (attendances.m_in = 1 OR attendances.m_in = 0) AND
                students.year_level = ?
            ";
    
            $result = DB::select($query, [$eventId, $dayId, $yearLevel]);
            $counts[$yearLevel] = $result[0]->row_count;
        }
    
        return $counts;
    }

    protected function getScatterChartData($eventId, $dayId)
    {
        $query = "
        SELECT LEFT(students.id_no, 4) AS label, COUNT(*) AS count
        FROM attendances
        INNER JOIN days ON attendances.day_id = days.id
        INNER JOIN events ON days.event_id = events.id
        INNER JOIN students ON attendances.student_id = students.id
        WHERE 
            events.id = ? AND 
            days.id = ? AND 
            (attendances.m_in = 1 OR attendances.m_in = 0)
        GROUP BY label
        ORDER BY label
        LIMIT 10
        ";
    
        $result = DB::select($query, [$eventId, $dayId]);
        return $result;
    }
    
    
    
    
}
