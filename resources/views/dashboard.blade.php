@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1 class="text-center">Dashboard</h1>
    <div class="row">
        <!-- Filters Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dashboard') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="event">Select Event:</label>
                                <select name="event" id="event" class="form-control">
                                    <option value="">Events</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->id }}"
                                            {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                                            {{ $event->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="day">Select Day:</label>
                                <select name="day" id="day" class="form-control" {{ empty($selectedEventId) ? 'disabled' : '' }}>
                                    <option value="">Days</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Charts Cards -->
        <div class="col-md-6">
            <div class="card mt-4">
                <div class="card-body ">
                    <canvas id="lineChartYearLevel"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-4">
                <div class="card-body">
                    <canvas id="scatterChartIdNo"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-4">
                <div class="card-body">
                    <canvas id="barChartMIn"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mt-4">
                <div class="card-body">
                    <canvas id="barChartAfOut"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('dashboard')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script>

    document.getElementById('event').addEventListener('change', function () {
            const daySelect = document.getElementById('day');
            const selectedEventId = this.value;
            
            // Clear existing options
            while (daySelect.options.length > 1) {
                daySelect.remove(daySelect.options.length - 1);
            }
            
            // Populate day options based on selected event
            if (selectedEventId) {
                const daysForEvent = {!! json_encode($daysByEvent) !!}[selectedEventId];
                for (const day of daysForEvent) {
                    const option = document.createElement('option');
                    option.value = day.id;
                    option.text = `Day ${day.day_number} (Event: ${day.event.name})`;
                    daySelect.add(option);
                }
            }
            
            // Enable/disable day select
            daySelect.disabled = !selectedEventId;
            // Clear selected day
            daySelect.selectedIndex = 0;
        });

    var ctxYearLevel = document.getElementById('lineChartYearLevel').getContext('2d');
    var lineChartYearLevel = new Chart(ctxYearLevel, {
        type: 'line',
        data: {
            labels: {!! json_encode($yearLevelLabels) !!},
            datasets: [{
                label: 'Attendance based on Year',
                data: {!! json_encode($dataByYearLevel) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }]
        },
        options: {
            // Add any options you need for your chart
        }
    });


    var ctxIdNo = document.getElementById('scatterChartIdNo').getContext('2d');
    var scatterChartIdNo = new Chart(ctxIdNo, {
        type: 'scatter',
        data: {
            labels: {!! json_encode($scatterLabels) !!},
            datasets: [{
                label: 'Attendance based on Student ID#',
                data: {!! json_encode($scatterValues) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                pointRadius: 5,
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'category',
                    title: {
                        display: true,
                        text: 'ID Number'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Present'
                    }
                }
            }
        }
    });



    // var ctxBarMIn = document.getElementById('barChartMIn').getContext('2d');
    // var barChartMIn = new Chart(ctxBarMIn, {
    //     type: 'bar',
    //     data: {
    //         labels: @json($mInLabels),
    //         datasets: [{
    //             label: 'Sign in Morning',
    //             data: @json($dataForMIn),
    //             backgroundColor: 'rgba(75, 192, 192, 0.5)',
    //             borderColor: 'rgba(75, 192, 192, 1)',
    //             borderWidth: 1
    //         }]
    //     },
    //     options: {
    //         // Add any options you need for your chart
    //     }
    // });

    // var ctxBarAfOut = document.getElementById('barChartAfOut').getContext('2d');
    // var barChartAfOut = new Chart(ctxBarAfOut, {
    //     type: 'bar',
    //     data: {
    //         labels: @json($afOutLabels),
    //         datasets: [{
    //             label: 'Sign out Afternoon',
    //             data: @json($dataForAfOut),
    //             backgroundColor: 'rgba(75, 192, 192, 0.5)',
    //             borderColor: 'rgba(75, 192, 192, 1)',
    //             borderWidth: 1
    //         }]
    //     },
    //     options: {
    //         // Add any options you need for your chart
    //     }
    // });
</script>
@endpush
