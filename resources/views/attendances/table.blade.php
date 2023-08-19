@foreach ($attendances as $attendance)
    <tr>
        <td>{{ $attendance->student->id_no }}</td>
        <td>{{ $attendance->student->full_name }}</td>
        <td>{{ $attendance->student->major }}</td>
        <td>{{ $attendance->student->year_level }}</td>
        <td>{{ $attendance->day->event->name }}</td>
        <td>{{ $attendance->day->day_number }}</td>
        <td>{{ $attendance->m_in ? 'Present' : 'Absent' }}</td>
        <td>{{ $attendance->m_out ? 'Present' : 'Absent' }}</td>
        <td>{{ $attendance->af_in ? 'Present' : 'Absent' }}</td>
        <td>{{ $attendance->af_out ? 'Present' : 'Absent' }}</td>
        <!-- Display other attendance data here -->
    </tr>
@endforeach
