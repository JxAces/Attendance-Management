@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1 class="text-center">Attendances</h1>

    <!-- Filter Form -->
    <form action="{{ route('attendances.index') }}" method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="event_id" class="form-label">Event</label>
                <select name="event_id" class="form-control" id="event_id">
                    <option value="">Select Event</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="day_number" class="form-label">Day Number</label>
                <input type="number" name="day_number" class="form-control" id="day_number" value="{{ request('day_number') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" class="form-control" id="search" value="{{ request('search') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="year_level" class="form-label">Year Level</label>
                <input type="number" name="year_level" class="form-control" id="year_level" value="{{ request('year_level') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            <a href="#" id="exportButton" class="btn btn-success">Export to Excel</a>
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
    $(document).ready(function() {
        $('#event_id, #day_number, #search').on('change keyup', function() {
            updateTable();
        });

        $('#exportButton').on('click', function() {
            exportData();
        });

        function updateTable() {
            var eventId = $('#event_id').val();
            var dayNumber = $('#day_number').val();
            var searchValue = $('#search').val();

            $.ajax({
                url: '{{ route('attendances.index') }}',
                type: 'GET',
                data: {
                    event_id: eventId,
                    day_number: dayNumber,
                    search: searchValue
                },
                success: function(response) {
                    $('#attendanceTableBody').html(response);
                },
                error: function() {
                    alert('An error occurred while fetching data.');
                }
            });
        }

        function exportData() {
            var eventId = $('#event_id').val();
            var dayNumber = $('#day_number').val();
            var yearLevel = $('#year_level').val();
            var searchValue = $('#search').val();

            var exportUrl = '{{ route('attendances.export-data') }}' +
                            '?event_id=' + eventId +
                            '&day_number=' + dayNumber +
                            '&year_level=' + yearLevel +
                            '&search=' + searchValue;

            window.location.href = exportUrl;
        }
    });
</script>
@endsection
