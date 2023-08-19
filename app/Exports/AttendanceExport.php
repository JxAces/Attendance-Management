<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Attendance;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Attendance::query();

        // Apply filters based on the request
        if ($this->request->filled('event_id')) {
            // Apply event filter
            $query->whereHas('day.event', function ($query) {
                $query->where('id', $this->request->input('event_id'));
            });
        }

        if ($this->request->filled('day_number')) {
            // Apply day number filter
            $query->whereHas('day', function ($query) {
                $query->where('day_number', $this->request->input('day_number'));
            });
        }

        if ($this->request->filled('search')) {
            // Apply search filter
            $searchTerm = $this->request->input('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('student_id', 'LIKE', "%$searchTerm%")
                    ->orWhereHas('student', function ($query) use ($searchTerm) {
                        $query->where('id_no', 'LIKE', "%$searchTerm%")
                            ->orWhere('full_name', 'LIKE', "%$searchTerm%");
                    });
            });
        }

        // Add more filters based on your needs

        $attendances = $query->with(['day.event', 'student'])
                             ->orderBy('updated_at', 'desc')
                             ->get();

        // Transform raw data into desired format
        $formattedData = [];
        foreach ($attendances as $attendance) {
            $formattedData[] = [
                $attendance->student->id_no,     // Student ID
                $attendance->student->full_name,      // Student
                $attendance->student->major,         // Course
                $attendance->student->year_level,           // Year
                $attendance->day->event->name,        // Event
                $attendance->day->day_number,         // Day
                $attendance->m_in == 1 ? 'Present' : 'Absent',   // Sign In Morning
                $attendance->m_out == 1 ? 'Present' : 'Absent',  // Sign Out Morning
                $attendance->af_in == 1 ? 'Present' : 'Absent',  // Sign In Afternoon
                $attendance->af_out == 1 ? 'Present' : 'Absent', // Sign Out Afternoon
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
