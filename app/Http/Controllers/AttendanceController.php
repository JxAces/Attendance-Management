<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Day;
use Maatwebsite\Excel\Facades\Excel;

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

}
