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
                                    <option value="">All Events</option>
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
                                <select name="day" id="day" class="form-control">
                                    <option value="">All Days</option>
                                    @foreach ($days as $day)
                                        <option value="{{ $day->id }}"
                                            {{ $selectedDayId == $day->id ? 'selected' : '' }}>
                                            {{ $day->date }} (Event: {{ $day->event->name }})
                                        </option>
                                    @endforeach
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
            <div class="card">
                <div class="card-body">
                    <canvas id="lineChartYearLevel"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <canvas id="scatterChartIdNo"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <canvas id="barChartMIn"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
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
    var ctxYearLevel = document.getElementById('lineChartYearLevel').getContext('2d');
    var lineChartYearLevel = new Chart(ctxYearLevel, {
        type: 'line',
        data: {
            labels: @json($yearLevelLabels),
            datasets: [{
                label: 'Attendance based on Year',
                data: @json(array_values($dataByYearLevel)),
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
        type: 'scatter', // Use 'scatter' chart type for scatter plot
        data: {
            datasets: [{
                label: 'Attendance based on ID# by Year',
                data: @json($scatterData),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Add background color for data points
                pointRadius: 5, // Set the radius of each data point
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'category', // Use 'category' scale for x-axis
                    title: {
                        display: true,
                        text: 'Year ID'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Count of Year ID'
                    }
                }
            }
        }
    });

    var ctxBarMIn = document.getElementById('barChartMIn').getContext('2d');
    var barChartMIn = new Chart(ctxBarMIn, {
        type: 'bar',
        data: {
            labels: @json($mInLabels),
            datasets: [{
                label: 'Sign in Morning',
                data: @json($dataForMIn),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            // Add any options you need for your chart
        }
    });

    var ctxBarAfOut = document.getElementById('barChartAfOut').getContext('2d');
    var barChartAfOut = new Chart(ctxBarAfOut, {
        type: 'bar',
        data: {
            labels: @json($afOutLabels),
            datasets: [{
                label: 'Sign out Afternoon',
                data: @json($dataForAfOut),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            // Add any options you need for your chart
        }
    });
</script>
@endpush
