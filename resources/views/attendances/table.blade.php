@foreach ($attendances as $attendance)
    <tr>
        <td>{{ $attendance->student->id_no }}</td>
        <td>{{ $attendance->student->full_name }}</td>
        <td>{{ $attendance->student->major }}</td>
        <td>{{ $attendance->student->year_level }}</td>
        <td>{{ $attendance->day->event->name }}</td>
        <td>{{ $attendance->day->day_number }}</td>
        <td>{{ $attendance->m_in->name}}</td>
        <td>{{ $attendance->m_out->name}}</td>
        <td>{{ $attendance->af_in->name}}</td>
        <td>{{ $attendance->af_out->name}}</td>
        <!-- Display other attendance data here -->
    </tr>
@endforeach
