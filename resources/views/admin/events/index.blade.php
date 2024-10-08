@extends('layouts.user_type.auth')

@section('content')

<div class="container mb-5"> 
    <h1 class="events">Manage Events</h1>
</div>

<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 ">
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="allevents">All Events</div>
                        </div>
                        @if(auth()->check() && auth()->user()->admin)
                        <a href="#" class="btn bg-gradient-infos mt-3" id="newEventButton" type="button">Add an Event</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Events
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Days
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $event->name }}</td>
                                        <td class="text-center">{{ $event->days }}</td>
                                        <td class="text-center">
                                            @if (auth()->check() && auth()->user()->admin)
                                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-success btn-sm">Edit</a>
                                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-event-button">Delete</button>
                                                </form>
                                            @else
                                                <a href="{{ url('/attendances') }}" class="btn btn-success btn-sm">Get Attendance</a>
                                                @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for creating an event -->
    <div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="createEventModalLabel">Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <!-- Form for creating an event -->
                    <form action="{{ route('events.store') }}" method="POST">
                        @csrf
                        <!-- Event form fields go here -->
                        <div class="mb-3">
                            <label for="event-name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event-name" name="name" required>
                        </div>
                        <!-- Add more event form fields as needed -->
                        <div class="mb-3">
                            <label for="event-days" class="form-label">Days</label>
                            <input type="text" class="form-control" id="event-days" name="days" required>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-info">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for displaying event days -->
        <div class="modal fade" id="eventDaysModal" tabindex="-1" aria-labelledby="eventDaysModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventDaysModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                      <th scope="col">Day Number</th>
                                    <!-- Add more day fields as needed -->
                                </tr>
                            </thead>
                            <tbody id="eventDaysTableBody">
                            @if ($days)
                            @foreach ($days as $day)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $day->day_number }}</td>
                                    <!-- Add more day fields as needed -->
                                </tr>
                            @endforeach
                        @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
  var j = $.noConflict(); // Assign a new symbol (e.g., 'j') to jQuery
  j(document).ready(function() {
    j('#newEventButton').click(function() {
      j('#createEventModal').modal('show');
    });

    // Check if a success message is present in the session
    @if(session('success'))
      // Display SweetAlert notification
      j(function() {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: '{{ session('success') }}',
          showConfirmButton: false,
          timer: 3000 // Adjust the timer as needed
        });
      });
    @endif


    // JavaScript/jQuery code to handle the button click event
    j('a.btn-info').click(function(e) {
      e.preventDefault(); // Prevent the default behavior of the anchor tag

      var eventId = j(this).attr('href').split('/').pop();

      // Make an AJAX request to fetch the event days' data
      j.ajax({
        url: '/events/' + eventId,
        type: 'GET',
        success: function(data) {
          var event = data.event;
          var days = data.days;

          // Populate the event name in the modal header
          j('#eventDaysModalLabel').text(event.name);

          // Populate the days' data in the modal body
          var tableBody = j('#eventDaysTableBody');
          tableBody.empty(); // Clear existing data

          for (var i = 0; i < days.length; i++) {
            var day = days[i];
            var row = '<tr>' +
              '<th scope="row">' + (i + 1) + '</th>' +
              '<td>' + day.day_number + '</td>' +
              // Add more day fields as needed
              '</tr>';

            tableBody.append(row);
          }

          // Show the event days modal
          j('#eventDaysModal').modal('show');
        },
        error: function() {
          alert('Error occurred while fetching event days');
        }
      });
    });

            // Handle delete event button click
        j('.delete-event-button').click(function() {
            var form = j(this).closest('form'); // Get the parent form
            var eventId = form.attr('data-event-id'); // Get the event ID from data attribute
            var eventName = form.attr('data-event-name'); // Get the event name from data attribute

            Swal.fire({
                title: 'Confirm Deletion',
                text: `Are you sure you want to delete the event? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form for deletion
                }
            });
        });
  });
</script>


@endsection
