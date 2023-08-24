@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1 id="clock" class="text-center mt-4 h3"></h1>
    <p id="eventDetails" class="text-center mt-2 h5"></p>
    <div class="text-center">
        <video id="qr-scanner" style="max-width: 100%; height: auto;"></video>
    </div>
    <h2 class="mt-4 h4">Search Students by ID</h2>
    <div class="d-flex align-items-center"> <!-- Added a flex container for alignment -->
        <div class="form-group flex-grow-1"> <!-- Adjusted the width to grow with the flex container -->
            <label for="studentSelect" class="h6">Search and Select Student:</label>
            <select id="studentSelect" class="form-control"></select>
        </div>
        <button id="openModalButton" class="btn btn-primary ml-2">New Student</button> <!-- Added the button -->
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
            </div>
            <div class="modal-body">
                <form id="addStudentForm" action="{{ route('save_student') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="studentName">Student ID No</label>
                        <input type="text" class="form-control" id="studentName" name="studentName" required>
                        <input type="text" name="event_name_new" placeholder="Event Name" required hidden>
                        <input type="text" name="day_number_new" placeholder="Day Number" required hidden>
                        <input type="text" name="sign_time_new" placeholder="Sign Time" required hidden>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitStudentButton">Save Student</button>
            </div>
        </div>
    </div>
</div>

<div class="custom-alert-container">
    @if(session('success'))
    <div class="custom-alert alert alert-success">
        {{ session('success') }}
    </div>
    @endif
</div>
<div class="custom-alert-container">
    @if(session('warning'))
    <div class="custom-alert alert alert-warning">
        {{ session('warning') }}
    </div>
    @endif
</div>
<div class="custom-alert-container">
    @if(session('error'))
    <div class="custom-alert alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
</div>


<div class="container mt-4">
    <h2 class="h4">Student Details</h2>
    <div id="studentDetails"></div>
    <form action="/update-attendance" method="POST">
        @csrf
        <input type="text" name="student_id" placeholder="Student ID" required hidden>
        <input type="text" name="event_name" placeholder="Event Name" required hidden>
        <input type="text" name="day_number" placeholder="Day Number" required hidden>
        <input type="text" name="sign_time" placeholder="Sign Time" required hidden>
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

        $('#openModalButton').on('click', function () {
            $('#addStudentModal').modal('show'); // Open the modal
        });
        $('#closeModal').on('click', function () {
            $('#addStudentModal').modal('hide'); // Open the modal
        });

        $('#submitStudentButton').on('click', function () {
            const eventDetails = $('#eventDetails').text();
                        if (eventDetails !== "No Event"){
                            const eventParts = eventDetails.split(" || ");
                            const eventName = eventParts[0].split(": ")[1];
                            const dayNumber = eventParts[1].split(": ")[1];
                            $('input[name="event_name_new"]').val(eventName);
                            $('input[name="day_number_new"]').val(dayNumber);
                            $('input[name="sign_time_new"]').val(getCurrentTimeFormatted());
                }
            $('#addStudentForm').submit();
        });

            let scanner = new Instascan.Scanner({ video: document.getElementById('qr-scanner') });
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                }
            }).catch(function (error) {
                console.error(error);
            });
            scanner.addListener('scan', function (content) {
                studentSelect[0].selectize.setValue(content);
                studentSelect[0].selectize.search(content);
                // Assuming the QR code content is the student's ID
                handleAttendance(content);
            });

        function handleAttendance(studentId) {
            // Fetch student details and attendance information using the scanned student ID
            $.ajax({
                url: '/student/' + studentId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data) { // Ensure data is not null or undefined
                        $('#studentDetails').html(`
                            <h3 class="h5">${data.full_name}</h3>
                            <p class="h6">ID: ${data.id_no}</p>
                            <p class="h6">Year Level: ${data.year_level}</p>
                            <p class="h6">Major: ${data.major}</p>
                        `);  
                        
                        const eventDetails = $('#eventDetails').text();
                        if (eventDetails !== "No Event"){
                            const eventParts = eventDetails.split(" || ");
                            const eventName = eventParts[0].split(": ")[1];
                            const dayNumber = eventParts[1].split(": ")[1];
                            $('input[name="event_name"]').val(eventName);
                            $('input[name="day_number"]').val(dayNumber);
                            $('input[name="sign_time"]').val(getCurrentTimeFormatted());
                             $('input[name="student_id"]').val(studentId);
                        }
                        $('form').submit();
                    } else {
                        $('#studentDetails').html('<p class="h6">Student details not found.</p>');
                        $('#signInButton').prop('disabled', true);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid QR Code!',
                        text: 'Student details not found',
                        showConfirmButton: false,
                        timer: 2000 // Adjust the timer as needed
                        });
                    $('#studentDetails').html('<p class="h6">Student details not found.</p>');
                    $('#signInButton').prop('disabled', true);
                },
            });
        }

        window.scrollTo(0, document.body.scrollHeight);


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
            const options = { timeZone: 'Asia/Manila' };
            const currentTime = new Date().toLocaleTimeString('en-US', options);
            const currentHours = new Date().getHours();
            const currentMinutes = new Date().getMinutes();
            const currentDate = new Date();
            currentDate.setHours(currentDate.getHours() + 8); // Adjust for Philippines timezone

            eventDays.forEach(eventDay => {
                const signInMorning = eventDay.sign_in_morning || '00:00:00';
                const signOutMorning = eventDay.sign_out_morning || '00:00:00';
                const signInAfternoon = eventDay.sign_in_afternoon || '00:00:00';
                const signOutAfternoon = eventDay.sign_out_afternoon || '00:00:00';
                const eventDate = eventDay.date ? eventDay.date.substr(0, 10) : null;

                const localEventDate = new Date(eventDate);
                localEventDate.setHours(localEventDate.getHours() + 8); // Adjust for Philippines timezone

                if (localEventDate.toISOString().substr(0, 10) === currentDate.toISOString().substr(0, 10)) {
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
<style>
/* Add custom styles for the alert container and the alert itself */
.custom-alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

.custom-alert {
    padding: 10px 20px;
    border-radius: 5px;
    background-color: #2ecc71; /* Green background color */
    color: #ffffff; /* White text color */
    box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}
</style>