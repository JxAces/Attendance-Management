@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Edit Event and Days</h1>

    <div id="loadingScreen" class="loading-screen">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <form action="{{ route('events.update', ['event' => $event->id]) }}" method="POST" id="editEventForm"   >
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $event->name) }}">
        </div>

        <!-- Loop through the days associated with the event, two days per row -->
        @for ($i = 0; $i < count($days); $i += 2)
        <div class="row">
            <div class="col-md-6">
                @php
                $day1 = $days[$i];
                $day2 = isset($days[$i + 1]) ? $days[$i + 1] : null;
                @endphp

                <div class="day">
                    <h2>Day {{ $i + 1 }}</h2>
                    <div class="form-group">
                        <label for="date_{{ $day1->id }}">Date</label>
                        <input type="date" class="form-control" name="days[{{ $day1->id }}][date]"
                        value="{{ $day1->date ? date('Y-m-d', strtotime($day1->date)) : '' }}">
                    </div>
                    @foreach (['sign_in_morning', 'sign_out_morning', 'sign_in_afternoon', 'sign_out_afternoon'] as $field)
                    <div class="form-group">
                        <label for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                        <input type="time" class="form-control" name="days[{{ $day1->id }}][{{ $field }}]"
                            value="{{ $day1->$field ?: '' }}" {{ $day1->$field ? '' : 'disabled' }}>
                        <label>
                            <input type="checkbox" class="disable-group"
                                data-target="days[{{ $day1->id }}][{{ $field }}]"
                                {{ $day1->$field ? '' : 'checked' }}> No {{ ucfirst(str_replace('_', ' ', $field)) }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            @if ($day2)
            <div class="col-md-6">
                <div class="day">
                    <h2>Day {{ $i + 2 }}</h2>
                    <div class="form-group">
                        <label for="date_{{ $day2->id }}">Date</label>
                        <input type="date" class="form-control" name="days[{{ $day2->id }}][date]"
                        value="{{ $day2->date ? date('Y-m-d', strtotime($day2->date)) : '' }}">
                    </div>
                    @foreach (['sign_in_morning', 'sign_out_morning', 'sign_in_afternoon', 'sign_out_afternoon'] as $field)
                    <div class="form-group">
                        <label for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                        <input type="time" class="form-control" name="days[{{ $day2->id }}][{{ $field }}]"
                            value="{{ $day2->$field ?: '' }}" {{ $day2->$field ? '' : 'disabled' }}>
                        <label>
                            <input type="checkbox" class="disable-group"
                                data-target="days[{{ $day2->id }}][{{ $field }}]"
                                {{ $day2->$field ? '' : 'checked' }}> No {{ ucfirst(str_replace('_', ' ', $field)) }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endfor

        <button type="submit" class="btn btn-primary">Update Event and Days</button>
    </form>
</div>

<script>
    // JavaScript to handle checkbox interactions and show/hide loading screen
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.disable-group');
        const loadingScreen = document.getElementById('loadingScreen');
        const editEventForm = document.getElementById('editEventForm'); // Define the form

        checkboxes.forEach(checkbox => {
            const targetName = checkbox.getAttribute('data-target');
            const targetInput = document.querySelector(`[name="${targetName}"]`);
            if (targetInput) {
                checkbox.addEventListener('change', function () {
                    targetInput.disabled = this.checked;
                    if (this.checked) {
                        targetInput.value = ''; // Clear input value
                    }
                });
            }
        });

        editEventForm.addEventListener('submit', function () {
            // Show loading screen on form submission
            loadingScreen.style.display = 'flex';
        });
    });
</script>

<style>
    /* Add styles for the loading screen */
    .loading-screen {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
</style>
@endsection
