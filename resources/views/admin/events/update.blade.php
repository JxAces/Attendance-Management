@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <h1>Edit Event and Days</h1>
        
        <form action="{{ route('events.update', ['event' => $event->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name">Event Name</label>
                <input type="text" class="form-control" name="name" value="{{ old('name', $event->name) }}">
            </div>
            
            <!-- Loop through the days associated with the event -->
            @foreach ($days as $day)
                <div class="day">
                    <h2>Day {{ $loop->index + 1 }}</h2>
                    <div class="form-group">
                        <label for="sign_in_morning">Sign In (Morning)</label>
                        <input type="time" class="form-control" name="days[{{ $day->id }}][sign_in_morning]" value="{{ $day->sign_in_morning ? $day->sign_in_morning->format('H:i') : '' }}">
                    </div>
                    <div class="form-group">
                        <label for="sign_out_morning">Sign Out (Morning)</label>
                        <input type="time" class="form-control" name="days[{{ $day->id }}][sign_out_morning]" value="{{ $day->sign_out_morning ? $day->sign_out_morning->format('H:i') : '' }}">
                    </div>
                </div>
            @endforeach
            
            <button type="submit" class="btn btn-primary">Update Event and Days</button>
        </form>
    </div>
@endsection
	