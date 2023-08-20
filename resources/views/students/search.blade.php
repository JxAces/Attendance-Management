@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <!-- Display the clock in smaller font and 12-hour format -->
    <h1 id="clock" class="text-center mt-4 h3"></h1>

    <!-- Display the event details if there's a matching event day -->
    <p id="eventDetails" class="text-center mt-2 h5"></p>

    <h2 class="mt-4 h4">Search Students by ID</h2>
    <div class="form-group">
        <label for="studentSelect" class="h6">Search and Select Student:</label>
        <select id="studentSelect" class="form-control"></select>
    </div>
</div>

<div class="container mt-4">
    <h2 class="h4">Student Details</h2>
    <div id="studentDetails">
        <!-- Student details will be displayed here -->
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <form action="/update-attendance" method="POST">
        @csrf
        <input type="text" name="student_id" placeholder="Student ID" required hidden>
        <input type="text" name="event_name" placeholder="Event Name" required hidden>
        <input type="text" name="day_number" placeholder="Day Number" required hidden>
        <input type="text" name="sign_time" placeholder="Sign Time" required hidden>
        <!-- Add the student sign-in button -->
        <button id="signInButton" type="submit" class="btn btn-primary mt-3" disabled>Sign In Student</button>
    </form>
</div>

<script>
    $(document).ready(function () {
        const studentSelect = $('#studentSelect').selectize({
            valueField: 'id_no',
            labelField: 'composite_label',
            searchField: ['id_no'],
            options: [],
            create: false,
            load: function (query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: '/search',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        q: query
                    },
                    success: function (data) {
                        data.forEach(function (student) {
                            student.composite_label = student.id_no + ' - ' + student.full_name;
                        });
                        callback(data);
                    },
                    error: function () {
                        callback();
                    }
                });
            },
            onChange: function (value) {
                if (!value) {
                    $('#signInButton').prop('disabled', true);
                    return;
                }
                $.ajax({
                    url: '/student/' + value,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#studentDetails').html(`
                            <h3 class="h5">${data.full_name}</h3>
                            <p class="h6">ID: ${data.id_no}</p>
                            <p class="h6">Year Level: ${data.year_level}</p>
                            <p class="h6">Major: ${data.major}</p>
                        `);
                        $('#signInButton').prop('disabled', false);
                    },
                    error: function () {
                        $('#studentDetails').html('<p class="h6">Student details not found.</p>');
                        $('#signInButton').prop('disabled', true);
                    },
                });
            },
        });

        function updateClock() {
            const now = new Date();
            const hours = now.getHours() % 12 || 12;
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const ampm = now.getHours() < 12 ? 'AM' : 'PM';
            const timeString = `${hours}:${minutes}:${seconds} ${ampm}`;
            $('#clock').text(timeString).addClass('h2');
        }

        updateClock();
        setInterval(updateClock, 1000);

        let isSignIn = false;
        let isSignOut = false;

        function updateButtonLabel() {
            if (isSignIn) {
                $('#signInButton').text('Sign In Student');
            } else if (isSignOut) {
                $('#signInButton').text('Sign Out Student');
            } else {
                $('#signInButton').text('Student Sign-In/Out');
            }
        }

        $('#signInButton').on('click', function () {
            const selectedValue = studentSelect[0].selectize.getValue();
            const eventDetails = $('#eventDetails').text();

            if (selectedValue && eventDetails !== "No Event") {
                const eventParts = eventDetails.split(" || ");
                const eventName = eventParts[0].split(": ")[1];
                const dayNumber = eventParts[1].split(": ")[1];

                // Update hidden input fields
                $('input[name="student_id"]').val(selectedValue);
                $('input[name="event_name"]').val(eventName);
                $('input[name="day_number"]').val(dayNumber);
                $('input[name="sign_time"]').val(getCurrentTimeFormatted());

                // Continue with form submission
            }
        });


        // Check if the current date matches any event day and display the event
        const eventDays = {!! json_encode($days) !!}; // Replace with the actual array of event days
        const events = {!! json_encode($events) !!};

        function checkEventTime() {
        const currentTime = new Date();
        const currentHours = currentTime.getHours();
        const currentMinutes = currentTime.getMinutes();
        const currentDateString = currentTime.toISOString().substr(0, 10);

        eventDays.forEach(eventDay => {
            const signInMorning = eventDay.sign_in_morning || '00:00:00';
            const signOutMorning = eventDay.sign_out_morning || '00:00:00';
            const signInAfternoon = eventDay.sign_in_afternoon || '00:00:00';
            const signOutAfternoon = eventDay.sign_out_afternoon || '00:00:00';
            const eventDate = eventDay.date ? eventDay.date.substr(0, 10) : null;
            console.log('For Loop Events');

            if (eventDate === currentDateString) {
                console.log('We have Event');
                const dayNumber = eventDay.day_number;
                const eventName = events.find(event => event.id === eventDay.event_id).name;
                let eventDetails = `Event: ${eventName} || Day: ${dayNumber} || `;

                let isSignIn = false;
                let isSignOut = false;

                if (isWithinHourAfter(currentHours, currentMinutes, signInMorning)) {
                    eventDetails += "Morning Sign In";
                    isSignIn = true;
                    isSignOut = false;
                } else if (isWithinHourAfter(currentHours, currentMinutes, signOutMorning)) {
                    eventDetails += "Morning Sign Out";
                    isSignIn = false;
                    isSignOut = true;
                } else if (isWithinHourAfter(currentHours, currentMinutes, signInAfternoon)) {
                    eventDetails += "Afternoon Sign In";
                    isSignIn = true;
                    isSignOut = false;
                } else if (isWithinHourAfter(currentHours, currentMinutes, signOutAfternoon)) {
                    eventDetails += "Afternoon Sign Out";
                    isSignIn = false;
                    isSignOut = true;
                } else {
                    eventDetails += "No Event";
                    isSignIn = false;
                    isSignOut = false;
                }

                $('#eventDetails').text(eventDetails);
                updateButtonLabel();
            }
        });
}


        function isWithinHourAfter(hours, minutes, timeString) {
            const [targetHours, targetMinutes] = timeString.split(':');
            const parsedTargetHours = parseInt(targetHours);
            const targetTotalMinutes = parsedTargetHours * 60 + parseInt(targetMinutes);
            const currentTotalMinutes = hours * 60 + minutes;

            return (
                currentTotalMinutes >= targetTotalMinutes &&
                currentTotalMinutes <= targetTotalMinutes + 60
            );
        }

        function format12HourTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            const parsedHours = parseInt(hours);
            const ampm = parsedHours < 12 ? 'AM' : 'PM';
            const formattedHours = parsedHours % 12 || 12;
            return `${formattedHours}:${minutes} ${ampm}`;
        }

        function getCurrentTimeFormatted() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const seconds = now.getSeconds();
            return `${hours}:${minutes}:${seconds}`;
        }


        checkEventTime(); // Initial check
        setInterval(checkEventTime, 1000); // Check every minute

    });
</script>
@endsection