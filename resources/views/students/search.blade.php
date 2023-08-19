@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <h2>Search Students by ID</h2>
        <div class="form-group">
            <label for="studentSelect">Search and Select Student:</label>
            <select id="studentSelect" class="form-control"></select>
        </div>
    </div>

    <div class="container mt-4">
        <h2>Student Details</h2>
        <div id="studentDetails">
            <!-- Student details will be displayed here -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const studentSelect = $('#studentSelect').selectize({
                valueField: 'id_no',
                labelField: 'composite_label',
                searchField: ['id_no'],
                options: [],
                create: false,
                load: function(query, callback) {
                    if (!query.length) return callback();
                    $.ajax({
                        url: '/search',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            q: query
                        },
                        success: function(data) {
                            // Create a composite label using both id_no and full_name
                            data.forEach(function(student) {
                                student.composite_label = student.id_no + ' - ' + student.full_name;
                            });
                            callback(data);
                        },
                        error: function() {
                            callback();
                        }
                    });
                },
                onChange: function(value) {
                    if (!value) return;
                    // Fetch and display student details
                    $.ajax({
                        url: '/student/' + value,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#studentDetails').html(`
                                <h3>${data.full_name}</h3>
                                <p>ID: ${data.id_no}</p>
                                <p>Year Level: ${data.year_level}</p>
                                <p>Major: ${data.major}</p>
                                <!-- Add more details as needed -->
                            `);
                        },
                        error: function() {
                            $('#studentDetails').html('<p>Student details not found.</p>');
                        }
                    });
                }
            });
        });
    </script>
@endsection
