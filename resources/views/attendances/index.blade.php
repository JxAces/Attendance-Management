@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1 class="attendancelist">Attendance List</h1>

    <!-- Filter Form -->
    <form action="{{ route('attendances.index') }}" method="GET" class="mb-3">
        <div class="row1">
            <div class="col-md-6 mb-3">
                <label for="event_id" class="form-label">Event</label>
                <select name="event_id" class="form-control" id="event_id">
                    <option value="">Select Event</option>
                    @foreach ($events as $event)
                    <option value="{{ $event->id }}" {{ request('event_id')==$event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="day_number" class="form-label">Day Number</label>
                <input type="number" name="day_number" class="form-control" id="day_number"
                    value="{{ request('day_number') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="year_level" class="form-label">Year Level</label>
                <input type="number" name="year_level" class="form-control" id="year_level"
                    value="{{ request('year_level') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="search" class="form-label">ID Number</label>
                <input type="text" name="search" class="form-control" id="search" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label for="course" class="form-label">Course</label>
                <input type="text" name="course" class="form-control" id="course"
                    value="{{ request('course') }}">
            </div>
                <div class="col-md-3 mb-3">
                    <a href="#" id="exportButton" class="btn btn-success">Export to Excel</a>
                </div>
        </div>
    </form>
    <!-- Table displaying attendances -->
    <div class="table-responsive">
        <table class="table text-center">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Event</th>
                    <th>Day</th>
                    <th>Sign In Morning</th>
                    <th>Sign Out Morning</th>
                    <th>Sign In Afternoon</th>
                    <th>Sign Out Afternoon</th>
                    <!-- Add more table headers as needed -->
                </tr>
            </thead>
            <tbody id="attendanceTableBody">
                @include('attendances.table', ['attendances' => $attendances])
            </tbody>
        </table>
    </div>
</div>

    <!-- Include the jQuery library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            var originalData = @json($attendances);

            $('#event_id, #day_number, #search, #year_level, #course').on('change keyup', function () {
                updateTable();
            });

            $('#exportButton').on('click', function () {
                exportData();
            });

            function updateTable() {
                var eventId = $('#event_id').val();
                var dayNumber = $('#day_number').val();
                var yearLevel = $('#year_level').val();
                var course = $('#course').val();
                var searchValue = $('#search').val();

                var filteredData = originalData.filter(function (attendance) {
                    return (
                        (eventId === '' || attendance.day.event_id == eventId) &&
                        (dayNumber === '' || attendance.day.day_number == dayNumber) &&
                        (searchValue === '' || attendance.student.id_no == searchValue) &&
                        (yearLevel === '' || attendance.year_level == yearLevel) &&
                        (course === '' || attendance.major == course)
                    );
                });

                for (var i = 0; i < 5; i++) {
                    console.log(originalData[i]);
                }

                updateTableBody(filteredData);
            }

            function updateTableBody(data) {
                $('#attendanceTableBody').empty();

                data.forEach(function (attendance) {
                    var rowHtml = '<tr>';
                    rowHtml += '<td>' + attendance.student.id_no + '</td>';
                    rowHtml += '<td>' + attendance.student.full_name + '</td>';
                    rowHtml += '<td>' + attendance.student.major + '</td>';
                    rowHtml += '<td>' + attendance.student.year_level + '</td>';
                    rowHtml += '<td>' + attendance.day.event.name + '</td>';
                    rowHtml += '<td>' + attendance.day.day_number + '</td>';
                    rowHtml += '<td>' + mapAttendanceLevel(attendance.m_in) + '</td>';
                    rowHtml += '<td>' + mapAttendanceLevel(attendance.m_out) + '</td>';
                    rowHtml += '<td>' + mapAttendanceLevel(attendance.af_in) + '</td>';
                    rowHtml += '<td>' + mapAttendanceLevel(attendance.af_out) + '</td>';
                    // Add more cells as needed
                    rowHtml += '</tr>';

                    $('#attendanceTableBody').append(rowHtml);
                });
            }

            
            function mapAttendanceLevel(attendanceLevel) {
                switch (attendanceLevel) {
                    case 0:
                        return 'Free';
                    case 1:
                        return 'Present';
                    case 2:
                        return 'Late';
                    case 4:
                        return 'Excuse';
                    case 5:
                        return 'Absent';
                    case 6:
                        return 'EC';
                    default:
                        return '';
                }
            }

            function exportData() {
                var eventId = $('#event_id').val();
                var dayNumber = $('#day_number').val();
                var yearLevel = $('#year_level').val();
                var course = $('#course').val();
                var searchValue = $('#search').val();

                var exportUrl = '{{ route('attendances.export-data') }}?' +
                    'event_id=' + eventId +
                    '&day_number=' + dayNumber +
                    '&search=' + searchValue +
                    '&year_level=' + yearLevel +
                    '&major=' + course;

                window.location.href = exportUrl;
            }

        });
    </script>
@endsection