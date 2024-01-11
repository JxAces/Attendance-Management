@extends('layouts.user_type.guest')

@section('content')
<div class="containers">
    <div class="main-container">
        <div class="circle-column">
            <img class="circle-image" src="{{ asset('assets/img/1.png') }}" alt="Circle Image">
        </div>
        <div class="content-container">
            <div class="sams">
                <div class="logo">SAMs</div>
                <div class="title">Student Attendance Monitoring System</div>
            </div>
            <div class="login-box">
                <div class="form-container">
                    <div class="content">
                        <form role="form" method="POST" action="/session">
                            @csrf
                            <label for="email">Email</label>
                            <div class="form-field">
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                                    value="" aria-label="Email" aria-describedby="email-addon">
                                @error('email')
                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <label for="password">Password</label>
                            <div class="form-field">
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Password" value="" aria-label="Password"
                                    aria-describedby="password-addon">
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
    </div>
    <div class="ccs2023">
        <div class="council-titles">CCS - Executive Council 2023</div>
        <img class="small-image" src="{{ asset('assets/img/smalllogo.png') }}" alt="Small Image">
    </div>
</div>
@endsection

<style>
    .containers {
        width: 100%;
        height: 100%;
        background: white;
    }

    .main-container {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .content-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-bottom: 50px;
    }

    .content-container,
    .circle-column{
        width: 725px;
    }

    .ccs2023 {
        position: absolute;
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        margin-top: auto;
    }

    .council-titles {
        color: #726E6E;
        font-size: 21.63px;
        font-family: 'Open Sans', sans-serif;
        font-weight: 700;
        word-wrap: break-word;
    }

    .circle-column {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .circle-image {
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        border-radius: 301px;
    }

    .logo {
        font-family: 'Peace', sans-serif;
        font-weight: 400;
        color: #153f53;
        font-size: 130px;
        margin-bottom: -45px;
    }

    .title {
        color: #726E6E;
        font-weight: 400;
        font-size: 21px;
    }

    .sams {
        flex-direction: column;
    }

    .form-container {
        width: 100%;
        max-width: 500px;
        background: white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        padding: 20px;
        box-sizing: border-box;
        height: 450px;
    }

    .content {
        justify-content: center;
        padding-top: 50px;
    }

    .form-field {
        margin-bottom: 15px;
    }

    .form-check.form-switch {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .sign-in-btn {
        background-color: #2B738B !important;
        color: white !important;
        border: none !important;
        padding: 10px 195px !important;
        border-radius: 5px !important;
        cursor: pointer !important;
        font-size: 13.3333px;
    }

    .forgot-password,
    .sign-up {
        text-align: center;
        margin-top: 15px;
        font-family: "Open Sans";
    }

    .forgot-password a,
    .sign-up a {
        color: #336862;
        font-weight: bold;
        text-decoration: none;
        font-family: "Open Sans";
    }

    .forgot-password a:hover,
    .sign-up a:hover {
        text-decoration: underline;
    }

    .form-container .sams {
        position: flex;
        flex-direction: row;
    }
</style>