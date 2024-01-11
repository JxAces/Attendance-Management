<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Attendance::query();

        if ($this->request->filled('event_id')) {
            $query->whereHas('day.event', function ($query) {
                $query->where('id', $this->request->input('event_id'));
            });
        }
        
        if ($this->request->filled('day_number')) {
            $query->whereHas('day', function ($query) {
                $query->where('day_number', $this->request->input('day_number'));
            });
        }
        
        if ($this->request->filled('search')) {
            $query->whereHas('student', function ($query) {
                $query->where('id_no', $this->request->input('search'));
            });
        }
        
        if ($this->request->filled('year_level')) {
            $query->whereHas('student', function ($query) {
                $query->where('year_level', $this->request->input('year_level'));
            });
        }
        
        if ($this->request->filled('major')) {
            $query->whereHas('student', function ($query) {
                $query->where('major', $this->request->input('major'));
            });
        }
        
        $attendances = $query->with(['day.event', 'student'])
            ->orderBy('updated_at', 'desc')
            ->get();        

        // Transform raw data into desired format
        $formattedData = [];
        foreach ($attendances as $attendance) {
            $formattedData[] = [
                $attendance->student->id_no, // Student ID
                $attendance->student->full_name, // Student
                $attendance->student->major, // Course
                $attendance->student->year_level, // Year
                $attendance->day->event->name, // Event
                $attendance->day->day_number, // Day
                $attendance->m_in->name,
                $attendance->m_out->name,
                $attendance->af_in->name,
                $attendance->af_out->name,
                // Add more data fields as needed
            ];
        }

        return collect($formattedData);
    }

    public function headings(): array
    {
        return [
            'Student ID',
            'Student',
            'Course',
            'Year',
            'Event',
            'Day',
            'Sign In Morning',
            'Sign Out Morning',
            'Sign In Afternoon',
            'Sign Out Afternoon',
            // Add more headings as needed
        ];
    }
}
