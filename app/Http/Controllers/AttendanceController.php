<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Day;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                             ->orderBy('updated_at', 'desc')
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

        $signInMorning = Carbon::parse($day->sign_in_morning);
        $endsignInMorning = $signInMorning->addHour();
        $signOutMorning = Carbon::parse($day->sign_out_morning);
        $endsignOutMorning = $signOutMorning->addHour();
        $signInAfternoon = Carbon::parse($day->sign_in_afternoon);
        $endsignInAfternoon = $signInAfternoon->addHour();
        $signOutAfternoon = Carbon::parse($day->sign_out_afternoon);
        $endsignOutAfternoon  = $signOutAfternoon->addHour();
        $signTime = Carbon::parse($requestData['sign_time']);
        
        if($signTime > $signInMorning && $signTime < $endsignInMorning){
            $attendance->m_in = 1;
        } else if($signTime > $day->signOutMorning && $signTime < $endsignOutMorning){
            $attendance->m_out = 1;
        } else if($signTime > $day->signInAfternoon && $signTime < $endsignInAfternoon){
            $attendance->af_in = 1;
        } else if($signTime > $day->signOutAfternoon && $signTime < $endsignOutAfternoon){
            $attendance->af_out = 1;
        }

        $attendance->save();

        return redirect()->route('student.search')->with('success', 'Attendance updated successfully');
    }

}