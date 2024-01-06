@extends('layouts.user_type.guest')

@section('content')

<div class="main-container">
    <div class="logo">SAMs</div>
    <div class="title">Student Attendance Monitoring System</div>
    <img class="circle-image" src="{{ asset('assets/img/1.png') }}" alt="Circle Image">
    <div class="council-title">CCS - Executive Council 2023</div>
    <img class="small-image" src="{{ asset('assets/img/smalllogo.png') }}" alt="Small Image">
    <div class="login-box">
        <div class="form-container">
            <div class="content">
                <form role="form" method="POST" action="/session">
                    @csrf
                    <label for="email">Email</label>
                    <div class="form-field">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email" value=""
                            aria-label="Email" aria-describedby="email-addon">
                        @error('email')
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <label for="password">Password</label>
                    <div class="form-field">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                            value="" aria-label="Password" aria-describedby="password-addon">
                        @error('password')
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>

                    <button type="submit" class="sign-in-btn">Sign In</button>

                    <div class="forgot-password">
                        <span>Forgot your password?</span>
                        <a href="/login/forgot-password">Reset it here</a>
                    </div>

                    <div class="sign-up">
                        <span>Don't have an account?</span>
                        <a href="register">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection